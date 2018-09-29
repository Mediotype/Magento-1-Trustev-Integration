<?php
/**
 * Class Mediotype_Trustev_Model_Case_Transaction
 */
class Mediotype_Trustev_Model_Case_Transaction extends Mage_Core_Model_Abstract{

    /**
     * @param $orderId
     * @param $timestamp
     * @return $this
     */
    public function setTransactionData($orderId,$timestamp)
    {
        //make sure order exists
        if($order = Mage::getModel('sales/order')->load($orderId))
        {
            /** @var $order Mage_Sales_Model_Order */
            $this->setData('TotalTransactionValue',$order->getTotalDue());
            $this->setData('Currency',$order->getOrderCurrencyCode());
            $this->setData('Timestamp',$timestamp);

            $addressData = $this->createAddressData($order->getAddressesCollection(),$timestamp);
            $this->setData('Addresses',$addressData);

            $this->setData('Items',$this->createItemsData($order->getItemsCollection()));
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Address_Collection $addresses
     * @param $timestamp
     * @return array
     */
    protected function createAddressData(Mage_Sales_Model_Resource_Order_Address_Collection $addresses,$timestamp)
    {
        //loop through all the addresses and add them
        $addressArray = array();
        foreach($addresses as $address)
        {
            $addressArray[] = Mage::getModel('mediotype_trustev/case_address')->setAddressDataFromOrder($address,$timestamp)->getData();
        }
        return $addressArray;
    }

    /**
     * @param Mage_Sales_Model_Resource_Order_Item_Collection $items
     * @return array
     */
    protected function createItemsData(Mage_Sales_Model_Resource_Order_Item_Collection $items)
    {
        //loop through transaction items and add them
        $tItemArray = array();
        foreach($items as $item)
        {
            $tItemArray[] = Mage::getModel('mediotype_trustev/case_transaction_item')->setItemData($item)->getData();
        }
        return $tItemArray;
    }
}