<?php
/**
 * Class Mediotype_Trustev_Model_Case_Customer
 */
class Mediotype_Trustev_Model_Case_Customer extends Mage_Core_Model_Abstract{

    /**
     * @param $orderId
     * @param $timestamp
     * @return $this
     */
    public function setCustomerData($orderId,$timestamp)
    {
        //make sure order exists
        if($order = Mage::getModel('sales/order')->load($orderId))
        {
            /** @var $order Mage_Sales_Model_Order */
            $this->setData('FirstName',$order->getCustomerFirstname());
            $this->setData('LastName',$order->getCustomerLastname());
            $this->setData('Emails',$this->checkCustomerEmail($order));
            $this->setData('PhoneNumber',$order->getCustomer());
            $this->setData('Addresses',$this->createAddressData($order,$timestamp));
            if($customerId = $order->getCustomerId())
            {
                $this->setData('AccountNumber',$customerId);
            }
        }
        return $this;
    }

    public function checkCustomerEmail(Mage_Sales_Model_Order $order)
    {
        //check billing email against customer email if it exists
        $emailArray = array();
        $orderEmail = array(
            'EmailAddress' => $order->getCustomerEmail(),
            'IsDefault' => false
        );
        if($customer = $order->getCustomer())
        {
            /** @var $customer Mage_Customer_Model_Customer */
            if($orderEmail['EmailAddress'] == $customer->getEmail())
            {
                $orderEmail['IsDefault'] = true;
            } else {
                $emailArray[] = array(
                    'EmailAddress' => $customer->getEmail(),
                    'IsDefault' => true
                );
            }

        }
        $emailArray[]=$orderEmail;

        return $emailArray;

    }

    public function createAddressData(Mage_Sales_Model_Order $order,$timestamp)
    {
        $addressArray = array();
        //TODO if customer exists, give customer addresses. otherwise, order addresses
        /*if($customer = $order->getCustomer())
        {
            foreach($customer->getAddressCollection() as $address)
            {
                $addressArray[] = Mage::getModel('mediotype_trustev/case_address')->setAddressDataFromCustomer($address,$timestamp)->getData();
            }

        } else {*/

        foreach($order->getAddressesCollection() as $address)
        {
            $addressArray[] = Mage::getModel('mediotype_trustev/case_address')->setAddressDataFromOrder($address,$timestamp)->getData();
        }
        //}

        return $addressArray;
    }

}