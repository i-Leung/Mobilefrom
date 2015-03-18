<?php
class Website_HelperController extends Project_Controller_Action_Front
{
	/**
	 * 手机交易知多D
	 */
	public function whatToBuyAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 手机交易防骗攻略
	 */
	public function howToBuyAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 清除手机个人信息
	 */
	public function cleanPrivateInfoAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 商家开店须知
	 */
	public function storeToKnowAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 商家服务介绍
	 */
	public function storeIntroductionAction()
	{
		$this->loadLayout()->renderLayout();
	}
}
