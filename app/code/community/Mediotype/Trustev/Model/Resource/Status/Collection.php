<?php
/**
 * Class Mediotype_Trustev_Model_Resource_Status_Collection
 *
 */
class Mediotype_Trustev_Model_Resource_Status_Collection extends Mediotype_Core_Model_Resource_Db_Collection_Abstract{

    public function _construct(){
        $this->_init('mediotype_trustev/status');
    }

}
