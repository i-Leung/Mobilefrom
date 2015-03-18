<?php
class Resource_Relation_Mobile_Order extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_mobile_order';
	}
	
	/**
	 * 获取手机信息最近刷新时间
	 * @param 手机信息ID $id
	 * @return false或刷新时间
	 */
	public function getRefreshedAt($id)
	{
		$sql = 'select `refreshed_at` from `relation_mobile_order` 
				where mobile_id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取所有刷新信息
	 */
	public function loadAllOrder($page = 1, $per = 20)
	{
		$sql = 'select * from `relation_mobile_order` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}