<?php
class Project_View_Layout_Admin extends Project_View_Layout_Abstract
{
	public function __construct()
	{
		$this->_path = APPLICATION_PATH . '/design/admin/layout/';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::setLayout()
	 */
	public function setTargetLayout($module, $controller, $action)
	{
		if (strstr($controller, '_')) {
			$tmp = explode('_', $controller);
			$this->_targetXml = strtolower($tmp[0]) . '/' . strtolower($tmp[1]) . '.xml';
		} else {
			$this->_targetXml = strtolower($controller) . '.xml';
		}
		$this->_targetLayout = strtolower($controller . '_' . $action);
		$this->_targetXml = simplexml_load_file($this->_path . $this->_targetXml);
		$this->_targetLayout = $this->xmlToArray($this->_targetXml->{$this->_targetLayout}[0]);
		return $this;
	}
}