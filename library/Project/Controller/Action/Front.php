<?php
class Project_Controller_Action_Front extends Project_Controller_Action_Abstract
{
	/**
	 * @todo 加载页面布局
	 * @param 选取基本布局 $name
	 * @author 斌
	 */
	public function loadLayout($layout = NULL)
	{
		$this->_layout = new Project_View_Layout_Front();
		$this->_layout->setBaseLayout($layout)
					->setTargetLayout(
						$this->_request->getModuleName(), 
						$this->_request->getControllerName(), 
						$this->_request->getActionName()
					);
		return $this;
	}
}
