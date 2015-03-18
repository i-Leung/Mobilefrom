<?php
class Resource_Inactive_Purchase_Customer extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_purchase_inactive';
	}

	/**
	 * 获取对应非可用手机发布者信息记录
	 * @param 手机信息ID $id
	 */
	public function getInactivePurchasePublisherRecord($id)
	{
		$sql = 'select * from `relation_customer_purchase_inactive` where purchase_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
