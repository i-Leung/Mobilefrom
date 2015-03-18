<?php
class Customer_StoreView extends Project_View_Block_Abstract
{
	/**
	 * 获取店铺图片上传处理路径
	 */
	public function getStoreUploadUrl()
	{
		return Factory::getUrl('plugin/swfupload/upload-store-img');
	}

	/**
	 * 获取店铺资料
	 * @param 用户ID $customer_id
	 */
	public function getMyStoreInfo($customer_id)
	{
		if ($store = Factory::getResource('store')->getMyStoreInfo($customer_id)) {
			$store['gallery'] = explode(';', substr($store['gallery'], 0, -1));
			$store['introduction'] = htmlspecialchars_decode($store['introduction']);
			if ($addrs = Factory::getResource('store/addr')->getStoreAddr($store['id'])) {
				global $CStatic;
				$store['addr'] = array();
				foreach ($addrs as $item) {
					$store['addr'][] = array(
						'rid' => $item['region_id'],
						'region' => $CStatic['region'][$item['region_id']],
						'addr' => $item['addr']
					);
				}
			}
		}
		return $store;
	}

	/**
	 * 获取店铺ID
	 * @param 用户ID $customer_id
	 */
	public function getStoreId($customer_id)
	{
		return Factory::getResource('store')->getStoreId($customer_id);
	}

	/**
	 * 获取店铺设置
	 * @param 店铺ID $store_id
	 */
	public function getStoreSetting($store_id)
	{
		if ($setting = Factory::getResource('store/setting')->getStoreSetting($store_id)) {
			$setting['working'] = $setting['working'] ? array_reverse(unserialize($setting['working'])) : '';
			$setting['tel'] = unserialize($setting['tel']);
			$setting['qq'] = unserialize($setting['qq']);
			return $setting;
		}
		return false;
	}

	/**
	 * 检测店铺是否通过验证
	 * @param 用户ID $customer_id
	 */
	public function isValidated($customer_id)
	{
		return Factory::getResource('store')->isValidated($customer_id);
	}

	/**
	 * 获取用户列表
	 * @param 所在地区 $region 
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadStoreList($region = null, $page = 1, $per = 10)
	{
		return Factory::getResource('customer')->loadStoreList($region, $page, $per);
	}
	
	/**
	 * 获取用户总数
	 * @param 所在地区 $region 
	 */
	public function getStoreTotal($region = null)
	{
		return Factory::getResource('customer')->getStoreTotal($region);
	}

	/**
	 * 获取商店服务列表
	 * @param 店铺ID $store
	 */
	public function loadMyServiceList($store)
	{
		return Factory::getResource('store/service')->loadMyServiceList($store);
	}

	/**
	 * 获取今日店铺访问信息
	 * @param 过滤条件 $range
	 */
	public function getStoreAnalysis($range)
	{
		$result = array();
		$resource = Factory::getResource('store/visitor');
		$result['ip'] = $resource->getIpAmount($range);
		$result['pv'] = $resource->getPvAmount($range);
		$result['consult'] = $resource->getConsultAmount($range);
		return $result;
	}

	/**
	 * 获取店铺浏览记录
	 * @param 过滤条件 $range
	 */
	public function loadVisitorLog($range)
	{
		return Factory::getResource('store/visitor')->loadVisitorLog($range);
	}

	/**
	 * 获取页面名称
	 */
	public function loadStorePageName()
	{
		return array(
			'item/index' => '店铺首页',
			'item/mobile-list' => '手机专区',
			'item/mobile-info' => '手机详细',
			'item/mobile-sheet' => '手机报价',
			'item/model-list' => '手机专区',
			'item/model-info' => '手机详细',
			'item/model-sheet' => '手机报价',
			'item/tablet-list' => '平板专区',
			'item/tablet-info' => '平板详细',
			'item/service' => '店铺服务',
			'item/about' => '关于本店',
			'item/feedback' => '本店留言',
			'store/markqq' => 'QQ咨询',
			'search/inside' => '店内搜索',
			'search/outside' => '全站搜索'
		);
	}

	/**
	 * 页面导向统计时间范围计算
	 * @param 店铺ID $store
	 * @param 统计范围 $range
	 */
	public function getDateRange($store, $range)
	{
		$now = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$day = 24 * 60 * 60;
		$where = array();
		$params = array();
		switch ($range) {
			case '1':
				$where[] = '`date` = :now';
				$params[':now'] = $now;
				break;
			case '2':
				$where[] = '`date` >= :from';
				$where[] = '`date` <= :to';
				$params[':from'] = $now - 3 * $day;
				$params[':to'] = $now;
				break;
			case '3':
				$where[] = '`date` >= :from';
				$where[] = '`date` <= :to';
				$params[':from'] = $now - 7 * $day;
				$params[':to'] = $now;
				break;
			case '4':
				break;
		}
		$where[] = '`store_id` = :store';
		$params[':store'] = $store;
		return array('where' => $where, 'params' => $params);
	}

	/**
	 * 获取店铺活动列表
	 */
	public function loadMyActivityList()
	{
		if ($store = $this->getStoreId(Factory::getSession('customer/id'))) {
			if ($records = Factory::getResource('store/activity')->loadMyActivityList($store)) {
				foreach ($records as &$item) {
					if ($item['status']) {
						if ($item['limit'] > time() || $item['limit'] == 0) {
							$item['status'] = 1;
						} else {
							$item['status'] = 0;
						}
					} else {
						$item['status'] = 0;
					}
				}
			}
			return $records;
		}
		return false;
	}

	/**
	 * 获取店铺活动列表
	 * @param 活动ID $id
	 */
	public function getMyActivityInfo($id)
	{
		if ($store = $this->getStoreId(Factory::getSession('customer/id'))) {
			if ($activity = Factory::getResource('store/activity')->getMyActivityInfo($id, $store)) {
				$activity['gallery'] = array_reverse(explode(';', substr($activity['gallery'], 0, -1)));
				$activity['description'] = htmlspecialchars_decode($activity['description']);
				if ($activity['status']) {
					if ($activity['limit'] > time()) {
						$last = $activity['limit'] - time();
						$oneday = 24 * 60 * 60;
						$day = floor($last / $oneday);
						$onehour = 60 * 60;
						$hour = ceil(($last - $oneday * $day) / $onehour);
						$activity['limit'] = '还剩余' . $day . '天' . $hour . '小时';
					} elseif ($activity['limit'] == 0) {
						$activity['limit'] = '无限期';
					} else {
						$activity['limit'] = '已过期';
					}
				} else {
					$activity['limit'] = '已过期';
				}
				return $activity;
			}
		}
		return false;
	}

	/**
	 * 店铺是否有权限参与活动
	 * @param 店铺ID $store
	 * @param 活动ID $id
	 */
	public function canEditActivity($store, $id)
	{
		$resource = Factory::getResource('store/activity');
		if ($id) {
			return !$resource->isActiveActivity($store, $id);
		} else {
			return $resource->hasActiveActivity($store);
		}
	}
}
