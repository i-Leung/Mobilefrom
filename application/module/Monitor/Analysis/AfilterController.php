<?php
class Monitor_Analysis_AfilterController extends Project_Controller_Action_Admin
{
	/**
	 * 手机属性筛选
	 */
	public function mobileAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 求购属性筛选
	 */
	public function purchaseAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}
