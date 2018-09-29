<?php
/**
 * Class Mediotype_Trustev_Model_Case_Status
 */
class Mediotype_Trustev_Model_Case_Status extends Mage_Core_Model_Abstract{

    /**
     * @param null $timestamp
     */
    public function newStatusData($timestamp){
        $newCaseType = Mage::getModel('mediotype_trustev/case_status_type')->loadNewCaseType();
        $this->setData('Status', $newCaseType->getAdjustedId());
        $this->setData('Comment', 'New order');
        $this->setData('Timestamp', $timestamp);
    }
}