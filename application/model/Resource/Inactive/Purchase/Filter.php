<?php
class Resource_Inactive_Purchase_Filter extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_purchase_filter_value_inactive';
	}

	/**
	 * 获取对应非可用求购所有过滤器信息记录
	 * @param 求购信息ID $id
	 */
	public function getInactivePurchaseFilterRecord($id)
	{
		$sql = 'select * from `relation_purchase_filter_value_inactive` where purchase_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}
}
