<?php
class Analysis_AfilterView extends Project_View_Block_Abstract
{
	/**
	 * 加载手机属性筛选记录列表
	 * @param 属性ID $filter_id
	 */
	public function loadMobileAttributeRecord($filter_id)
	{
		return Factory::getResource('analysis/afilter')->loadAttributeFilterRecord(1, $filter_id);
	}

	/**
	 * 获取手机属性值标
	 * @param 属性ID $filter_id
	 * @param 属性值ID $value_id
	 */
	public function mobileAttributeTranslateFilter($filter_id, $value_id)
	{
		global $static;
		$translate = array(
			1 => 3,
			2 => 1,
			3 => 4,
			4 => 5,
			5 => 6,
			6 => 7,
			7 => 9
		);
		if ($translate[$filter_id] == '3') {
			return $static['mobile'][$translate[$filter_id]][$value_id]['label'];
		}
		return $static['mobile'][$translate[$filter_id]][$value_id];
	}

	/**
	 * 获取手机所有筛选属性
	 */
	public function loadAllMobileAttribute()
	{
		return array(
			1 => '价格范围',
			2 => '手机品牌',
			3 => '新旧程度',
			4 => '手机版本',
			5 => '手机系统',
			6 => '交易地区',
			7 => '卖家性质'
		);
	}

	/**
	 * 加载求购属性筛选记录列表
	 * @param 属性ID $filter_id
	 */
	public function loadPurchaseAttributeRecord($filter_id)
	{
		return Factory::getResource('analysis/afilter')->loadAttributeFilterRecord(2, $filter_id);
	}

	/**
	 * 获取求购属性值标
	 * @param 属性ID $filter_id
	 * @param 属性值ID $value_id
	 */
	public function purchaseAttributeTranslateFilter($filter_id, $value_id)
	{
		global $static;
		$translate = array(
			1 => 1,
			2 => 7,
			3 => 9
		);
		return $static['mobile'][$translate[$filter_id]][$value_id];
	}

	/**
	 * 获取求购所有筛选属性
	 */
	public function loadAllPurchaseAttribute()
	{
		return array(
			1 => '手机品牌',
			2 => '交易地区',
			3 => '卖家性质'
		);
	}
}
