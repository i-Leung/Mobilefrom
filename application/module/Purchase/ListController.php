<?php
class Purchase_ListController extends Project_Controller_Action_Front
{
	/**
	 * 求购信息
	 */
	public function indexAction()
	{
		$tmp_filter = $this->_request->getParam('filter', null);
		$filter = Factory::getSession('pfilter');
		!is_null($filter) ? Factory::setSession('pfilter', $filter) : Factory::setSession('pfilter', array());
		if ($tmp_filter) {
			if (is_array($tmp_filter)) {
				foreach ($tmp_filter as $filter_id => $value_id) {
					//统计属性筛选频率
					Factory::getLogic('analysis/afilter')->markAttributeFilter($filter_id, $value_id, 2);
					//更新属性筛选项目
					if ($value_id == 'all') {
						Factory::unsetSession('pfilter/' . $filter_id);
					} else {
						Factory::setSession('pfilter/' . $filter_id, $value_id);
					}
				}
			} else {
				Factory::setSession('pfilter', array());
			}	
		}
		$orderby = $this->_request->getParam('porder', null);
		if (!$orderby) {
			if ($porder = Factory::getSession('porder')) {
				$orderby = $porder;
			} else {
				$orderby = 'refreshed';
			}
		}
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * ajax获取求购集合
	 */
	public function ajaxListAction()
	{
		global $static;
		$page = $this->_request->getParam('page', 1);
		$purchases = Factory::getView('purchase/list')->loadVisiblePurchase($page, 10);
		$customer = Factory::getView('customer/info');
		if ($purchases) {
			echo json_encode($purchases);
			exit(0);
		}
		echo json_encode(0);
	}
	
	/**
	 * 求购搜索
	 */
	public function searchAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * ajax获取求购集合
	 */
	public function ajaxSearchAction()
	{
		global $static;
		$page = $this->_request->getParam('page', 1);
		$q = $this->_request->getParam('q', 1);
		$purchases = Factory::getView('purchase/list')->loadSearchPurchase($q, 10);
		$customer = Factory::getView('customer/info');
		if ($purchases) {
			echo json_encode($purchases);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 求购浏览记录
	 */
	public function historyAction()
	{
		if ($phistory = Factory::getCookie('phistory')) {
			$purchases = Factory::getView('purchase/list')->loadPurchaseByIds(unserialize($phistory));
			$msg = Factory::getMsg();
			if (empty($msg) && $purchases) {
				echo json_encode($purchases);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 清空求购浏览记录
	 */
	public function clearHistoryAction()
	{
		Factory::setcookie('phistory', '', $expire, '/');
		echo json_encode(1);
	}
}
