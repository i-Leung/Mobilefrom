<?php
class Monitor_Website_SuggestionController extends Project_Controller_Action_Admin
{
	/**
	 * 网站建议列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
		echo '网站建议列表';
	}
	
	/**
	 * 网站建议详细
	 */
	public function detailAction()
	{
		$this->loadLayout();
		$this->renderLayout();
		echo '网站建议详细';
	}
}