<?php
class Customer_FavorController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$action = $this->_request->getActionName();
		$isLoged = Factory::isLoged();
		if (!$isLoged) {
			$this->redirect(Factory::getBaseUrl());
		}
	}
	
	public function mobileAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	public function purchaseAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	public function addAction()
	{
		$id = $this->_request->getParam('id');
		$type = $this->_request->getParam('type');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $type && $customer_id) {
			Factory::getLogic('customer')->addFavor($customer_id, $type, $id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setSession('message/favor', Factory::getSession('message/favor') + 1);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	public function removeAction()
	{
		$id = $this->_request->getParam('id');
		$type = $this->_request->getParam('type');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $type && $customer_id) {
			Factory::getLogic('customer')->removeFavor($customer_id, $type, $id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setSession('message/favor', Factory::getSession('message/favor') - 1);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}