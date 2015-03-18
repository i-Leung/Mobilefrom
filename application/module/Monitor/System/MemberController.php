<?php
class Monitor_System_MemberController extends Project_Controller_Action_Admin
{
	/**************************************************网站成员操作**************************************************/
	/**
	 * 网站成员列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 网站成员信息
	 */
	public function infoAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	
	/**
	 * 添加网站成员
	 */
	public function addAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 检测添加网站成员的用户名是否存在
	 */
	public function checkUsernameAction()
	{
		$username = $this->_request->getParam('username');
		if ($username) {
			$isExist = Factory::getLogic('customer')->isExist($username);
			if ($isExist) {
				echo json_encode(1);
				exit(0);
			}	
		}
		echo json_encode(0);
	}

	/**
	 * 网站成员授权操作
	 */
	public function authorizeAction()
	{
		$member = $this->_request->getParam('member');
		$author = 1;
		if ($member && $author) {
			$customer_id = Factory::getLogic('customer')->isExist($member['username']);
			if ($customer_id) {
				unset($member['username']);
				$member['customer_id'] = $customer_id;
				$member['authorized_by'] = $author;
				$member['authorized_at'] = time();
				$result = Factory::getLogic('system/member')->createMember($member);
				$msg = Factory::getMsg();
				if ($result && empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 修改网站成员信息操作
	 */
	public function modifyMemberAction()
	{
		$id = $this->_request->getParam('id');
		$member = $this->_request->getParam('member');
		$author = 1;
		if ($id && $member && $author) {
			$customer_id = Factory::getLogic('customer')->isExist($member['username']);
			if ($customer_id) {
				unset($member['username']);
				$member['customer_id'] = $customer_id;
				$member['authorized_by'] = $author;
				$member['authorized_at'] = time();
				$result = Factory::getLogic('system/member')->modifyMember($id, $member);
				$msg = Factory::getMsg();
				if ($result && empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 改变网站成员状态操作
	 */
	public function changeMemberStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$author = 1;
		if ($id && $author) {
			$member = array();
			$member['authorized_by'] = $author;
			$member['authorized_at'] = time();
			$member['status'] = $status;
			$result = Factory::getLogic('system/member')->modifyMember($id, $member);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 设置网站成员后台登录密码
	 * @param 成员ID $id
	 * @param 新密码 $pwd
	 */
	public function setPwdAction()
	{
		$id = $this->_request->getParam('id');
		$pwd = $this->_request->getParam('pwd');
		if ($id && $pwd) {
			$member = Factory::getView('system/member')->getMember($id);
			if ($member) {
				$logic = Factory::getLogic('system/member');
				$result = $logic->modifyMember($id, array('pwd' => $logic->encrypt($pwd, $member['authorized_at'])));
				$msg = Factory::getMsg();
				if ($result && empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 网站成员后台操作历史
	 */
	public function operationAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 网站成员设置后台登陆密码
	 */
	public function pwdAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 网站后台操作历史
	 */
	public function historyAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**************************************************网站成员群组操作**************************************************/
	/**
	 * 群组列表
	 */
	public function groupListAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 群组信息
	 */
	public function groupInfoAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 添加群组
	 */
	public function addGroupAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 添加群组操作
	 */
	public function ajaxAddGroupAction()
	{
		$label = $this->_request->getParam('label');
		$description = $this->_request->getParam('description');
		$resources = $this->_request->getParam('resources');
		if ($label && $resources) {
			$member = Factory::getLogic('system/member');
			if ($group_id = $member->addGroup($label, $description)) {
				$result = $member->addGroupResource($group_id, $resources);	
				$msg = Factory::getMsg();
				if (empty($msg) && $result) {
					echo json_encode(1);
					exit(0);
				}	
			}
		}
		echo json_encode(0);
	}

	/**
	 * 修改群组操作
	 */
	public function ajaxModifyGroupAction()
	{
		$id = $this->_request->getParam('id');
		$label = $this->_request->getParam('label');
		$description = $this->_request->getParam('description');
		$resources = $this->_request->getParam('resources');
		if ($id && $label && $description && $resources) {
			$member = Factory::getLogic('system/member');
			$group = $member->getGroup($id);
			if ($group['label'] != $label || $group['description'] != $description) {
				$member->modifyGroup($id, $label, $description);
			}
			if ($add = array_diff($resources, $group['resource'])) {
				$member->modifyGroupResource($id, $add, 'add');	
			}
			if ($delete = array_diff($group['resource'], $resources)) {
				$member->modifyGroupResource($id, $delete, 'delete');	
			}
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}	
		}
		echo json_encode(0);
	}

	/**
	 * 修改群组状态
	 */
	public function changeGroupStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/member')->changeGroupStatus($id, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
