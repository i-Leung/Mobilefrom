<?php
interface Project_View_Layout_Interface
{
	/**
	 * 设定布局文件路径
	 * @param 布局文件路径 $path
	 */
	public function setPath($path);
	
	/**
	 * 获取布局文件路径
	 * @return 布局文件路径 
	 */
	public function getPath();
	
	/**
	 * 设定基本布局
	 * @param 基本布局 $layout
	 */
	public function setBaseLayout($layout);
	
	/**
	 * 获取基本布局
	 * @return 基本布局
	 */
	public function getBaseLayout();
	
	/**
	 * 设定请求布局
	 * @param 模块 $module
	 * @param 控制器 $controller
	 * @param 动作 $action
	 */
	public function setTargetLayout($module, $controller, $action);
	
	/**
	 * 获取请求布局
	 * @return 请求布局
	 */
	public function getTargetLayout();
	
	/**
	 * xml结构转array结构
	 * @param 需要转换的xml结构
	 */
	public function xmlToArray($xml);
	
	/**
	 * 构建最终布局
	 * @param 基本布局 $base
	 * @param 请求布局 $request
	 */
	public function build(&$base, &$target);
	
	/**
	 * 解释布局
	 */
	public function explain();
}