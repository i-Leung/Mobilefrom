<?php
class Project_View_Layout_Front extends Project_View_Layout_Abstract
{
	public function __construct()
	{
		$current = 'V1.1';
		$this->_path = APPLICATION_PATH . '/design/front/' . $current . '/layout/';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Project_View_Layout_Interface::setLayout()
	 */
	public function setTargetLayout($module, $controller, $action)
	{
		$this->_targetXml = strtolower($module) . '.xml';
		$this->_targetLayout = strtolower($module . '_' . $controller . '_' . $action);
		$this->_targetXml = simplexml_load_file($this->_path . $this->_targetXml);
		$this->_targetLayout = $this->xmlToArray($this->_targetXml->{$this->_targetLayout}[0]);
		return $this;
	}
}
