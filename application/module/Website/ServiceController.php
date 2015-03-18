<?php
class Website_ServiceController extends Project_Controller_Action_Front
{
	/**
	 * 获取服务信息
	 */
	public function getDetailAction()
	{
		$sid = $this->_request->getParam('sid', null);
		$customer = $this->_request->getParam('uid', null);
		if ($sid && $customer) {
			if ($result = Factory::getView('customer/info')->getService($sid, $customer)) {
				global $CStatic;
				$result['contact']['region'] = $CStatic['region'][$result['contact']['region']];
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
