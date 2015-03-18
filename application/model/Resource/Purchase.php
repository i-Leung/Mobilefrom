<?php
class Resource_Purchase extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'purchase';
	}

	/**
	 * 判断信息是否可用
	 * @param 求购信息ID $id
	 * @return boolean
	 */
	public function isActivePurchase($id)
	{
		$sql = 'select `id` from `purchase` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取求购信息详细
	 * @param 求购信息ID $id
	 * @return false或求购信息详细
	 */
	public function getInfo($id)
	{
		$sql = 'select * from (
				select `id`, `title`, `brand`, `price`, `amount`, `remark`, `contact` 
				from `purchase` 
				where id = :id 
				union 
				select `id`, `title`, `brand`, `price`, `amount`, `remark`, `contact` 
				from `purchase_inactive` 
				where id = :in_id 
				) as `all_purchase` 
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':in_id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 求购详细页显示信息
	 * @param 求购信息ID $id
	 * @return false或求购信息详细
	 */
	public function showInfo($id)
	{
		$sql = 'select a.id, a.title, a.brand, a.price, a.amount, a.remark, a.created_at, a.clicks, b.customer_id 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取求购概述信息
	 * @param 求购信息ID $id
	 * @return false或求购概述详细
	 */
	public function getSummarize($id)
	{
		$sql = 'select id, title 
				from `purchase` 
				where id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 求购详细页显示联系信息
	 * @param 求购信息ID $id
	 * @return false或求购联系信息
	 */
	public function showContact($id)
	{
		$sql = 'select a.id, a.contact, c.id as customer_id, c.username, c.email, c.group 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				left join `customer` as c on c.id = b.customer_id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取我所发布求购列表
	 * @param 用户ID $customer_id
	 * @return false或求购列表
	 */
	public function loadMyPurchase($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.clicks, a.created_at 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where b.customer_id = :customer 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取我所发布求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyPurchaseTotal($customer_id)
	{
		$sql = 'select count(a.id) 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where b.customer_id = :customer';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户发布求购列表
	 * @param 用户ID $customer_id
	 * @return false或求购列表
	 */
	public function loadCustomerPurchase($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where b.customer_id = :customer 
				order by a.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户发布求购总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getCustomerPurchaseTotal($customer_id)
	{
		$sql = 'select count(a.id) 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where b.customer_id = :customer';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取可见求购列表
	 * @return false或求购列表
	 */
	public function loadVisiblePurchase($page = 1, $per = 10)
	{
		$where = $this->getVisibleSql();
		$sql = 'select a.id, a.title, a.price, a.amount, a.created_at, a.contact, c.id as customer_id, c.username, c.group 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				left join `customer` as c on c.id = b.customer_id 
				left join `relation_purchase_order` as d on d.purchase_id = a.id 
				 ' . $where . ' 
				order by d.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取可见求购总量
	 * @return false或总量
	 */
	public function getVisiblePurchaseTotal()
	{
		$where = $this->getVisibleSql();
		$sql = 'select count(a.id) 
				from `purchase` as a ' . $where;
		$select = $this->select($sql);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取最新求购列表
	 * @return false或求购列表
	 */
	public function loadNewPurchase($page = 1, $per = 8)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at 
				from `purchase` as a 
				left join `relation_purchase_order` as b on b.purchase_id = a.id 
				order by b.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取过滤器sql
	 */
	public function getVisibleSql()
	{
		$sql = $fsql = $csql = '';
		$filters = Factory::getSession('pfilter');
		if (!empty($filters)) {
			foreach ($filters as $filter_id => $value_id) {
				$csql = 'select purchase_id from `relation_purchase_filter_value` where ';
				$csql .= 'filter_id = ' . $filter_id . ' and value_id = "' . $value_id . '"';
				if (!$fsql) {//若元素为第一个子元素则赋值，否则嵌套子查询
					$fsql = $csql;
				} else {
					$fsql = $csql . ' and purchase_id in (' . $fsql . ')';
				}
			}
			$sql .= ' where a.id in (' . $fsql . ')';
		}
		return $sql;
	}
	
	/**
	 * 获取用户其它发布求购信息
	 * @param 用户ID $customer_id
	 * @param 已查看求购ID $purchase_id
	 * @return false或求购列表
	 */
	public function loadCustomerOther($customer_id, $purchase_id)
	{
		$sql = 'select a.id, a.title, a.price 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				where b.customer_id = :customer and a.id <> :purchase_id 
				order by a.id desc 
				limit 1';
		$select = $this->select($sql, array(':customer' => $customer_id, ':purchase_id' => $purchase_id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取搜索求购列表
	 * @param 搜索内容 $q
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadSearchPurchase($q, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.amount, a.created_at, a.contact, c.id as customer_id, c.username, c.group 
				from `purchase` as a 
				left join `relation_customer_purchase` as b on b.purchase_id = a.id 
				left join `customer` as c on c.id = b.customer_id 
				left join `relation_purchase_order` as d on d.purchase_id = a.id 
				where a.title like :q 
				order by d.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取搜索求购总量
	 * @param 搜索内容 $q
	 * @return false或总量
	 */
	public function getSearchPurchaseTotal($q)
	{
		$sql = 'select count(id) 
				from `purchase` 
				where title like :q';
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取求购刷新信息
	 * @param 求购信息ID $id
	 */
	public function getLastRefreshedAt($id)
	{
		$sql = 'select created_at from `purchase` where id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 后台获取用户所有发布求购列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或求购列表
	 */
	public function loadAdminCustomerPurchase($customer_id, $params, $page = 1, $per = 10)
	{
		$where = array();
		$filter = array();
		if ($customer_id) {
			$where[] = 'b.customer_id = :customer_id';
			$filter[':customer_id'] = $customer_id;	
		}
		if ($params['search']) {
			$where[] = 'a.title like :title';
			$filter[':title'] = '%' . $params['search'] . '%';	
		}
		$where = $where ? 'where ' . implode(' and ', $where) : '';
		$sql = 'select a.`id`, a.`title`, a.`clicks`, a.`created_at` 
			from `purchase` as a 
			left join `relation_customer_purchase` as b on b.purchase_id = a.id 
			' . $where . ' 
			order by a.`id` desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $filter);
		return $this->fetchAll($select);
	}

	/**
	 * 后台获取用户所发布求购总量
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @return false或总量
	 */
	public function getAdminCustomerPurchaseTotal($customer_id, $params)
	{
		$where = array();
		$filter = array();
		if ($customer_id) {
			$where[] = 'b.customer_id = :customer_id';
			$filter[':customer_id'] = $customer_id;	
		}
		if ($params['search']) {
			$where[] = 'a.title like :title';
			$filter[':title'] = '%' . $params['search'] . '%';	
		}
		$where = $where ? 'where ' . implode(' and ', $where) : '';
		$sql = 'select count(a.`id`) 
			from `purchase` as a 
			left join `relation_customer_purchase` as b on b.purchase_id = a.id 
			' . $where; 
		$select = $this->select($sql, $filter);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取所有求购列表
	 * @return false或求购列表
	 */
	public function loadAllPurchase($page = 1, $per = 10)
	{
		$sql = 'select * from `purchase` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 根据提供的ID集合获取对应求购信息列表
	 * @param ID集合数组 $ids
	 */
	public function loadPurchaseByIds($ids)
	{
		$sql = 'select id, title, price from `purchase` 
				where id in (' . $ids . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取对应可用求购信息记录
	 * @param 求购信息ID $id
	 */
	public function getActivePurchaseRecord($id)
	{
		$sql = 'select * from `purchase` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 加载所有过期求购记录
	 * @param 过期时间 $expired_at
	 */
	public function loadExpiredPurchase($expired_at)
	{
		$sql = 'select * from `purchase` 
				where created_at <= "' . $expired_at . '"';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
