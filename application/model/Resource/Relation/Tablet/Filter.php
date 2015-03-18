<?php
class Resource_Relation_Tablet_Filter extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_tablet_filter_value';
	}

	/**
	 * 获取对应可用手机所有过滤器信息记录
	 * @param 手机信息ID $id
	 */
	public function getActiveTabletFilterRecord($id)
	{
		$sql = 'select * from `relation_tablet_filter_value` where tablet_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 加载有效筛选条件
	 */
	public function loadAvailableFilterOption()
	{
		$sql = 'select filter_id, value_id from `relation_tablet_filter_value` 
				group by filter_id asc, value_id asc';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 加载前五手机品牌
	 * @param 排名数量 $num
	 */
	public function loadTopBrandList($num = null)
	{
		$num = $num ? 'limit ' . $num : '';
		$sql = 'select value_id, count(value_id) as amount from `relation_tablet_filter_value` 
				where filter_id = 2 and value_id <> 15  
				group by value_id 
				order by amount desc ' . $num;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取闲置手机型号ID
	 * @param 手机ID $id
	 */
	public function getTabletModelId($id)
	{
		$sql = 'select value_id 
				from `relation_tablet_filter_value` 
				where tablet_id = :id and filter_id = 8 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取闲置手机所有型号信息
	 */
	public function loadAllTabletModelId()
	{
		$sql = 'select value_id, count(value_id) as amount 
				from `relation_tablet_filter_value` 
				where filter_id = 8 
				group by value_id';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
