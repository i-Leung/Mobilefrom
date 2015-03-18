<?php
class Resource_Inactive_Purchase extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'purchase_inactive';
	}

	/**
	 * 获取对应非可用求购信息记录
	 * @param 求购信息ID $id
	 */
	public function getInactivePurchaseRecord($id)
	{
		$sql = 'select * from `purchase_inactive` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取我所发布过期求购列表
	 * @param 用户ID $customer_id
	 * @return false或求购列表
	 */
	public function loadMyInactivePurchase($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.clicks, a.created_at 
			from `purchase_inactive` as a 
			left join `relation_customer_purchase_inactive` as b on b.purchase_id = a.id 
			where b.customer_id = :customer and a.status = 2 
			order by a.created_at desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取我所发布过期求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyInactivePurchaseTotal($customer_id)
	{
		$sql = 'select count(a.id) 
			from `purchase_inactive` as a 
			left join `relation_customer_purchase_inactive` as b on b.purchase_id = a.id 
			where b.customer_id = :customer 
			and a.status = 2';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
}
