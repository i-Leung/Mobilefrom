<?php
class Resource_Relation_Purchase_Filter extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_purchase_filter_value';
	}

	/**
	 * 获取对应可用求购所有过滤器信息记录
	 * @param 求购信息ID $id
	 */
	public function getActivePurchaseFilterRecord($id)
	{
		$sql = 'select * from `relation_purchase_filter_value` where purchase_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 加载有效筛选条件
	 */
	public function loadAvailableFilterOption()
	{
		$sql = 'select filter_id, value_id from `relation_purchase_filter_value` 
				group by filter_id asc, value_id asc';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
