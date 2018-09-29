<?php
/**
 * Class Mediotype_Trustev_Model_Case_Payment
 */
class Mediotype_Trustev_Model_Case_Payment extends Mage_Core_Model_Abstract{

    const TRUSTEV_PAYMENT_TYPE_NONE = 0;
    const TRUSTEV_PAYMENT_TYPE_CREDITCARD = 1;
    const TRUSTEV_PAYMENT_TYPE_DEBITCARD = 2;
    const TRUSTEV_PAYMENT_TYPE_DIRECTDEBIT = 3;
    const TRUSTEV_PAYMENT_TYPE_PAYPAL = 4;
    const TRUSTEV_PAYMENT_TYPE_BITCOIN = 5;

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return $this
     */
    public function setPaymentData(Mage_Sales_Model_Order_Payment $payment)
    {
        //make sure payment exists
        if($payment->getId())
        {
            $this->setData('PaymentType',$this->setPaymentTypeFromMethod($payment));
            $this->setBINN($payment);
        }
        return $this;
    }

    public function setPaymentTypeFromMethod(Mage_Sales_Model_Order_Payment $payment)
    {
        //TODO: need better logic for determining type
        switch($payment->getData('method'))
        {
            case 'ccsave':
            case 'authorizenet':
                $type = $this::TRUSTEV_PAYMENT_TYPE_CREDITCARD;
                break;
            case 'paypal_standard':
            case 'paypal_direct':
            case 'paypal_express':
                $type = $this::TRUSTEV_PAYMENT_TYPE_PAYPAL;
                break;
            case 'checkmo':
            case 'banktransfer':
                $type = $this::TRUSTEV_PAYMENT_TYPE_DIRECTDEBIT;
                break;
            default:
                $type = ($this->checkForBIN($payment))? $this::TRUSTEV_PAYMENT_TYPE_CREDITCARD: $this::TRUSTEV_PAYMENT_TYPE_NONE;
        }
        return $type;
    }

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return string
     */
    public function checkForBIN(Mage_Sales_Model_Order_Payment $payment)
    {
        return ($binn = $payment->getData('cc_bin'))? $binn: '';
    }

    public function setBINN(Mage_Sales_Model_Order_Payment $payment)
    {
        if($binn = $this->checkForBIN($payment))
        {
            $this->setData('BINNumber',Mage::helper('core')->decrypt($binn));
        }
        else
        {
            $this->setData('BINNumber','N/A');
        }
    }
}