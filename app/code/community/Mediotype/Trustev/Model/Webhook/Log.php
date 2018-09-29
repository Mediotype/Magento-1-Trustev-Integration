<?php

/**
 * Class Mediotype_Trustev_Model_Webhook_Log
 *
 *
 */
class Mediotype_Trustev_Model_Webhook_Log extends Mage_Core_Model_Abstract{

    public function _construct(){
        $this->_setResourceModel('mediotype_trustev/log');
    }

}