<?php
/**
 * Class Mediotype_Trustev_Model_Resource_Status
 *
 */
class Mediotype_Trustev_Model_Resource_Status extends Mediotype_Core_Model_Resource_Abstract{

    public function _construct(){
        $this->_init('mediotype_trustev/status_type', 'id');
    }

}
