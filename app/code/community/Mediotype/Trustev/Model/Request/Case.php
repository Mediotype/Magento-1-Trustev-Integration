<?php
class Mediotype_Trustev_Model_Request_Case extends Mediotype_Trustev_Model_Request_Abstract {

    const CASE_DECISION_RESULT_UNKNOWN = 0;
    const CASE_DECISION_RESULT_PASS = 1;
    const CASE_DECISION_RESULT_FLAG = 2;
    const CASE_DECISION_RESULT_FAIL = 3;

    public function _construct(){
        $this->_setResourceModel('mediotype_trustev/case');
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return "https://app.trustev.com/api/v2.0/case"; //todo move to default config in config.xml
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAuth()
    {
        return "X-Authorization:" . $this->getDataHelper()->getUsername() . " " . $this->getToken();
    }

    /**
     * @param $orderId
     */
    public function buildCaseFromOrder($orderId)
    {
        //check for a session id.  this must exist for the api call

        if($sessionId   = Mage::getSingleton('customer/session')->getData('trustevV2SessionId'))
        {
            $timestamp  = $this->getDataHelper()->createTimestamp();

            $txCase     = Mage::getModel('mediotype_trustev/case_transaction'); /** @var $txCase Mediotype_Trustev_Model_Case_Transaction */
            $txCase->setTransactionData($orderId,$timestamp);

            $custCase   = Mage::getModel('mediotype_trustev/case_customer');/** @var $custCase Mediotype_Trustev_Model_Case_Customer */
            $custCase->setCustomerData($orderId,$timestamp);

            $this->setData('SessionId',$sessionId);
            $this->setData('CaseNumber',$orderId);
            $this->setData('Transaction',$txCase->getData());
            $this->setData('Customer',$custCase->getData());
            $this->setData('Payments',$this->createPaymentData($orderId));
            $this->setData('Timestamp',$timestamp);
        }

    }

    public function createStatusData($timestamp)
    {
        $statusCase = Mage::getModel('mediotype_trustev/case_status');/** @var $statusCase Mediotype_Trustev_Model_Case_Status */
        $statusCase->newStatusData($timestamp);
        return array($statusCase->getData());
    }

    /**
     * @param $orderId
     * @return array
     */
    public function createPaymentData($orderId)
    {
        $paymentsArray = array();
        foreach(Mage::getModel('sales/order')->load($orderId)->getPaymentsCollection() as $payment)
        {
            $paymentsArray[] = Mage::getModel('mediotype_trustev/case_payment')->setPaymentData($payment)->getData();
        }

        return $paymentsArray;
    }

    /**
     * @param $orderId
     * @return array
     * TODO revisit this function
     */
    public function createStatusDataFromOrder($orderId)
    {
        $statusesArray = array();
        foreach(Mage::getModel('sales/order')->load($orderId)->getStatusHistoryCollection() as $status)
        {
            $statusesArray[] = Mage::getModel('mediotype_trustev/case_status')->setStatusData($status)->getData();
        }

        return $statusesArray;
    }

    public function processResponse($response)
    {
        $decodedResponse = json_decode($response,true);
        //check for test mode, output data
        if($this->getDataHelper()->getIsTestMode()) {
            Mage::log($response, null, 'trustev.log');
        }
        //check for an error message
        if(isset($decodedResponse['Message'])) {
            throw new Exception($decodedResponse['Message']);
        }
        $this->setData('case_id',$decodedResponse['Id']);
        $this->setData('order_id',$decodedResponse['CaseNumber']);
        $this->setData('response',$response);
        $this->save();
        return $this;
    }

    /**
     * @return Mediotype_Trustev_Model_Status_Type
     */
    public function getCurrentStatus()
    {
        $decodedResponse = $this->getDecodedResonse();
        $mostRecentStatus = end($decodedResponse['Statuses']);
        return Mage::getModel('mediotype_trustev/case_status_type')->load($mostRecentStatus['Status']);
    }

    /**
     * @param string $status
     * @param string $comment
     * @throws Exception
     */
    public function updateStatus($status,$comment = '')
    {
        $orderStatus = Mage::getModel('mediotype_trustev/sales_order_status')->load($status,'status');/** @var $orderStatus Mediotype_Trustev_Model_Sales_Order_Status */
        if(!$orderStatus->getData('trustev_status_id'))
        {
            //no trustev status
            return;
        }
        $trustevStatusType = Mage::getModel('mediotype_trustev/case_status_type')->load($orderStatus->getData('trustev_status_id'));/** @var $trustevStatusType Mediotype_Trustev_Model_Case_Status_Type */
        if(!$trustevStatusType->getId())
        {
            //this status doesn't exist
            return;
        }
        $requestStatus = Mage::getModel('mediotype_trustev/request_status');/** @var $requestStatus Mediotype_Trustev_Model_Request_Status */
        $requestStatus->setCaseId($this->getData('case_id'));
        $requestStatus->setStatusUpdateData($trustevStatusType->getAdjustedId(),$comment);
        $newStatus = json_decode($requestStatus->apiCall(),true);
        if($newStatus == false)
        {
            //api call failed
            return;
        }
        //add new status to response data
        $response = $this->getDecodedResonse();
        $response['Statuses'][] = $newStatus;
        $this->setData('response',json_encode($response));
        $this->save();
    }

    /**
     * @return Array
     */
    public function getDecodedResonse()
    {
        return json_decode($this->getData('response'),true);
    }

    /**
     * @return Array
     */
    public function getDecision()
    {
        //request a new decision if needed
        if($this->getData('decision') == null)
        {
            $decision = Mage::getModel('mediotype_trustev/request_decision');
            $decision->setTrustevCaseId($this->getData('case_id'));
            $this->setData('decision', $decision->apiCall());
            $this->save();
        }
        //get existing decision data and return it
        return json_decode($this->getData('decision'),true);
    }

    public function getDecisionResult()
    {
        $decisionArray = $this->getDecision();
        switch($decisionArray['Result'])
        {
            case $this::CASE_DECISION_RESULT_PASS:
                $result = 'Pass';
                break;
            case $this::CASE_DECISION_RESULT_FLAG:
                $result = 'Flag';
                break;
            case $this::CASE_DECISION_RESULT_FAIL:
                $result = 'Fail';
                break;
            case $this::CASE_DECISION_RESULT_UNKNOWN:
            default:
                $result = 'Unknown';
                break;
        }
        return $result;
    }

    public function getDecisionScore()
    {
        $decisionArray = $this->getDecision();
        return $decisionArray['Score'];
    }

    public function getDecisionConfidence()
    {
        $decisionArray = $this->getDecision();
        return $decisionArray['Confidence'];
    }

    public function getDecisionComment()
    {
        $decisionArray = $this->getDecision();
        return $decisionArray['Comments'];
    }

}