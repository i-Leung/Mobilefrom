<?php
class Monitor_System_ResourceController extends Project_Controller_Action_Admin
{
	/****************************************资源组操作************************************/
	/**
	 * 资源组列表
	 */
	public function groupListAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 添加资源组
	 */
	public function addGroupAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * ajax添加资源组操作
	 */
	public function ajaxAddGroupAction()
	{
		$group = $this->_request->getParam('group');
		if ($group) {
			$result = Factory::getLogic('system/resource')->addGroup($group);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * ajax修改资源组操作
	 */
	public function ajaxModifyGroupAction()
	{
		$id = $this->_request->getParam('id');
		$group = $this->_request->getParam('group');
		if ($id && $group) {
			$result = Factory::getLogic('system/resource')->modifyGroup($id, $group);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * ajax修改资源组状态
	 */
	public function changeGroupStatusAction()
	{
		$id = $this->_request->getParam('id');
		$pid = $this->_request->getParam('pid');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/resource')->changeGroupStatus($id, $pid, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/****************************************资源操作************************************/
	/**
	 * 资源列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 资源操作历史
	 */
	public function operationAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 添加资源
	 */
	public function addItemAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}


	/**
	 * ajax添加资源操作
	 */
	public function ajaxAddItemAction()
	{
		$item = $this->_request->getParam('item');
		if ($item) {
			$result = Factory::getLogic('system/resource')->addItem($item);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * ajax修改资源操作
	 */
	public function ajaxModifyItemAction()
	{
		$id = $this->_request->getParam('id');
		$item = $this->_request->getParam('item');
		if ($id && $item) {
			$result = Factory::getLogic('system/resource')->modifyItem($id, $item);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * ajax修改资源状态
	 */
	public function changeItemStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/resource')->changeItemStatus($id, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
