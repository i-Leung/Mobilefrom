<?php
class Purchase_ListView extends Project_View_Block_Abstract
{
	/**
	 * 获取我所求购手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或求购列表
	 */
	public function loadMyPurchase($customer_id, $page)
	{
		return Factory::getResource('purchase')->loadMyPurchase($customer_id, $page);
	}
	
	/**
	 * 获取我所发布求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyPurchaseTotal($customer_id)
	{
		$total = Factory::getSession('message/purchase');
		if (is_null($total)) {
			$total = Factory::getResource('purchase')->getMyPurchaseTotal($customer_id);
			Factory::setSession('message/purchase', $total);
		}
		return $total;
	}
	
	/**
	 * 获取我所发布过期求购列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或求购列表
	 */
	public function loadMyInactivePurchase($customer_id, $page, $per = 10)
	{
		return Factory::getResource('inactive/purchase')->loadMyInactivePurchase($customer_id, $page, $per);
	}
	
	/**
	 * 获取我所发布过期求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyInactivePurchaseTotal($customer_id)
	{
		return Factory::getResource('inactive/purchase')->getMyInactivePurchaseTotal($customer_id);
	}
	
	/**
	 * 获取用户求购手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或求购列表
	 */
	public function loadCustomerPurchase($customer_id, $page)
	{
		return Factory::getResource('purchase')->loadCustomerPurchase($customer_id, $page);
	}
	
	/**
	 * 获取用户发布求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getCustomerPurchaseTotal($customer_id)
	{
		return Factory::getResource('purchase')->getCustomerPurchaseTotal($customer_id);
	}
	
	/**
	 * 获取可见求购列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadVisiblePurchase($page = 1, $per = 10)
	{
		$cache = Factory::getLogic('cache');
		$pfilter = Factory::getSession('pfilter');
		if (empty($pfilter) && $purchases = $cache->getCache('purchase-list-' . $page)) {
			return $purchases;
		} else {
			$purchases = Factory::getResource('purchase')->loadVisiblePurchase($page, $per);
			if ($purchases) {
				global $CStatic;
				$customer = Factory::getView('customer/info');
				foreach ($purchases as &$item) {
					$item['created_at'] = timeforcustomer($item['created_at']);
					$item['contact'] = unserialize($item['contact']);
					$item['region'] = $CStatic['region'][$item['contact']['region']];
					$item['group'] = $customer->getGroupName($item['group']);
					unset($item['contact']);
				}
				if (empty($pfilter)) {
					$cache->createCache('purchase-list-' . $page, $purchases);
				}
				return $purchases;
			}
		}
		return array();
	}
	
	/**
	 * 获取可见求购总量
	 * @return false或总量
	 */
	public function getVisiblePurchaseTotal()
	{
		return Factory::getResource('purchase')->getVisiblePurchaseTotal();
	}
	
	/**
	 * 获取最新求购列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadNewPurchase($page = 1, $per = 8)
	{
		$cache = Factory::getLogic('cache');
		if ($purchases = $cache->getCache('purchase-new')) {
			return $purchases;
		} else {
			if ($purchases = Factory::getResource('purchase')->loadNewPurchase($page, $per)) {
				$cache->createCache('purchase-new', $purchases);
				return $purchases;
			}
		}
		return array();
	}
	
	/**
	 * 获取用户其它发布求购信息
	 * @param 用户ID $customer_id
	 * @param 已查看求购ID $purchase_id
	 * @return false或求购列表
	 */
	public function loadCustomerOther($customer_id, $purchase_id)
	{
		return Factory::getResource('purchase')->loadCustomerOther($customer_id, $purchase_id);
	}
	
	/**
	 * 获取求购状态
	 * @param 状态ID $id
	 */
	public function getStatus($id)
	{
		$status = '';
		switch ($id) {
			case '1':
				$status = '有效';
				break;
			case '2':
				$status = '过期';
				break;
			case '3':
				$status = '冻结';
				break;
		}
		return $status;
	}
	
	/**
	 * 转换手机过滤条件
	 * @param 过滤器 $filter
	 * @return 过滤值字符串
	 */
	function translate($filter)
	{
		$result = array();
		if (isset($filter[1])) {
			$brands = Factory::getView('system/mlib')->loadBrandList();
			$result[1] = $brands[$filter[1]];
		}
		if (isset($filter[2])) {
			global $CStatic;
			$result[2] = $CStatic['region'][$filter[2]];
		}
		if (empty($result)) {
			return null;
		}
		return $result;
	}
	
	/**
	 * 获取搜索求购列表
	 * @param 搜索内容 $q
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadSearchPurchase($q, $page = 1, $per = 10)
	{
		$result = Factory::getResource('purchase')->loadSearchPurchase($q, $page, $per);
		if ($result) {
			global $CStatic;
			$customer = Factory::getView('customer/info');
			foreach ($result as &$item) {
				$item['created_at'] = timeforcustomer($item['created_at']);
				$item['contact'] = unserialize($item['contact']);
				$item['region'] = $CStatic['region'][$item['contact']['region']];
				$item['group'] = $customer->getGroupName($item['group']);
				unset($item['contact']);
			}
			return $result;
		}
		return array();
	}
	
	/**
	 * 获取搜索求购总量
	 * @param 搜索内容 $q
	 * @return false或总量
	 */
	public function getSearchPurchaseTotal($q)
	{
		return Factory::getResource('purchase')->getSearchPurchaseTotal($q);
	}

	/**
	 * 后台获取用户所有发布求购列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadAdminCustomerPurchase($customer_id, $params, $page = 1, $per = 10)
	{
		return Factory::getResource('purchase')->loadAdminCustomerPurchase($customer_id, $params, $page, $per);
	}

	/**
	 * 后台获取用户所发布求购总量
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @return false或总量
	 */
	public function getAdminCustomerPurchaseTotal($customer_id, $params)
	{
	 	return Factory::getResource('purchase')->getAdminCustomerPurchaseTotal($customer_id, $params);
	}

	/**
	 * 根据提供的ID集合获取对应求购信息列表
	 * @param ID集合数组 $ids
	 */
	public function loadPurchaseByIds($ids)
	{
		if ($ids) {
			$ids = implode(',', $ids);
			return Factory::getResource('purchase')->loadPurchaseByIds($ids);
		}
		return false;
	}

	/**
	 * 加载有效筛选条件
	 */
	public function loadAvailableFilterOption()
	{
		$cache = Factory::getLogic('cache');
		if ($options = $cache->getCache('purchase-options')) {
			return $options;
		} else {
			$options = array();
			$result = Factory::getResource('relation/purchase/filter')->loadAvailableFilterOption();
			foreach ($result as $item) {
				$options[$item['filter_id']][$item['value_id']] = $item['value_id'];
			}
			$cache->createCache('purchase-options', $options);
			return $options;
		}
		return array();
	}
}
