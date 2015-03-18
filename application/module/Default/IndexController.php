<?php
class Default_IndexController extends Project_Controller_Action_Front
{
	public function indexAction()
	{
		if ($visited = Factory::getCookie('index-visited')) {
			Factory::setCookie('index-visited', $visited + 1);
		} else {
			Factory::setCookie('index-visited', '1');
		}
		$this->loadLayout();
		$this->renderLayout();
	}
}
