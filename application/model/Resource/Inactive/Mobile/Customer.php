<?php
class Resource_Inactive_Mobile_Customer extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_mobile_inactive';
	}

	/**
	 * 获取对应非可用手机发布者信息记录
	 * @param 手机信息ID $id
	 */
	public function getInactiveMobilePublisherRecord($id)
	{
		$sql = 'select * from `relation_customer_mobile_inactive` where mobile_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
