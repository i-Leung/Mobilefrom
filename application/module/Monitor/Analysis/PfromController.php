<?php
class Monitor_Analysis_PfromController extends Project_Controller_Action_Admin
{
	/**
	 * 整站流量导向
	 */
	public function siteAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 模块流量导向
	 */
	public function moduleAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 动作流量导向
	 */
	public function actionAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}
