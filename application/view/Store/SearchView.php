<?php
class Store_SearchView extends Project_View_Block_Abstract
{
	/**
	 * 店内搜索
	 * @param 店铺ID $store
	 * @param 搜索内容 $q
	 */
	public function loadInsideRecords($store, $q)
	{
		$mobile = Factory::getResource('system/mlib/model')->loadInsideRecords($store, $q);
		$tablet = Factory::getResource('system/tlib/model')->loadInsideRecords($store, $q);
		return array('mobile' => $mobile, 'tablet' => $tablet);
	}

	/**
	 * 获取店内搜索结果总数
	 * @param 店铺ID $store
	 * @param 搜索内容 $q
	 */
	public function getInsideRecordsTotal($store, $q)
	{
		$mobile = Factory::getResource('system/mlib/model')->getInsideRecordsTotal($store, $q);
		$tablet = Factory::getResource('system/tlib/model')->getInsideRecordsTotal($store, $q);
		return $mobile + $tablet;
	}

	/**
	 * 站内搜索
	 * @param 搜索内容 $q
	 */
	public function loadOutsideRecords($q)
	{
		$mobile = Factory::getResource('system/mlib/model')->loadOutsideRecords($q);
		$tablet = Factory::getResource('system/tlib/model')->loadOutsideRecords($q);
		return array('mobile' => $mobile, 'tablet' => $tablet);
	}

	/**
	 * 获取站内搜索结果总数
	 * @param 搜索内容 $q
	 */
	public function getOutsideRecordsTotal($q)
	{
		$mobile = Factory::getResource('system/mlib/model')->getOutsideRecordsTotal($q);
		$tablet = Factory::getResource('system/tlib/model')->getOutsideRecordsTotal($q);
		return $mobile + $tablet;
	}
}
