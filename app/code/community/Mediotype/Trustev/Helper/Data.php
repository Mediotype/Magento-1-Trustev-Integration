<?php
/**
 * Class Mediotype_Trustev_Helper_Data
 * helper class for trustev module
 */
class Mediotype_Trustev_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const TRUSTEV_ENABLED  = 'trustev_config/general/enabled';

    /**
     * Check if extension is enabled
     * return Boolean
     */
    public function getEnabled()
    {
        return Mage::getStoreConfig($this::TRUSTEV_ENABLED);
    }

    /**
     * Check if extension is in test mode
     * return Boolean
     */
    public function getIsTestMode()
    {
        return Mage::getStoreConfig('trustev_config/general/test_mode');
    }

    /**
     * pull public key
     * return String
     */
    public function getPublicKey()
    {
        return Mage::getStoreConfig('trustev_config/keys/public_key');
    }

    /**
     * pull username
     * return String
     */
    public function getUsername()
    {
        return Mage::getStoreConfig('trustev_config/keys/username');
    }

    /**
     * pull secret key
     * return String
     */
    protected function getSecret()
    {
        return Mage::getStoreConfig('trustev_config/keys/secret');
    }

    /**
     * pull site password
     * return String
     */
    protected function getPassword()
    {
        return Mage::getStoreConfig('trustev_config/keys/password');
    }

    protected function get256Hash($string)
    {
        return hash('sha256',$string);
    }

    /**
     * Create a hash out of the username
     *
     * @param $timestamp
     * @return String
     */
    public function createUserNameHash($timestamp)
    {
        $secret = Mage::helper('core')->decrypt($this->getSecret());
        $username = $this->getUsername();
        $newHash = $this->get256Hash($timestamp . "." . $username);
        return $this->get256Hash($newHash . "." . $secret);
    }

    /**
     * Create a hash out of the password
     *
     * @param $timestamp
     * @return String
     */
    public function createPasswordHash($timestamp)
    {
        $secret = Mage::helper('core')->decrypt($this->getSecret());
        $password = Mage::helper('core')->decrypt($this->getPassword());
        $newHash = $this->Get256Hash($timestamp . "." . $password);
        return $this->Get256Hash($newHash . "." . $secret);
    }

    /**
     * Timestamp is in format of yyyy-MM-ddTHH:mm:ss.fffZ
     *
     * @return String
     */
    public function createTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s.000", time()) . "Z";
    }

}


