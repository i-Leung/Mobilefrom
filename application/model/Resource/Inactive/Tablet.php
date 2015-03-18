<?php
class Resource_Inactive_Tablet extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'tablet_inactive';
	}

	/**
	 * 获取对应非可用手机信息记录
	 * @param 手机信息ID $id
	 */
	public function getInactiveTabletRecord($id)
	{
		$sql = 'select * from `tablet_inactive` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取我所发布过期手机列表
	 * @param 用户ID $customer_id
	 * @return false或手机列表
	 */
	public function loadMyInactiveTablet($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.clicks, a.status, a.created_at 
			from `tablet_inactive` as a 
			left join `relation_customer_tablet_inactive` as b on b.tablet_id = a.id 
			where b.customer_id = :customer and a.status = 2 
			order by a.created_at desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取我所发布过期手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyInactiveTabletTotal($customer_id)
	{
		$sql = 'select count(a.id) 
			from `tablet_inactive` as a 
			left join `relation_customer_tablet_inactive` as b on b.tablet_id = a.id 
			where b.customer_id = :customer 
			and a.status = 2';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
}
