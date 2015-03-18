<?php
class Resource_Relation_Customer_Mobile extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_mobile';
	}
	
	/**
	 * 获取手机信息发布者ID
	 * @param 手机信息ID $id
	 * @return false或发布者ID
	 */
	public function getPublisher($id)
	{
		$sql = 'select `customer_id` from (
				select `customer_id` from `relation_customer_mobile` 
				where mobile_id = :id 
				union 
				select `customer_id` from `relation_customer_mobile_inactive` 
				where mobile_id = :in_id 
				) as `all_customer_mobile`  
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':in_id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取对应可用手机发布者信息记录
	 * @param 手机信息ID $id
	 */
	public function getActiveMobilePublisherRecord($id)
	{
		$sql = 'select * from `relation_customer_mobile` where mobile_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
