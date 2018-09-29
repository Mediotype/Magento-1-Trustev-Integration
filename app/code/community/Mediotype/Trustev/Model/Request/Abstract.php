<?php

abstract class Mediotype_Trustev_Model_Request_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * @return string
     */
    public abstract function getRequestUrl();

    public abstract function processResponse($response);

    protected function getAuth()
    {
        return "X-Authorization:" . $this->getDataHelper()->getUsername() . " " . $this->getToken();
    }

    public function getJsonRequest(){
        return json_encode($this->getData());
    }

    public function getToken()
    {
        //get token from cache
        $cachedToken = Mage::app()->getCache()->load(Mediotype_Trustev_Model_Request_Token::TRUSTEV_API_TOKEN_CACHE_KEY);
        if ($cachedToken == false) {
            //if no token exists, get a new one
            $timestamp = $this->getDataHelper()->createTimestamp();
            $response = Mage::getModel('mediotype_trustev/request_token')
                ->setData(
                    array("UserName" => $this->getDataHelper()->getUsername(),
                        "Timestamp" => $timestamp,
                        "UserNameHash" => $this->getDataHelper()->createUserNameHash($timestamp),
                        "PasswordHash" => $this->getDataHelper()->createPasswordHash($timestamp))
                        )
                ->apiCall();
            $decodedResponse = json_decode($response,true);
            if($decodedResponse['APIToken']){
                return $decodedResponse['APIToken'];
            } else {
                throw new Exception('Unable to fetch token');
            }
        }
        //we have the token, return it
        return $cachedToken;

    }

    /**
     * @param $request
     * @return bool|array
     */
    public function apiCall()
    {
        $request = $this->getJsonRequest();
        try {
            //create the curl request
            $auth = $this->getAuth();
            $contentType = "Content-type: application/json";
            $curl = curl_init($this->getRequestUrl());
            if ($auth) {
                //we have a token
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => [
                        $contentType,
                        $auth
                    ]
                ]);
            } else {
                //we are requesting a token
                curl_setopt_array($curl, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => $request,
                    CURLOPT_HTTPHEADER => [
                        $contentType
                    ]
                ]);
            }

            $result = curl_exec($curl);
            curl_close($curl);
            $this->processResponse($result);
            return $result;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    public function getDataHelper()
    {
        return Mage::helper('mediotype_trustev');
    }


}