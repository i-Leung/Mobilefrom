<?php
class Tlib_ListController extends Project_Controller_Action_Front
{
	/**
	 * 型号大全
	 */
	public function indexAction()
	{
		if ($order = $this->_request->getParam('order', null)) {
			Factory::setSession('tlib-order', $order);
		}
		if ($filter = $this->_request->getParam('filter', null)) {
			if ($filter == 'all') {
				Factory::unsetSession('tlib-filter');
			} else {
				foreach ($filter as $key => $value) {
					if ($value == 'all') {
						Factory::unsetSession('tlib-filter/' . $key);
					} else {
						Factory::setSession('tlib-filter/' . $key, $value);
					}
				}
			}
		}
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 获取指定型号报价
	 */
	public function loadModelQuoteAction()
	{
		$data = $this->_request->getParam('data', null);
		if ($data && $result = Factory::getView('tlib/list')->loadModelQuote($data)) {
			echo json_encode($result);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 获取更多平板型号信息
	 */
	public function moreModelAction()
	{
		$p = $this->_request->getParam('p', '1');
		if ($models = Factory::getView('tlib/list')->loadMoreModelList($p)) {
			echo json_encode($models);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 搜索页面
	 */
	public function searchAction()
	{
		$this->loadLayout()->renderLayout();
	}
}
