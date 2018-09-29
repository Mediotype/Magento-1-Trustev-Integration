<?php
/**
 * Class Mediotype_Trustev_Model_Resource_Sales_Order_Status
 *
 */
class Mediotype_Trustev_Model_Resource_Sales_Order_Status extends Mediotype_Core_Model_Resource_Abstract{

    public function _construct(){
        $this->_init('sales/order_status', 'status');
    }

}
