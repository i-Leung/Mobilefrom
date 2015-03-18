<?php
class Factory
{
	/**
	 * @var 网站根域名
	 */
	private static $_baseUrl = 'http://www.mobilefrom.com';
	
	/**
	 * @var 过程操作信息
	 */
	private static $_msg = array();
	
	/**
	 * 获取项目根网址
	 * @return string url
	 * @author 斌
	 */
	public static function getBaseUrl()
	{
		return self::$_baseUrl;
	}
	
	/**
	 * 获取网址
	 * @param $path 网址请求路径
	 * @param array $params 传递参数
	 * @return string url
	 * @author 斌
	 */
	public static function getUrl($path, $params = array())
	{
		$uri = array();
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				$uri[] = $key . '=' . $value;
			}
		} else {
			throw new Exception('参数必需为数组');
		}
		return self::getBaseUrl() . '/' . $path . '?' . implode('&', $uri);
	}
	
	/**
	 * 获取请求操作类
	 */
	public static function getRequest()
	{
		return Project_Controller_Request::getInstance();
	}
	
	/**
	 * 获取指定逻辑模型
	 * @param 模型类名称 $classname
	 * @return 逻辑模型 obj
	 */
	public static function getLogic($classname)
	{
		$classname = explode('/', $classname);
		foreach ($classname as $key => $value) {
			$classname[$key] = ucfirst($value);
		}
		$classname = 'Logic_' . implode('_', $classname);
		return new $classname;
	}
	
	/**
	 * 获取指定资源模型
	 * @param 模型类名称 $classname
	 * @return 资源模型 obj
	 */
	public static function getResource($classname) 
	{
		$classname = explode('/', $classname);
		foreach ($classname as $key => $value) {
			$classname[$key] = ucfirst($value);
		}
		$classname = 'Resource_' . implode('_', $classname);
		return new $classname;
	}
	
	/**
	 * 获取指定视图处理类
	 * @param 视图处理类名称 $classname
	 * @return 视图处理类实例 obj
	 */
	public static function getView($classname)
	{
		$classname = explode('/', $classname);
		foreach ($classname as $key => $value) {
			$classname[$key] = ucfirst($value);
		}
		$classname = implode('_', $classname) . 'View';
		return new $classname;
	}
	
	/**
	 * 设置操作过程信息
	 * @param $msg
	 */
	public static function setMsg($msg)
	{
		/**
		$log = 'var/log/' . date('Y-m-d') . '.log';
		if (!file_exists($log)) {
			touch($log);
		}
		$handle = fopen($log, 'a');
		fwrite($handle, $msg . "\n\n");
		fclose($handle);
		*/
		return self::$_msg[] = $msg;
	}
	
	/**
	 * 获取操作过程信息
	 * @return string
	 */
	public static function getMsg()
	{
		return self::$_msg;
	}
	
	/**
	 * 设置session值(只支持一维/二维数组)
	 * @param 键 $key
	 * @param 值 $value
	 */
	public static function setSession($key, $value)
	{
		if (strpos($key, '/')) {
			$tmp = explode('/', $key);
			$_SESSION[$tmp[0]][$tmp[1]] = $value;
		} else {
			$_SESSION[$key] = $value;
		}
	}
	
	/**
	 * 获取指定session值(只支持一维/二维数组)
	 * @param 键 $key
	 * @param 值 $value
	 */
	public static function getSession($key, $value = null)
	{
		if (strpos($key, '/')) {
			$tmp = explode('/', $key);
			if (isset($_SESSION[$tmp[0]][$tmp[1]])) {
				return $_SESSION[$tmp[0]][$tmp[1]];
			}
		} else {
			if (isset($_SESSION[$key])) {
				return $_SESSION[$key];
			}
		}
		self::setSession($key, $value);
		return $value;
	}
	
	/**
	 * 清除对应session值
	 * @param 键 $key
	 */
	public static function unsetSession($key)
	{
		if (strpos($key, '/')) {
			$key = explode('/', $key);
			if (isset($_SESSION[$key[0]][$key[1]])) {
				unset($_SESSION[$key[0]][$key[1]]);
			}
		} else {
			if (isset($_SESSION[$key])) {
				unset($_SESSION[$key]);
			}
		}
	}
	
	/**
	 * 设置cookie值
	 * @param 键 $key
	 * @param 值 $value
	 */
	public static function setCookie($key, $value, $expire = null)
	{
		$expire = $expire ? $expire : time() + 2592000;
		setcookie($key, $value, $expire, '/');
	}
	
	/**
	 * 获取指定cookie值
	 * @param 键 $key
	 */
	public static function getCookie($key)
	{
		return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
	}
	
	/**
	 * 销毁用户使用痕迹
	 */
	public static function unsetTrace()
	{
		session_unset();
		session_destroy();
		$expire = time() - 3600;
		self::setcookie('customer_id', '', $expire, '/');
		self::setcookie('compare', '', $expire, '/');
		self::setcookie('mhistory', '', $expire, '/');
		self::setcookie('phistory', '', $expire, '/');
	}
	
	/**
	 * 判断用户是否已登录
	 */
	public static function isLoged()
	{
		if (self::getSession('customer/id')) {
			return true;
		} else {
			if ($customer_id = self::getCookie('customer_id')) {
				$logic = Factory::getLogic('customer');
				$customer = $logic->getInfo($customer_id);
				self::setSession('customer', $customer);
				$logic->reflashLogonLatest($customer_id);
				return true;
			}
		}
		return false;
	}
}
