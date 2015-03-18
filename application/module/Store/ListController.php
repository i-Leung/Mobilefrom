<?php
class Store_ListController extends Project_Controller_Action_Front
{
	/**
	 * 本地商家
	 */
	public function indexAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * ajax获取本地商家
	 */
	public function ajaxAction()
	{
		$region = $this->_request->getParam('region', null);
		$page = $this->_request->getParam('p', '1');
		if ($stores = Factory::getView('store/list')->loadStoreList($region, $page)) {
			echo json_encode($stores);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 加载指定型号商家报价
	 */
	public function loadModelStoreQuoteAction()
	{
		if ($ids = $this->_request->getParam('ids', null)) {
			$result = Factory::getView('store/list')->loadModelStoreQuoteList($ids);
			if ($result) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
