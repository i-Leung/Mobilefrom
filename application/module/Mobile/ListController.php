<?php
class Mobile_ListController extends Project_Controller_Action_Front
{
	/**
	 * 手机天地
	 */
	public function indexAction()
	{
		//属性筛选
		$tmp_filter = $this->_request->getParam('filter', null);
		$filter = Factory::getSession('mfilter');
		!is_null($filter) ? Factory::setSession('mfilter', $filter) : Factory::setSession('mfilter', array());
		if ($tmp_filter) {
			if (is_array($tmp_filter)) {
				foreach ($tmp_filter as $filter_id => $value_id) {
					//统计属性筛选频率
					Factory::getLogic('analysis/afilter')->markAttributeFilter($filter_id, $value_id, 1);
					//更新属性筛选项目
					if ($value_id == 'all') {
						Factory::unsetSession('mfilter/' . $filter_id);
					} else {
						Factory::setSession('mfilter/' . $filter_id, $value_id);
					}
				}
			} else {
				Factory::setSession('mfilter', array());
			}	
		}
		//排序方式
		$orderby = $this->_request->getParam('morder', null);
		if (!$orderby) {
			if ($morder = Factory::getSession('morder')) {
				$orderby = $morder;
			} else {
				$orderby = 'refreshed';
			}
		}
		Factory::setSession('morder', $orderby);
		//展示方式
		$vtype = $this->_request->getParam('mview', null);
		if (!$vtype) {
			if ($mview = Factory::getSession('mview')) {
				$vtype = $mview;
			} else {
				$vtype = 'grid';
			}
		}
		Factory::setSession('mview', $vtype);
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 *手机对比
	 */
	public function compareAction()
	{
	 	$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 添加手机对比
	 */
	public function addCompareAction()
	{
		$id = $this->_request->getParam('id');
		$compare = Factory::getCookie('compare');
		if ($compare) {
			if (strpos($compare, ',')) {
				$compare = substr($compare, 0, -1);
				$compare = explode(',', $compare);
				if (count($compare) >= 4) {
					echo json_decode(0);
				} else {
					if (in_array($id, $compare)) {
						echo json_decode(2);
					} else {
						Factory::setCookie('compare', Factory::getCookie('compare') . $id . ',');
						echo json_decode(1);
					}
				}
				exit(0);
			}
		}
		Factory::setCookie('compare', $id . ',');
		echo json_decode(1);
	}
	
	/**
	 * 删除手机对比
	 */
	public function removeCompareAction()
	{
		$id = $this->_request->getParam('id');
		$compare = Factory::getCookie('compare');
		if ($compare) {
			$compare = str_replace($id . ',', '', $compare);
			Factory::setCookie('compare', $compare);
			echo json_decode(1);
			exit(0);
		}
		echo json_decode(1);
	}
	
	/**
	 * ajax加载对比列表窗口
	 */
	public function loadCompareAction()
	{
		$compare = Factory::getCookie('compare');
		if ($compare && strpos($compare, ',')) {
			$compare = substr($compare, 0, -1);
			if ($compare = Factory::getLogic('mobile')->loadCompareOutline($compare)) {
				echo json_encode($compare);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * ajax加载对比列表
	 */
	public function showCompareAction()
	{
		$compare = Factory::getCookie('compare');
		if ($compare && strpos($compare, ',')) {
			$compare = substr($compare, 0, -1);
			if ($compare = Factory::getLogic('mobile')->showCompare($compare)) {
				global $MStatic;
				global $CStatic;
				$customer = Factory::getView('customer/info');
				foreach ($compare as &$value) {
					$value['data'] = unserialize($value['data']);
					$value['data']['cost'] = $MStatic['cost'][$value['data']['cost']];
					$value['data']['newly'] = $MStatic['newly'][$value['data']['newly']];
					$value['data']['repair'] = is_int($value['data']['repair']) ? $MStatic['repair'][$value['data']['repair']] : $value['data']['repair'];
					$value['data']['trouble'] = $value['data']['trouble'] ? $value['data']['trouble'] : '完全正常';
					$value['contact'] = unserialize($value['contact']);
					$value['contact']['region'] = $CStatic['region'][$value['contact']['region']];
					$value['group'] = $customer->getGroupName($value['group']);
				}
				echo json_encode($compare);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * ajax获取手机集合
	 */
	public function ajaxListAction()
	{
		$page = $this->_request->getParam('page', 1);
		$mobiles = Factory::getView('mobile/list')->loadVisibleMobile($page, 20, Factory::getSession('mview'));
		$customer = Factory::getView('customer/info');
		if ($mobiles) {
			foreach ($mobiles as &$mobile) {
				$mobile['group'] = $customer->getGroupName($mobile['group']);
				$mobile['created_at'] = timeforcustomer($mobile['created_at']);
			}
			echo json_encode($mobiles);
			exit(0);
		}
		echo json_encode(0);
	}
	
	/**
	 * 手机搜索
	 */
	public function searchAction()
	{
		//排序方式
		$orderby = $this->_request->getParam('smorder', null);
		if (!$orderby) {
			if ($smorder = Factory::getSession('smorder')) {
				$orderby = $smorder;
			} else {
				$orderby = 'refreshed';
			}
		}
		Factory::setSession('smorder', $orderby);
		//展示方式
		$vtype = $this->_request->getParam('mview', null);
		if (!$vtype) {
			if ($mview = Factory::getSession('mview')) {
				$vtype = $mview;
			} else {
				$vtype = 'grid';
			}
		}
		Factory::setSession('mview', $vtype);
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * ajax获取手机集合
	 */
	public function ajaxSearchAction()
	{
		$page = $this->_request->getParam('page', 1);
		$q = $this->_request->getParam('q', '');
		$mobiles = $q ? Factory::getView('mobile/list')->loadSearchMobile($q, Factory::getSession('mview'), $page, 20) : array();
		$customer = Factory::getView('customer/info');
		if ($mobiles) {
			foreach ($mobiles as &$mobile) {
				$mobile['group'] = $customer->getGroupName($mobile['group']);
				$mobile['created_at'] = timeforcustomer($mobile['created_at']);
			}
			echo json_encode($mobiles);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 闲置手机浏览记录
	 */
	public function historyAction()
	{
		if ($mhistory = Factory::getCookie('mhistory')) {
			$mobiles = Factory::getView('mobile/list')->loadMobileByIds(unserialize($mhistory));
			$msg = Factory::getMsg();
			if (empty($msg) && $mobiles) {
				echo json_encode($mobiles);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 清空手机浏览记录
	 */
	public function clearHistoryAction()
	{
		Factory::setcookie('mhistory', '', $expire, '/');
		echo json_encode(1);
	}
}
