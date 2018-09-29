<?php
/**
 * Class Mediotype_Trustev_Model_Case_Address
 */
class Mediotype_Trustev_Model_Case_Address extends Mage_Core_Model_Abstract{

    const TRUSTEV_ADDRESS_TYPE_STANDARD = 0;
    const TRUSTEV_ADDRESS_TYPE_BILLING = 1;
    const TRUSTEV_ADDRESS_TYPE_DELIVERY = 2;

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @param $timestamp
     * @return $this
     */
    public function setAddressDataFromOrder(Mage_Sales_Model_Order_Address $address,$timestamp)
    {
        //make sure address exists
        if($address->getId())
        {
            /** @var $address Mage_Sales_Model_Order_Address */
            $this->setData('FirstName',$address->getFirstname());
            $this->setData('LastName',$address->getLastname());
            $this->setData('Address1',$address->getStreet1());
            $this->setData('Address2',$address->getStreet2());
            $this->setData('City',$address->getCity());
            $this->setData('State',$address->getRegion());
            $this->setData('PostalCode',$address->getPostcode());
            $this->setData('Type',$this->getTrustevAddressType($address->getAddressType()));
            $this->setData('CountryCode',Mage::getModel('directory/country')->load($address->getCountryId())->getIso2Code());
            $this->setData('Timestamp',$timestamp);
            $this->setData('IsDefault',$this->checkIsDefault($address));
        }
        return $this;
    }

    /**
     * @param Mage_Customer_Model_Address $address
     * @param $timestamp
     * @return $this
     */
    public function setAddressDataFromCustomer(Mage_Customer_Model_Address $address,$timestamp)
    {
        //TODO finish this function for the Mage_Customer_Model_Address
        //make sure address exists
        if($address->getId())
        {
            /** @var $address Mage_Customer_Model_Address */
            $this->setData('FirstName',$address->getFirstname());
            $this->setData('LastName',$address->getLastname());
            $this->setData('Address1',$address->getStreet1());
            $this->setData('Address2',$address->getStreet2());
            $this->setData('City',$address->getCity());
            $this->setData('State',$address->getRegion());
            $this->setData('PostalCode',$address->getPostcode());
            $this->setData('Type',$this->getTrustevAddressType($address->getAddressType()));
            $this->setData('CountryCode',Mage::getModel('directory/country')->load($address->getCountryId())->getIso2Code());
            $this->setData('Timestamp',$timestamp);
            $this->setData('IsDefault',$this->checkIsDefault($address));
        }
        return $this;
    }

    /**
     * @param String $type
     * @return int
     */
    public function getTrustevAddressType($type)
    {
        $trustevType = $this::TRUSTEV_ADDRESS_TYPE_STANDARD;
        switch($type) {
            case 'billing':
                $trustevType = $this::TRUSTEV_ADDRESS_TYPE_BILLING;
                break;
            case 'shipping':
                $trustevType = $this::TRUSTEV_ADDRESS_TYPE_DELIVERY;
                break;
            default:
                break;
        }
        return $trustevType;
    }

    /**
     * @param Mage_Sales_Model_Order_Address $address
     * @return bool
     */
    public function checkIsDefault(Mage_Sales_Model_Order_Address $address)
    {
        $order = $address->getOrder();
        if(!$order->getCustomerIsGuest())
        {
            //get customer default addresses and compare to this address
            foreach (Mage::getModel('customer/customer')->load($order->getCustomerId())->getPrimaryAddressIds() as $id) {
                if ($address->getId() == $id) {
                    return true;
                }
            }
        }
        return false;
    }
}