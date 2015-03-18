<?php
class Project_Application
{
	/**
	 * 模块
	 */
	private $_module = '';
	
	/**
	 * 控制器
	 */
	private $_controller = '';
	
	/**
	 * 动作
	 */
	private $_action = '';
	
	/**
	 * 所耗内存
	 */
	private $_memory = 0;
	
	/**
	 * 所耗时间
	 */
	private $_time = 0;
	
	/**
	 * 请求
	 */
	private $_request;
	
	public function __construct()
	{
		$this->_initRequest();
	}
	
	/**
	 * 初始化请求对象
	 */
	private function _initRequest()
	{
		$this->_request = Factory::getRequest();
	}
	
	/**
	 * 请求路由
	 */
	private function _router()
	{
		//过滤多余字符
		$uri = substr($_SERVER["REQUEST_URI"], 1);
		$q = strpos($uri, '?');
		if ($q !== false) {
			$uri = substr($uri, 0, $q);
		}
		if (substr($uri, -1) == '/') {
			$uri = substr($uri, 0, -1);
		}
		$uri = explode('/', $uri);
		//构造模块
		$module = ($uri[0] == '') ? 'Default' : ucwords($uri[0]);
		$this->setModule($module);
		$this->_request->setModuleName(strtolower($module));
		//构造控制器
		$controller = '';
		if (!isset($uri[1]) || $uri[1] == '') {
			$this->_request->setControllerName('index');
			$controller = 'IndexController';
		} else {
			$this->_request->setControllerName($uri[1]);
			if (strstr($uri[1], '_')) {
				$controller = explode('_', $uri[1]);
				$controller = ucwords($controller[0]) . '_' . ucwords($controller[1]) . 'Controller';
			} else {
				$controller = ucwords($uri[1]) . 'Controller';
			}
		}
		$this->setController($module . '_' . $controller);
		//构造动作
		if (!isset($uri[2]) || $uri[2] == '') {
			$this->_request->setActionName('index');
			$action = 'indexAction';
		} else {
			$this->_request->setActionName($uri[2]);
			if (strstr($uri[2], '-')) {
				$action = explode('-', $uri[2]);
				foreach ($action as $key => &$value) {
					if ($key != 0) {
						$value = ucwords($value);
					}
				}
				$action = implode('', $action) . 'Action';
			} else {
				$action = $uri[2] . 'Action';
			}
		}
		$this->setAction($action);
		return $this;
	}
	
	/**
	 * 执行请求
	 */
	public function run()
	{
		try {
			$this->_appStart();
			$this->_router();//路由
			$controller = $this->getController();//实例化
			if (method_exists($controller, $this->getAction())) {
				$controller->{$this->getAction()}();//调用
			} else {
				notfound();
			}
			$this->_appEnd();
		} catch (Exception $e) {
			echo 'error';
		}
	}
	
	/**
	 * 设置模块
	 * @param string $module
	 */
	public function setModule($module)
	{
		$this->_module = $module;
		return $this;
	}
	
	/**
	 * 获取模块
	 */
	public function getModule()
	{
		return $this->_module;
	}
	
	/**
	 * 设置控制器
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->_controller = $controller;
		return $this;
	}
	
	/**
	 * 获取控制器
	 */
	public function getController()
	{
		return new $this->_controller();
	}
	
	/**
	 * 设置动作
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->_action = $action;
	}
	
	/**
	 * 获取控制器
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * 判断请求是否未ajax
	 */
	public function is_xhr()
	{
	  return @$_SERVER[ 'HTTP_X_REQUESTED_WITH' ] === 'XMLHttpRequest';
	}
	
	
	/**
	 * 应用起始点
	 */
	private function _appStart()
	{
		$this->_memory = memory_get_usage();
		$this->_time = microtime(true);
		return $this;
	}
	
	/**
	 * 应用结束点
	 */
	private function _appEnd()
	{
		if ($this->is_xhr()) {
			return $this;
		}
// 		echo '<br />';
// 		echo $this->_memory = (memory_get_usage() - $this->_memory) . 'byte';
// 		echo '<br />';
// 		echo $this->_time = ((microtime(true) - $this->_time) * 1000) . 'ms';
		return $this;
	}
}
