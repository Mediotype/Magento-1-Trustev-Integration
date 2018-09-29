<?php
class Mediotype_Trustev_Model_Request_Decision extends Mediotype_Trustev_Model_Request_Abstract {

    protected $_trustevCaseId = '';

    /**
     * @param $string
     */
    public function setTrustevCaseId($string)
    {
        $this->_trustevCaseId = $string;
    }

    /**
     * @return string
     */
    public function getTrustevCaseId()
    {
        return $this->_trustevCaseId;
    }

    /**
     * @return string
     */
    public function getRequestUrl(){
        return "https://app.trustev.com/api/v2.0/decision/" . $this->getTrustevCaseId();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getAuth(){
        return "X-Authorization:" . $this->getDataHelper()->getUsername() . " " . $this->getToken();
    }

    /**
     * @param $request
     * @return bool|array
     */
    public function apiCall()
    {
        try {
            //create the curl request
            $auth = $this->getAuth();
            $contentType = "Content-type: application/json";
            $curl = curl_init($this->getRequestUrl());

            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    $contentType,
                    $auth
                ]
            ]);

            $result = curl_exec($curl);
            curl_close($curl);
            $this->processResponse($result);
            return $result;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    /**
     * @param $response
     * @return $this
     * @throws Exception
     */
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
        return $this;
    }
}