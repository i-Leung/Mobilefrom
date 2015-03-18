<?php
class Resource_Relation_Customer_Favor extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_favor';
	}
	
	/**
	 * 获取用户关注列表
	 */
	public function getFavorAmount($customer)
	{
		$sql = 'select count(topic_id) from (
				select a.topic_id 
				from `relation_customer_favor` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.customer_id = :mcustomer 
				union all 
				select a.topic_id 
				from `relation_customer_favor` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.customer_id = :pcustomer 
				) as result'; 
		$select = $this->select($sql, array(':mcustomer' => $customer, ':pcustomer' => $customer));
		return $this->fetchOne($select);
	}
	
	/**
	 * 用户关注列表
	 * @param 用户ID $customer_id
	 * @param 信息类型 $type
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadFavorType($customer_id, $type, $page = 1, $per = 10)
	{
		switch ($type) {
			case '1':
				$sql = 'select a.id, a.title, a.price, b.favor_at 
						from `mobile` as a 
						left join `relation_customer_favor` as b on b.topic_id = a.id 
						where b.customer_id = :customer_id and b.type = :type 
						order by b.favor_at 
						limit ' . $per . ' offset ' . ($page - 1) * $per;
				break;
			case '2':
				$sql = 'select a.id, a.title, a.price, b.favor_at 
						from `purchase` as a 
						left join `relation_customer_favor` as b on b.topic_id = a.id 
						where b.customer_id = :customer_id and b.type = :type 
						order by b.favor_at 
						limit ' . $per . ' offset ' . ($page - 1) * $per;
				break;
		}
		$select = $this->select($sql, array(':customer_id' => $customer_id, ':type' => $type));
		return $this->fetchAll($select);
	}
	
	/**
	 * 用户关注总数
	 * @param 用户ID $customer_id
	 * @param 信息类型 $type
	 */
	public function getFavorTypeTotal($customer_id, $type)
	{
		$sql = 'select count(*) 
				from `relation_customer_favor` 
				where customer_id = :customer_id and type = :type';
		$select = $this->select($sql, array(':customer_id' => $customer_id, ':type' => $type));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取所有关注信息
	 */
	public function loadAllFavor($page = 1, $per = 20)
	{
		$sql = 'select * from `relation_customer_favor` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 是否已收藏
	 * @param 用户ID $customer_id
	 * @param 类型ID $type
	 * @param 主题ID $id
	 */
	public function hasFavor($customer_id, $type, $id)
	{
		$sql = 'select topic_id from `relation_customer_favor` 
				where customer_id = :customer and type = :type and topic_id = :id 
				limit 1';
		$select = $this->select($sql, array(':customer' => $customer_id, 'type' => $type, ':id' => $id));
		return $this->fetchOne($select);
	}
}
