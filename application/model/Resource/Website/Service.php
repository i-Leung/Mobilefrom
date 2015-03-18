<?php
class Resource_Website_Service extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'local_service';
	}

	/**
	 * 判断对服务是否有操作权
	 * @param 服务ID $id
	 * @param 用户ID $customer
	 */
	public function isMyService($id, $customer)
	{
		$sql = 'select id from local_service 
				where id = :id and customer_id = :customer 
				limit 1';
		return $this->fetchOne(
				$this->select($sql, array(':id' => $id, ':customer' => $customer))
		);
	}

	/**
	 * 获取用户服务列表
	 * @param 用户ID $id
	 */
	public function loadCustomerService($id)
	{
		$sql = 'select id, sid, price, introduction from local_service 
				where customer_id = :customer';
		return $this->fetchAll(
				$this->select($sql, array(':customer' => $id))
		);
	}

	/**
	 * 获取服务内容
	 * @param 服务类型ID $sid
	 * @param 用户ID $customer_id
	 */
	public function getService($sid, $customer_id)
	{
		$sql = 'select id, customer_id, price, introduction, contact from local_service 
				where sid = :sid and customer_id = :customer 
				limit 1';
		return $this->fetchRow(
				$this->select($sql, array('sid' => $sid, ':customer' => $customer_id))
		);
	}

	/**
	 * 获取地区提供服务列表
	 * @param 地区ID $region
	 */
	public function loadRegionService($region)
	{
		$sql = 'select a.id, a.sid, a.customer_id, b.username, a.price from local_service as a 
				left join customer as b on b.id = a.customer_id 
				where region = :region';
		return $this->fetchAll(
				$this->select($sql, array(':region' => $region))
		);
	}
}
