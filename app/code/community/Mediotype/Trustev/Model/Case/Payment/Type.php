<?php

/**
 * Class Mediotype_Trustev_Model_Payment_Type
 *
 *
 */
class Mediotype_Trustev_Model_Payment_Type extends Mage_Core_Model_Abstract{

    public function _construct(){
        $this->_setResourceModel('mediotype_trustev/payment_type');
    }

}