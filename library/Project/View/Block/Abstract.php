<?php
class Project_View_Block_Abstract implements Project_View_Block_Interface
{
	/**
	 * 布局文件路径
	 */
	protected $_path = NULL;
	
	/**
	 * 布局文件名
	 */
	protected $_template = NULL;
	
	/**
	 * 子元素集合
	 */
	protected $_children = NULL;
	
	/**
	 * 皮肤素材路径
	 */
	protected $_skinPath = NULL;
	
	public function __construct()
	{
		if (Factory::getRequest()->getModuleName() == 'monitor') {
			$this->_path = APPLICATION_PATH . '/design/admin/template/';
			$this->_skinPath = 'skin/admin/';
		} else {
			$current = 'V1.1';
			$this->_path = APPLICATION_PATH . '/design/front/' . $current . '/template/';
			$this->_skinPath = 'skin/front/' . $current . '/';
		}
		$this->_skinPath = Factory::getBaseUrl() . '/' . $this->_skinPath;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::setPath()
	 */
	public function setPath($path)
	{
		$this->_path = $path;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::getPath()
	 */
	public function getPath()
	{
		return $this->_path;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::setTemplate()
	 */
	public function setTemplate($filename)
	{
		$this->_template = $filename;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::getTemplate()
	 */
	public function getTemplate()
	{
		return $this->_template;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::setChildren()
	 */
	public function setChildren($children = array())
	{
		$this->_children = $children;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::getChildHtml()
	 */
	public function getChildHtml($name)
	{
		$root = &$this->_children[$name];
		if ($root) {
			$php = new $root['type']();
			if (isset($root['template'])) {
				$php->setTemplate($root['template']);
			}
			if (isset($root['children'])) {
				if (isset($root['children']['action'])) {//如何子元素包含action，则执行其方法
					$actions = $root['children']['action'];
					unset($root['children']['action']);
					foreach ($actions as $key => $action) {
						$params = &$action;
						foreach ($params as $param) {
							$php->$key($param);
						}
					}
				}
				$php->setChildren($root['children']);
			}
			return $php->render();
		}
		return FALSE;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Block_Interface::render()
	 */
	public function render()
	{
		ob_start();
		ob_clean();
		eval('?>' . file_get_contents($this->_path . $this->_template));
		$content = ob_get_contents();
		ob_end_clean();
		ob_implicit_flush(1);
		echo $content;
	}
	
	/**
	 * 获取图片
	 */
	public function getImg($img)
	{
		return $this->_skinPath . '/img/' . $img;
	}
	
	/**
	 * 获取页面插件
	 * @param $name 文件名
	 * @return string 文件路径
	 * @author 斌
	 */
	public function getPlugin($name)
	{
		return Factory::getBaseUrl() . '/plugin/' . $name;
	}
}
