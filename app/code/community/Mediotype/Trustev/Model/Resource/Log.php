<?php
/**
 * Class Mediotype_Trustev_Model_Resource_Log
 *
 */
class Mediotype_Trustev_Model_Resource_Log extends Mediotype_Core_Model_Resource_Abstract{

    public function _construct(){
        $this->_init('mediotype_trustev/webhook_log', 'id');
    }

}
