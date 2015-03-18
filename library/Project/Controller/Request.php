<?php
class Project_Controller_Request
{
	/**
	 * 模块名
	 */
	private $_module = 'Default';
	
	/**
	 * 控制器名
	 */
	private $_controller = 'IndexController';
	
	/**
	 * 动作名
	 */
	private $_action = 'indexAction';
	
	/**
	 * 单例模式获取对象
	 */
	private static $_instance = NULL;
	
	private function __clone(){}
	
	static public function getInstance()
	{
		if (is_null(self::$_instance) || !isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * 
	 * @todo 过滤请求输入的信息
	 * @param mixed $param
	 * @return mixed 已过滤的信息
	 */
	public function loadFilter($param)
	{
		//过滤规则
		$get = "'|(and|or)\\b.+?(>|<|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?Select|Update.+?SET|
		Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
		$post = "\\b(and|or)\\b.{1,6}?(>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?Select|
		Update.+?SET|Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
		$cookie = "\\b(and|or)\\b.{1,6}?(>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?Select|
		Update.+?SET|Insert\\s+INTO.+?VALUES|(Select|Delete).+?FROM|(Create|Alter|Drop|TRUNCATE)\\s+(TABLE|DATABASE)" ;
		$filters = array($get, $post, $cookie);
		
		if (is_array($param)) {
			foreach ($param as $key => $val) {
				$param[$key] = $this->loadFilter($val);
			}
		} else {
			$param = addslashes(htmlspecialchars($param, ENT_QUOTES, 'UTF-8'));
			//过滤操作
			foreach ($filters as $filter) {
				if (preg_match("/".$filter."/is", $param) == 1) {
					print "非法操作" ;
					exit();
				}
			}
		}
		return $param;
	}
	
	/**
	 * @todo 重写获取外部信息的所有方法，加入字符过滤处理
	 * @method getParam/getParams/getPost/getQuery/getCookie
	 */
	public function getParam($key, $default = null)
	{
		$params = array_merge($_POST, $_GET, $_COOKIE);
		$param = null;
		if (!isset($params[$key])) {
			$param = is_null($default) ? '' : $default;
		} else {
			$param = $params[$key];
		}
		return $this->loadFilter($param);
	}
	
	public function getParams()
	{
		return $this->loadFilter(
			array_merge($_POST, $_GET, $_COOKIE)
		);
	}
	
	public function getPost($key = null, $default = null)
	{
		$post = null;
		if (is_null($key)) {
			$post = $_POST;
		} elseif (!isset($_POST[$key])) {
			$post = is_null($default) ? '' : $default;
		} else {
			$post = $_POST[$key];
		}
		return $this->loadFilter($post);
	}
	
	public function getQuery($key = null, $default = null)
	{
		$query = null;
		if (is_null($key)) {
			$query = $_GET;
		} elseif (!isset($_GET[$key])) {
			$query = is_null($default) ? '' : $default;
		} else {
			$query = $_GET[$key];
		}
		return $this->loadFilter($query);
	}
	
	public function getCookie($key = null, $default = null)
	{
		$cookie = null;
		if (is_null($key)) {
			$cookie = $_COOKIE;
		} elseif (!isset($_COOKIE[$key])) {
			$cookie = is_null($default) ? '' : $default;
		} else {
			$cookie = $_COOKIE[$key];
		}
		return $this->loadFilter($cookie);
	}
	
	/**
	 * 设置模块名
	 * @param 模块名 $module
	 */
	public function setModuleName($module)
	{
		$this->_module = $module;
	}
	
	/**
	 * 获取模块名
	 */
	public function getModuleName()
	{
		return $this->_module;
	}
	
	/**
	 * 设置控制器名
	 * @param 控制器名 $controller
	 */
	public function setControllerName($controller)
	{
		$this->_controller = $controller;
	}
	
	/**
	 * 获取控制器名
	 */
	public function getControllerName()
	{
		return $this->_controller;
	}
	
	/**
	 * 设置动作名
	 * @param 动作名 $action
	 */
	public function setActionName($action)
	{
		$this->_action = $action;
	}
	
	/**
	 * 获取动作名
	 */
	public function getActionName()
	{
		return $this->_action;
	}
}
