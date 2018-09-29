<?php

/**
 * Class Mediotype_Trustev_Model_Case_Status_Type
 *
 *
 */
class Mediotype_Trustev_Model_Case_Status_Type extends Mage_Core_Model_Abstract{

    const NEW_CASE_STATUS_TYPE = 9;

    public function _construct(){
        $this->_setResourceModel('mediotype_trustev/status');
    }

    /**
     * @return $this
     */
    public function loadNewCaseType()
    {
        $this->load($this::NEW_CASE_STATUS_TYPE);
        return $this;
    }

    /**
     * @return Int
     * Auto incremented Ids in table are +1 of Trustev Ids
     * TODO fix table so this function isn't required
     */
    public function getAdjustedId()
    {
        return $this->getId() - 1;
    }

}