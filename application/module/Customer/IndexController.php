<?php
class Customer_IndexController extends Project_Controller_Action_Front
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
	
	public function indexAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
}