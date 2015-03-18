<?php
class Space_MobileController extends Project_Controller_Action_Front
{
	/**
	 * 用户发布手机列表页
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 详细页TA还发布了内容
	 */
	public function otherAction()
	{
		$customer_id = $this->_request->getParam('cid', null);
		$mobile_id = $this->_request->getParam('mid', null);
		if (!is_null($customer_id) && !is_null($mobile_id)) {
			$mobile = Factory::getView('mobile/list')->loadCustomerOther($customer_id, $mobile_id);
			$msg = Factory::getMsg();
			if (empty($msg) && $mobile) {
				echo json_encode($mobile);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
