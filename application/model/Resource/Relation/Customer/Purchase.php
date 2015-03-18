<?php
class Resource_Relation_Customer_Purchase extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_purchase';
	}
	
	/**
	 * 获取求购信息发布者ID
	 * @param 求购信息ID $id
	 * @return false或发布者ID
	 */
	public function getPublisher($id)
	{
		$sql = 'select `customer_id` from (
				select `customer_id` from `relation_customer_purchase` 
				where purchase_id = :id 
				union 
				select `customer_id` from `relation_customer_purchase_inactive` 
				where purchase_id = :in_id 
				) as `all_customer_purchase`  
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':in_id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取对应可用求购发布者信息记录
	 * @param 求购信息ID $id
	 */
	public function getActivePurchasePublisherRecord($id)
	{
		$sql = 'select * from `relation_customer_purchase` where purchase_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
