<?php
class Resource_Analysis_Afilter extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'analysis_attribute_filter';
	}

	/**
	 * 检测是否有该统计记录 
	 * @param 筛选属性ID $filter_id
	 * @param 筛选值ID $value_id
	 * @param 筛选对象类型 $type
	 */
	public function hasAttributeFilter($filter_id, $value_id, $type)
	{
		$sql = 'select id from `analysis_attribute_filter` 
				where `type` = :type and `filter_id` = :filter_id 
				and `value_id` = :value_id 
				limit 1';
		$select = $this->select(
					$sql, 
					array(
						':type' => $type,
						':filter_id' => $filter_id,
						':value_id' => $value_id
					)
				);
		return $this->fetchOne($select);
	}

	/**
	 * 加载信息属性筛选记录列表
	 * @param 信息类型 $type
	 * @param 属性ID $filter_id
	 */
	public function loadAttributeFilterRecord($type, $filter_id)
	{
		$where = 'where `type` = :type';
		$params = array(':type' => $type);
		if ($filter_id) {
			$where = $where . ' and `filter_id` = :filter';
			$params[':filter'] = $filter_id;
		}
		$sql = 'select * from `analysis_attribute_filter` ' . $where . ' 
				group by `filter_id`, `value_id` 
				order by `filter_id` asc, `amount` desc'; 
		$select = $this->select(
					$sql, $params
				);
		return $this->fetchAll($select);
	}
}
