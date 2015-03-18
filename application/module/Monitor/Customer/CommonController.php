<?php
class Monitor_Customer_CommonController extends Project_Controller_Action_Admin
{
	/**
	 * 用户列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 基本用户信息
	 */
	public function baseAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 修改用户信息
	 */
	public function modifyInfoAction()
	{
		$customer_id = $this->_request->getParam('id', null);
		$info = $this->_request->getParam('info', null);
		if ($customer_id && $info) {
			Factory::getLogic('customer')->modify($customer_id, $info);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 密码设置
	 */
	public function pwdAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 密码重置操作
	 */
	public function resetPwdAction()
	{
		$customer_id = $this->_request->getParam('id', null);
		$pwd = $this->_request->getParam('newpwd', null);
		if ($customer_id && $pwd) {
			$customer = Factory::getLogic('customer');
			$customer->modify(
				$customer_id,
				array('pwd' => $customer->encryptPwd($pwd))
			);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
