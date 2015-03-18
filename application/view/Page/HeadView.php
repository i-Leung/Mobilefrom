<?php
class Page_HeadView extends Project_View_Block_Abstract
{
	/**
	 * css文件集合
	 */
	protected $_css = array();
	
	/**
	 * css文件路径
	 */
	protected $_css_path = NULL;
	
	/**
	 * js文件集合
	 */
	protected $_js = array();
	
	/**
	 * js文件路径
	 */
	protected $_js_path = NULL;
	
	public function __construct()
	{
		parent::__construct();
		$this->_css_path = $this->_skinPath . 'css/';
		$this->_js_path = $this->_skinPath . 'js/';
	}
	
	/**
	 * 设置样式路径
	 * @param 路径 $path
	 */
	public function setCssPath($path)
	{
		$this->_css_path = $path;
		return $this;
	}
	
	/**
	 * 获取样式路径
	 */
	public function getCssPath()
	{
		return $this->_css_path;
	}
	
	/**
	 * 设置JS路径
	 * @param 路径 $path
	 */
	public function setJsPath($path)
	{
		$this->_js_path = $path;
		return $this;
	}
	
	/**
	 * 获取JS路径
	 */
	public function getJsPath()
	{
		return $this->_js_path;
	}
	
	/**
	 * 添加样式文件
	 * @param 文件名 $file
	 */
	public function addCss($file)
	{
		$this->_css[] = $file;
		return $this->_css;
	}
	
	/**
	 * 添加JS文件
	 * @param 文件名 $file
	 */
	public function addJs($file)
	{
		$this->_js[] = $file;
		return $this->_js;
	}
	
	/**
	 * 移除样式文件
	 * @param 文件名 $file
	 */
	public function removeCss($file)
	{
		$key = array_search($file, $this->_css);
		if (!is_null($key)) {
			unset($this->_css[$key]);
		}
		return $this->_css;
	}
	
	/**
	 * 移除JS文件
	 * @param 文件名 $file
	 */
	public function removeJs($file)
	{
		$key = array_search($file, $this->_js);
		if (!is_null($key)) {
			unset($this->_js[$key]);
		}
		return $this->_js;
	}
	
	/**
	 * 获取加载的样式集合
	 */
	public function loadCss()
	{
		return array_reverse($this->_css);
	}
	
	/**
	 * 获取加载的脚步集合
	 */
	public function loadJs()
	{
		return $this->_js;
	}
	
	/**
	 * 获取meta内容:title\keywords\description
	 */
	public function getTitle()
	{
		return '机荒网';
	}
	
	public function getKeywords()
	{
		return 0;
	}
	
	public function getDescription()
	{
		return 0;
	}
}
