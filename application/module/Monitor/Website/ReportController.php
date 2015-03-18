<?php
class Monitor_Website_ReportController extends Project_Controller_Action_Admin
{
	/**
	 * 举报列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 修改举报信息状态
	 */
	public function statusAction()
	{
		$id = $this->_request->getParam('id', null);
		$status = $this->_request->getParam('status', null);
		if (!is_null($id) && !is_null($status)) {
			$result = Factory::getLogic('website')->modifyReport($id, array('status' => $status));
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
