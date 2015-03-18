<?php
class Project_View_Layout_Abstract implements Project_View_Layout_Interface
{
	/**
	 * 布局文件路径
	 */
	protected $_path = '';
	
	/**
	 * 默认布局文件
	 */
	protected $_baseXml = 'page.xml';
	
	/**
	 * 默认布局节点
	 */
	protected $_baseLayout = 'default';
	
	/**
	 * 目标布局文件
	 */
	protected $_targetXml = NULL;
	
	/**
	 * 目标布局节点
	 */
	protected $_targetLayout = NULL;
	
	/**
	 * 实际布局配置
	 */
	protected $_layout = NULL;
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::setPath()
	 */
	public function setPath($path)
	{
		$this->_path = $path;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::getPath()
	 */
	public function getPath()
	{
		return $this->_path;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::setBaseLayout()
	 */
	public function setBaseLayout($layout)
	{
		if (!is_null($layout)) {
			$this->_baseLayout = $layout;
		}
		$this->_baseXml = simplexml_load_file($this->_path . $this->_baseXml);
		$this->_baseLayout = $this->xmlToArray($this->_baseXml->{$this->_baseLayout}[0]);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::getBaseLayout()
	 */
	public function getBaseLayout()
	{
		return $this->_baseLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::setLayout()
	 */
	public function setTargetLayout($module, $controller, $action){}
	
	public function getTargetLayout()
	{
		return $this->_targetLayout;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::xmlToArray()
	 */
	public function xmlToArray($xml)
	{
		if ($xml->count()) {
			$arr = array();
			foreach ($xml as $child) {
				$attributes = $child->attributes();
				foreach ($attributes as $key => $attribute) {
					if (in_array($key, array('name', 'method'))) {
						$name = (string)$attribute;
						if (in_array($child->getName(), array('reference', 'remove', 'action'))) {//区分修改跟移除的描述
							$tmp = &$arr[$child->getName()][$name];
						} else {
							$tmp = &$arr[$name];
						}
						if ($child->getName() == 'action') {
							$tmp[] = (string)$child;//若节点为action则获取其参数列表
						} else {
							$tmp = array();//赋值为空数组操作
						}
					} else {
						$tmp[$key] = (string)$attribute;
					}
				}
				$children = $this->xmlToArray($child);
				$children ? $tmp['children'] = $children : '';
			}
			return $arr;
		}
		return false;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::build()
	 */
	public function build(&$base, &$target)
	{
		if (is_array($base)) {
			foreach ($base as $key => &$value) {
				if (!in_array($key, array('block', 'type', 'template', 'children', 'action'))) {
					if (isset($target['reference'][$key])) {//reference合并子元素
						$value['children'] = isset($value['children']) && is_array($value['children']) ? $value['children'] : array();
						$value['children'] = array_merge_recursive($target['reference'][$key]['children'], $value['children']);
						if (isset($target['reference'][$key]['type'])) {//改变操作php文件
							$value['type'] = $target['reference'][$key]['type'];
						}
					}
				}
				if (isset($target['remove'][$key])) {//remove移除子元素
					unset($base[$key]);
				}
				if (!empty($value)) {
					$value = $this->build($value, $target);
				}
			}
		}
		return $base;
	}
	
	/**
	 * 解释布局配置
	 * @param 布局配置内容 $layout
	 */
	public function explain()
	{
		$this->_layout = $this->build($this->_baseLayout, $this->_targetLayout);
		$root = &$this->_layout['root'];
		$php = new $root['type']();
		if ($root['template']) {
			$php->setTemplate($root['template']);
		}
		if ($root['children']) {
			$php->setChildren($root['children']);
		}
		$php->render();
	}
}
