<?php
interface Project_View_Block_Interface
{
	/**
	 * 设定块模板文件路径
	 * @param 模板文件路径 $path
	 */
	public function setPath($path);
	
	/**
	 * 获取块模板文件路径
	 * @return 模板文件路径 
	 */
	public function getPath();
	
	/**
	 * 设定块模板
	 * @param 模板文件 $filename
	 */
	public function setTemplate($filename);
	
	/**
	 * 获取块模板
	 * @return 模板文件
	 */
	public function getTemplate();
	
	/**
	 * 设定块子元素
	 * @param 子元素数组 $children
	 * @return self
	 */
	public function setChildren($children = array());
	
	/**
	 * 获取块子元素html内容
	 * @param 子元素名称 $name
	 * @return mixed
	 */
	public function getChildHtml($name);
	
	/**
	 * 编译块内容
	 */
	public function render();
}