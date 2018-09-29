<?php
class Mediotype_Trustev_Model_Request_Status extends Mediotype_Trustev_Model_Request_Abstract {

    protected $_caseId = '';

    /**
     * @param string $caseId
     */
    public function setCaseId($caseId)
    {
        $this->_caseId = $caseId;
    }

    /**
     * @return string
     */
    public function getCaseId()
    {
        return $this->_caseId;
    }

    /**
     * @return string
     */
    public function getRequestUrl(){
        return "https://app.trustev.com/api/v2.0/case/" . $this->getCaseId() . "/status";
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAuth(){
        return "X-Authorization:" . $this->getDataHelper()->getUsername() . " " . $this->getToken();
    }

    /**
     * @param string $trustevStatusId
     * @param int $statusId
     * @param string $comment
     * @param null $timestamp
     */
    public function setStatusUpdateData($statusId,$comment='',$timestamp = null){
        $timestamp = ($timestamp == null)?$this->getDataHelper()->createTimestamp():$timestamp;
        $this->setData('Status', $statusId);
        $this->setData('Comment', $comment);
        $this->setData('Timestamp', $timestamp);
    }

    /**
     * @param $response
     * @return $this
     * @throws Exception
     */
    public function processResponse($response){
        $decodedResponse = json_decode($response,true);
        //check for test mode, output data
        if($this->getDataHelper()->getIsTestMode()) {
            Mage::log($response, null, 'trustev.log');
        }
        //check for an error message
        if(isset($decodedResponse['Message'])) {
            throw new Exception($decodedResponse['Message']);
        }
        return $this;
    }
}