<?php
class Activity_ItemController extends Project_Controller_Action_Front
{
	/**
	 * 店铺优惠活动
	 */
	public function storeAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 店铺优惠活动访问记录
	 */
	public function storeActivityClickAction()
	{
		$id = $this->_request->getParam('id', null);
		$store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($id && !Factory::getView('activity/item')->isMyActivity($store, $id)) {
			if ($result = Factory::getLogic('store')->markActivityClicks($id)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
