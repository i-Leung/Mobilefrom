<?php
class Project_Controller_Action_Abstract
{
	/**
	 * 请求对象实例
	 */
	protected $_request = NULL;
	
	/**
	 * 页面布局对象
	 */
	protected $_layout = NULL;
	
	/**
	 * session控制器
	 */
	protected $_session_run = 1;
	
	public function __construct()
	{
		$this->init();
	}
	
	/**
	 * @author 斌
	 */
	public function init()
	{
		$this->setRequest();
		if ($this->_session_run) {
			session_start();
		}
	}
	
	/**
	 * @todo 加载页面布局
	 * @param 选取基本布局 $name
	 * @author 斌
	 */
	public function loadLayout($name)
	{
		
	}
	
	/**
	 * 解释布局
	 */
	public function renderLayout()
	{
		$this->_layout->explain();
	}
	
	public function setRequest()
	{
		$this->_request = Factory::getRequest();
		return $this;
	}
	
	public function getRequest()
	{
		return $this->_request;
	}
	
	/**
	 * 页面跳转
	 * @param 目标路径 $url
	 */
	public function redirect($url)
	{
		header('Location:' . $url);
	}
}
