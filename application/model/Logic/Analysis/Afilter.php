<?php
class Logic_Analysis_Afilter
{
	private $_resource_attribute_filter = null;

	public function __construct()
	{
		$this->_resource_attribute_filter = Factory::getResource('analysis/afilter');
	}

	/**
	 * 创建当天页面导向统计记录
	 * @param 筛选属性ID $filter_id
	 * @param 筛选值ID $value_id
	 * @param 筛选对象类型 $type
	 */
	public function createAttributeFilter($filter_id, $value_id, $type)
	{
		return $this->_resource_attribute_filter->create(
			array(
				'type' => $type,
				'filter_id' => $filter_id,
				'value_id' => $value_id
			)
		);
	}

	/**
	 * 增加导向量
	 * @param 记录ID $id
	 */
	public function modifyAttributeFilter($id)
	{
		return $this->_resource_attribute_filter->modify(
			array('id = "?"' => $id),
			array('amount' => '`amount` + 1')
		);
	}

	/**
	 * 检测是否有该统计记录 
	 * @param 筛选属性ID $filter_id
	 * @param 筛选值ID $value_id
	 * @param 筛选对象类型 $type
	 */
	public function hasAttributeFilter($filter_id, $value_id, $type)
	{
		return $this->_resource_attribute_filter
					->hasAttributeFilter($filter_id, $value_id, $type);
	}

	/**
	 * 属性筛选统计标记
	 * @param 筛选属性ID $filter_id
	 * @param 筛选值ID $value_id
	 * @param 筛选对象类型 $type
	 */
	public function markAttributeFilter($filter_id, $value_id, $type)
	{
		if (!in_array(Factory::getCookie('customer_id'), array(1, 2, 10))) {
			if ($id = $this->hasAttributeFilter($filter_id, $value_id, $type)) {
				return $this->modifyAttributeFilter($id);
			} else {
				return $this->createAttributeFilter($filter_id, $value_id, $type);
			}
		}
	}
}
