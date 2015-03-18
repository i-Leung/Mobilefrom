<?php
class Website_StoreController extends Project_Controller_Action_Front
{
	/**
	 * 商家介绍
	 */
	public function businessAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 用户接受
	 */
	public function customerAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 商家协议
	 */
	public function contractAction()
	{
		$this->loadLayout()->renderLayout();
	}
}
