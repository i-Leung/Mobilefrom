<?php
class Store_SearchController extends Project_Controller_Action_Front
{
	/**
	 * 店内搜索
	 */
	public function indexAction()
	{
		$this->loadLayout('store')->renderLayout();
	}
}
