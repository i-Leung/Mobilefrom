<?php
class Monitor_System_ServiceController extends Project_Controller_Action_Admin
{
	/**
	 * 服务列表页面
	 */
	public function indexAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 添加服务页面
	 */
	public function addAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 提交服务信息
	 */
	public function submitServiceAction()
	{
		$service = $this->_request->getParam('service', null);
		if ($service) {
			$result = false;
			if ($id = $this->_request->getParam('id', null)) {
				$result = Factory::getLogic('system/service')->modifyService($id, $service);
			} else {
				$result = Factory::getLogic('system/service')->createService($service);
			}
			if ($result) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取指定服务信息
	 */
	public function getAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			if ($service = Factory::getView('system/service')->getServiceInfo($id)) {
				echo json_encode($service);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
