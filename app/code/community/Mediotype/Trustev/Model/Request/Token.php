<?php
class Mediotype_Trustev_Model_Request_Token extends Mediotype_Trustev_Model_Request_Abstract {
    /**
     * Config paths for using throughout the code
     */
    const TRUSTEV_API_TOKEN_CACHE_KEY  = 'trustev_api_token';

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return "https://app.trustev.com/api/v2.0/token";
    }

    /**
     * @return bool
     */
    public function getAuth()
    {
        return false;
    }

    /**
     * response will be in the form of
     * {
     * "ExpireAt": "2015-11-17T16:48:19.001493Z",
     * "APIToken": "dcfdcbba-a15e-4a5f-9388-d2db642b314f",
     * "CredentialType": 1
     * }
     *
     * if a Message exists, an error occurred
     * attach message to exception throw
     *
     * @param $response
     * @throws Exception
     */
    public function processResponse($response)
    {
        $decodedResponse = json_decode($response,true);
        //check for test mode, output data
        if($this->getDataHelper()->getIsTestMode()) {
            Mage::log($decodedResponse, null, 'trustev.log');
        }
        //check for an error message
        if(isset($decodedResponse['Message'])) {
            throw new Exception($decodedResponse['Message']);
        }
        //cache token string, it expires after 30 minutes
        $cache = Mage::app()->getCache();
        $cache->save($decodedResponse['APIToken'], $this::TRUSTEV_API_TOKEN_CACHE_KEY, array("trustev_cache"), 60*30);
        $this->setData('token',$decodedResponse['APIToken']);
    }
}