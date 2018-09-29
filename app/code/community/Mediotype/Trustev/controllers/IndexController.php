<?php

/**
 * Class Mediotype_Trustev_IndexController
 */

class Mediotype_Trustev_IndexController extends Mage_Core_Controller_Front_Action {
    /**
     * Redirect users who somehow get here
     */
	public function indexAction() {
		$this->_redirect('no-route');
	}

    /**
     * set a trustev session id if one doesn't already exist
     */
	public function setSessionIdAction(){
        //first, check if session id is already set
        $customerSession = Mage::getSingleton('customer/session');
        if($customerSession->getData('trustevV2SessionId')) {
            return;
        }
        //no session id, so get parameter and set it
		$trustevV2SessionId = (string) $this->getRequest()->getParam('trustevV2SessionId');
        if($trustevV2SessionId) {
            $customerSession->setData('trustevV2SessionId',$trustevV2SessionId);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('status'=>true)));
        }
	}

    /**
     * add status to log table
     * @throws Exception
     */
    public function webHookAction()
    {
        $params = $this->getRequest()->getParams();
        $log = Mage::getModel('mediotype_status/webhook_log');/** @var $log Mediotype_Trustev_Model_Webhook_Log */
        $log->setData('case_id',$params['CaseId']);
        $log->setData('status_id',$params['Status']);
        $log->save();
    }

    /**
     * testing function TODO delete before release
     */
    public function testAction()
    {
        echo '<p>starting test action</p>';
        $caseModel  = Mage::getModel('mediotype_trustev/request_case')->load(1);
        //$caseModel->buildCaseFromOrder(6);

        //$json       = $caseModel->getJsonRequest();
        //Mage::log($json,null,'trustev.log');

        //echo "<p>$json</p>";

        $newJson    = $caseModel->getDecision();

        echo "<p>received response</p>";
        echo "<p>".print_r($newJson)."</p>";
    }
	
}