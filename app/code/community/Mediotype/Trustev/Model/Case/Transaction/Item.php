<?php
/**
 * Class Mediotype_Trustev_Model_Case_Transaction_Item
 */
class Mediotype_Trustev_Model_Case_Transaction_Item extends Mage_Core_Model_Abstract{

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return $this
     */
    public function setItemData(Mage_Sales_Model_Order_Item $item)
    {
        //make sure item exists
        if($item->getId())
        {
            $this->setData('Name',$item->getName());
            $this->setData('Quantity',(int)$item->getQtyOrdered());
            $this->setData('ItemValue',$item->getPrice());
        }
        return $this;
    }

}