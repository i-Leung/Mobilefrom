<?php
class Monitor_Customer_PersonalController extends Project_Controller_Action_Admin
{
	/**
	 * 个人用户列表
	 */
	public function listAction()
	{
		$this->loadLayout()->renderLayout();
	}
	
	/**
	 * 手机信息列表
	 */
	public function mobileAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 求购信息列表
	 */
	public function purchaseAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}
