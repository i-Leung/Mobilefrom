<?php
class Resource_Inactive_Mobile extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mobile_inactive';
	}

	/**
	 * 获取对应非可用手机信息记录
	 * @param 手机信息ID $id
	 */
	public function getInactiveMobileRecord($id)
	{
		$sql = 'select * from `mobile_inactive` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取我所发布过期手机列表
	 * @param 用户ID $customer_id
	 * @return false或手机列表
	 */
	public function loadMyInactiveMobile($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.clicks, a.status, a.created_at 
			from `mobile_inactive` as a 
			left join `relation_customer_mobile_inactive` as b on b.mobile_id = a.id 
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
	public function getMyInactiveMobileTotal($customer_id)
	{
		$sql = 'select count(a.id) 
			from `mobile_inactive` as a 
			left join `relation_customer_mobile_inactive` as b on b.mobile_id = a.id 
			where b.customer_id = :customer 
			and a.status = 2';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
}
