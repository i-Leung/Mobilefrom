<?php
class Activity_ItemView extends Project_View_Block_Abstract
{
	/**
	 * 获取活动资料
	 * @param 活动ID $id
	 */
	public function getStoreActivityInfo($id)
	{
		if ($id && $record = Factory::getResource('store/activity')->getStoreActivityInfo($id)) {
			$record['description'] = htmlspecialchars_decode($record['description']);
			if ($record['limit']) {
				$last = $record['limit'] - time();
				$oneday = 24 * 60 * 60;
				$day = floor($last / $oneday);
				$onehour = 60 * 60;
				$hour = ceil(($last - $oneday * $day) / $onehour);
				$record['limit'] = $day . '天' . $hour . '小时';
			} else {
				$record['limit'] = '长期有效';
			}
			$record['gallery'] = explode(';', substr($record['gallery'], 0, -1));
			$record['qq'] = unserialize($record['qq']);
			$record['tel'] = unserialize($record['tel']);
			if ($addrs = Factory::getResource('store/addr')->getStoreAddr($record['store_id'])) {
				global $CStatic;
				$record['addr'] = '';
				foreach ($addrs as $item) {
					$record['addr'] .= '<p>[' . $CStatic['region'][$item['region_id']] . ']' . $item['addr'] . '</p>';
				}
			}
			return $record;
		}
		return false;
	}

	/**
	 * 获取下一条活动记录
	 * @param 当前活动ID $current
	 */
	public function getNextStoreActivity($current)
	{
		return Factory::getResource('store/activity')->getNextStoreActivity($current);
	}

	/**
	 * 获取活动页面Meta信息
	 * @param 活动ID $id
	 */
	public function getStoreActivityMeta($id)
	{
		return Factory::getResource('store/activity')->getStoreActivityMeta($id);
	}

	/**
	 * 判断是否我的活动
	 * @param 店铺ID $store
	 * @param 活动ID $id
	 */
	public function isMyActivity($store, $id)
	{
		return Factory::getResource('store/activity')->isMyActivity($store, $id);
	}
}
