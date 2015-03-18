<?php
class Activity_ListController extends Project_Controller_Action_Front
{
	/**
	 * 店铺优惠活动
	 */
	public function storeAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 获取商家所在区域
	 */
	public function getStoreRegionAction()
	{
		$store = $this->_request->getParam('store', null);
		if ($store) {
			if ($result = Factory::getView('activity/list')->loadStoreRegionList($store)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
