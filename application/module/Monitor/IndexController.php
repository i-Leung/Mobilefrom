<?php
class Monitor_IndexController extends Project_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->loadLayout('index')->renderLayout();
	}
}
