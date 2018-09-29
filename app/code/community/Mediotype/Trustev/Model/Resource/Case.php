<?php
/**
 * Class Mediotype_Trustev_Model_Resource_Case
 *
 */
class Mediotype_Trustev_Model_Resource_Case extends Mediotype_Core_Model_Resource_Abstract{

    public function _construct(){
        $this->_init('mediotype_trustev/case', 'id');
    }

//    protected function _afterLoad(){
//
//        //$this->decryptJsonResponds();
//
//        return parent::_afterLoad();
//    }

    protected function decryptJsonResponds(){
        $fieldData = $this->getData('field_name');
        $this->setData('field_name', json_decode($fieldData));
    }

}
