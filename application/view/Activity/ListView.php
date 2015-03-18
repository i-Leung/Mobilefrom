<?php
class Activity_ListView extends Project_View_Block_Abstract
{
	/**
	 * 获取活动类型
	 * @param 活动类型 $type
	 */
	public function getActivityType($type = null)
	{
		$result = array(
			'1' => '购机优惠',
			'2' => '配件优惠',
			'3' => '服务优惠'
		);
		return $type ? $result[$type] : $result;
	}

	/**
	 * 获取活动列表
	 * @param 地区 $region
	 * @param 类型 $type
	 * @param 排序器 $sort
	 */
	public function loadStoreActivityList($region, $type, $sort)
	{
		if ($records = Factory::getResource('store/activity')->loadStoreActivityList($region, $type, $sort)) {
			foreach ($records as &$item) {
				$item['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])), 0, 250, 'utf-8');
				if ($item['limit']) {
					$last = $item['limit'] - time();
					$oneday = 24 * 60 * 60;
					$day = floor($last / $oneday);
					$day = $day ? $day . '天' : '';
					$onehour = 60 * 60;
					$hour = ceil(($last - $oneday * $day) / $onehour);
					$item['limit'] = $day . $hour . '小时';
				} else {
					$item['limit'] = '长期有效';
				}
			}
			return $records;
		}
		return false;
	}

	/**
	 * 获取店铺区域列表
	 * @param 店铺ID集合 $ids
	 */
	public function loadStoreRegionList($ids)
	{
		$result = array();
		if ($ids && $records = Factory::getResource('store/addr')->loadStoreRegionList($ids)) {
			global $CStatic;
			foreach ($records as $item) {
				$result[$item['store_id']] .= $CStatic['region'][$item['region_id']] . '；';
			}
		}
		return $result;
	}

	/**
	 * 获取最新活动信息
	 * @param 信息数量 $num
	 */
	public function loadNewStoreActivityList($amount = 1)
	{
		if ($records = Factory::getResource('store/activity')->loadNewStoreActivityList($amount)) {
			foreach ($records as &$item) {
				$item['description'] = mb_substr(strip_tags(htmlspecialchars_decode($item['description'])), 0, 250, 'utf-8');
				if ($item['limit']) {
					$last = $item['limit'] - time();
					$oneday = 24 * 60 * 60;
					$day = floor($last / $oneday);
					$day = $day ? $day . '天' : '';
					$onehour = 60 * 60;
					$hour = ceil(($last - $oneday * $day) / $onehour);
					$item['limit'] = $day . $hour . '小时';
				} else {
					$item['limit'] = '长期有效';
				}
			}
			return $records;
		}
		return false;
	}
}
