<?php
class Resource_Inactive_Mobile_Filter extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_mobile_filter_value_inactive';
	}

	/**
	 * 获取对应非可用手机所有过滤器信息记录
	 * @param 手机信息ID $id
	 */
	public function getInactiveMobileFilterRecord($id)
	{
		$sql = 'select * from `relation_mobile_filter_value_inactive` where mobile_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}
}
