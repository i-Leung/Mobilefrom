<?php
class Store_ListView extends Project_View_Block_Abstract
{
	/**
	 * 获取商店列表
	 * @param 服务类型 $service
	 * @param 地区 $region
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadStoreList($service = null, $region = null, $page = 1, $per = 9)
	{
		if ($result = Factory::getResource('store')->loadStoreList($service, $region, $page, $per)) {
			foreach ($result as &$item) {
				$item['service'] = $item['service'] ? unserialize($item['service']) : null;
				$item['introduction'] = mb_substr(str_replace('&nbsp;', '', strip_tags(htmlspecialchars_decode($item['introduction']))), 0, 70, 'utf-8');
			}
		}
		return $result;
	}

	/**
	 * 获取新入驻商店列表
	 * @param 读取数量 $limit
	 */
	public function loadNewStoreList($limit = 4)
	{
		$stores = array();
		if ($records = Factory::getResource('store')->loadNewStoreList($limit)) {
			global $CStatic;
			foreach ($records as $item) {
				if (!isset($stores[$item['id']])) {
					$stores[$item['id']]['name'] = $item['name'];
					$stores[$item['id']]['customer'] = $item['customer_id'];
				}
				$stores[$item['id']]['region'][] = $CStatic['region'][$item['region_id']];
			}
		}
		return $stores;
	}

	/**
	 * 获取商店总数
	 * @param 服务类型 $service
	 * @param 地区 $region
	 */
	public function getStoreTotal($service = null, $region = null)
	{
		return Factory::getResource('store')->getStoreTotal($service, $region);
	}

	/**
	 * 获取指定型号出售店铺列表
	 * @param 手机型号 $id
	 */
	public function loadStoreListByMobileModel($id)
	{
		if ($id && $stores = Factory::getResource('store')->loadStoreListByMobileModel($id)) {
			global $CStatic;
			foreach ($stores as &$store) {
				$store['region'] = $CStatic['region'][$store['region_id']];
			}
			return $stores;
		}
		return false;
	}

	/**
	 * 获取指定型号出售店铺列表
	 * @param 手机型号 $id
	 */
	public function loadStoreListByTabletModel($id)
	{
		if ($id && $stores = Factory::getResource('store')->loadStoreListByTabletModel($id)) {
			global $CStatic;
			foreach ($stores as &$store) {
				$store['region'] = $CStatic['region'][$store['region_id']];
			}
			return $stores;
		}
		return false;
	}

	/**
	 * 加载指定型号商家报价
	 * @param 店铺手机型号ID $id
	 */
	public function loadModelStoreQuoteList($ids)
	{
		$ids = '"' . implode('", "', $ids) . '"';
		$quotes = Factory::getResource('store/mobile/item')->loadModelStoreQuoteList($ids);
		$result = array();
		foreach ($quotes as $item) {
			switch ($item['isnew']) {
				case '1':
					$result[$item['model']]['news'] = $item['min'];
					break;
				case '0':
					$result[$item['model']]['used'] = $item['min'];
					break;
			}
		}
		return $result;
	}

	/**
	 * 获取服务列表
	 */
	public function loadServiceTitleList()
	{
		if ($services = Factory::getResource('system/service')->loadServiceTitleList()) {
			$result = array();
			foreach ($services as $item) {
				$result[$item['id']] = $item['title'];
			}
			return $result;
		}
		return false;
	}

	/**
	 * 获取指定服务信息
	 * @param 服务ID $id
	 */
	public function getServiceInfo($id)
	{
		if ($service = Factory::getResource('system/service')->getServiceInfo($id)) {
			$service['description'] = htmlspecialchars_decode($service['description']);
		}
		return $service;
	}
}
