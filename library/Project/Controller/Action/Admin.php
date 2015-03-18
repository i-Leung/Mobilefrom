<?php
class Project_Controller_Action_Admin extends Project_Controller_Action_Abstract
{
	/**
	 * 模块名称
	 */
	private $_module = null;

	/**
	 * 控制器名称
	 */
	private $_controller = null;

	/**
	 * 动作名称
	 */
	private $_action = null;

	/**
	 * 后台初始操作
	 */
	public function init()
	{
		parent::init();
		$this->_module = $this->_request->getModuleName();
		$this->_controller = $this->_request->getControllerName();
		$this->_action = $this->_request->getActionName();
		$url = $this->_controller . '/' . $this->_action;
		if (Factory::getSession('customer/id')) {
			$member = Factory::getSession('member');
			if ($this->_controller != 'entrance') {
				if ($member['id'] && $member['group_id']) {
					$logic = Factory::getLogic('system/member');
					$resources = $logic->loadGroupResource($member['group_id']);
//					if (isset($resources[$url])) {
//						$logic->operate($member['id'], $resources[$url]);	
						Factory::setSession('member/current-resource', $resources[$url]);
//					} else {
//						header("Content-type: text/html; charset=utf-8");
//						echo '您没有访问此资源的权限!';
//						exit(0);
//					}
				} else {
					$this->redirect('/monitor/entrance');
				}
			}
		} else {
			$this->redirect('/');
		}
	}

	/**
	 * @todo 加载页面布局
	 * @param 选取基本布局 $name
	 * @author 斌
	 */
	public function loadLayout($layout = NULL)
	{
		$this->_layout = new Project_View_Layout_Admin();
		$this->_layout->setBaseLayout($layout)
				->setTargetLayout(
					$this->_module,
					$this->_controller,
					$this->_action
		);	
		return $this;
	}
}
