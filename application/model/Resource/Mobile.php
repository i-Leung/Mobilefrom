<?php
class Resource_Mobile extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mobile';
	}

	/**
	 * 判断信息是否可用
	 * @param 手机信息ID $id
	 * @return boolean
	 */
	public function isActiveMobile($id)
	{
		$sql = 'select `id` from `mobile` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取手机信息详细
	 * @param 手机信息ID $id
	 * @return false或手机信息详细
	 */
	public function getInfo($id)
	{
		$sql = 'select * from (
				select `id`, `title`, `thumb`, `price`, `gallery`, `data`, `contact` 
				from `mobile` 
				where id = :id 
				union 
				select `id`, `title`, `thumb`, `price`, `gallery`, `data`, `contact` 
				from `mobile_inactive` 
				where id = :in_id 
				) as `all_mobile` 
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':in_id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 手机详细页显示信息
	 * @param 手机信息ID $id
	 * @return false或手机信息详细
	 */
	public function showInfo($id)
	{
		$sql = 'select a.`id`, a.`title`, a.`thumb`, a.`price`, 
				a.`gallery`, a.`data`, a.`created_at`, a.`clicks`, b.`customer_id` 
				from `mobile` as a 
				left join `relation_customer_mobile` as b on b.mobile_id = a.id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取手机概述信息
	 * @param 手机信息ID $id
	 * @return false或手机概述详细
	 */
	public function getSummarize($id)
	{
		$sql = 'select `id`, `title`, `thumb`, `price` 
			from `mobile` 
			where id = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 手机详细页显示联系信息
	 * @param 手机信息ID $id
	 * @return false或手机联系信息
	 */
	public function showContact($id)
	{
		$sql = 'select a.id, a.contact, c.id as customer_id, c.username, c.email, c.group, c.verified 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			left join `customer` as c on c.id = b.customer_id 
			where a.id = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取我所发布手机列表
	 * @param 用户ID $customer_id
	 * @return false或手机列表
	 */
	public function loadMyMobile($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.clicks, a.created_at 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			left join `relation_mobile_order` as c on c.mobile_id = a.id 
			where b.customer_id = :customer 
			order by c.id desc  
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取我所发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyMobileTotal($customer_id)
	{
		$sql = 'select count(a.id) 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			where b.customer_id = :customer';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户发布手机列表
	 * @param 用户ID $customer_id
	 * @return false或手机列表
	 */
	public function loadCustomerMobile($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			where b.customer_id = :customer 
			order by a.id desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getCustomerMobileTotal($customer_id)
	{
		$sql = 'select count(a.id) 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			where b.customer_id = :customer';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 后台获取用户所有发布手机列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadAdminCustomerMobile($customer_id, $params, $page = 1, $per = 10)
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
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			' . $where . ' 
			order by a.`id` desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $filter);
		return $this->fetchAll($select);
	}

	/**
	 * 后台获取用户所有发布手机列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @return false或手机列表
	 */
	public function getAdminCustomerMobileTotal($customer_id, $params)
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
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			' . $where; 
		$select = $this->select($sql, $filter);
		return $this->fetchOne($select);
	}

	/**
	 * 获取可见手机列表
	 * @return false或手机列表
	 */
	public function loadVisibleMobileByList($page = 1, $per = 20)
	{
		$where = $this->getVisibleSql();
		$order = '';
		//排序方式
		switch (Factory::getSession('morder')) {
			case 'price':
				$order = 'a.price asc';
				break;
			default:
				$order = 'd.id desc';
				break;
		}
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb, a.data, a.contact, c.`group` 
				from `mobile` as a 
				left join `relation_customer_mobile` as b on b.mobile_id = a.id 
				left join `customer` as c on c.id = b.customer_id 
				left join `relation_mobile_order` as d on d.mobile_id = a.id 
				 ' . $where . ' 
				order by ' . $order . ' 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取可见手机列表
	 * @return false或手机列表
	 */
	public function loadVisibleMobileByGrid($page = 1, $per = 20)
	{
		$where = $this->getVisibleSql();
		$order = '';
		//排序方式
		switch (Factory::getSession('morder')) {
			case 'price':
				$order = 'a.price asc';
				break;
			default:
				$order = 'c.id desc';
				break;
		}
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb, a.contact 
				from `mobile` as a 
				left join `relation_customer_mobile` as b on b.mobile_id = a.id 
				left join `relation_mobile_order` as c on c.mobile_id = a.id 
				 ' . $where . ' 
				order by ' . $order . ' 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取可见手机总量
	 * @return false或总量
	 */
	public function getVisibleMobileTotal()
	{
		$where = $this->getVisibleSql();
		$sql = 'select count(a.id) 
			from `mobile` as a ' . $where;
		$select = $this->select($sql);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取最新手机列表
	 * @return false或手机列表
	 */
	public function loadNewMobile($page = 1, $per = 8)
	{
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb, a.contact 
				from `mobile` as a 
				left join `relation_mobile_order` as b on b.mobile_id = a.id 
				where a.thumb <> "" 
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
		$filters = Factory::getSession('mfilter');
		if (!empty($filters)) {
			foreach ($filters as $filter_id => $value_id) {
				$csql = 'select mobile_id from `relation_mobile_filter_value` where ';
				$csql .= 'filter_id = ' . $filter_id . ' and value_id = "' . $value_id . '"';
				if (!$fsql) {//若元素为第一个子元素则赋值，否则嵌套子查询
					$fsql = $csql;
				} else {
					$fsql = $csql . ' and mobile_id in (' . $fsql . ')';
				}
			}
			$sql .= ' where a.id in (' . $fsql . ')';
		}
		return $sql;
	}
	
	/**
	 * 获取用户其它发布手机信息
	 * @param 用户ID $customer_id
	 * @param 已查看手机ID $mobile_id
	 * @return false或手机列表
	 */
	public function loadCustomerOther($customer_id, $mobile_id)
	{
		$sql = 'select a.id, a.title, a.price, a.thumb 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			where b.customer_id = :customer and a.id <> :mobile_id 
			order by a.id desc 
			limit 1';
		$select = $this->select($sql, array(':customer' => $customer_id, ':mobile_id' => $mobile_id));
		return $this->fetchRow($select);
	}
	
	/**
	 * ajax加载对比列表窗口
	 * @param 对比手机ID字符串 $compare
	 */
	public function loadCompareOutline($compare)
	{
		$sql = 'select id, title, thumb 
			from `mobile` 
			where id in (' . $compare . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
	
	/**
	 * ajax加载对比列表
	 * @param 对比手机ID字符串 $compare
	 */
	public function showCompare($compare)
	{
		$sql = 'select a.id, a.title, a.thumb, a.price, a.data, a.contact, c.group 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			left join `customer` as c on c.id = b.customer_id 
			where a.id in (' . $compare . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取搜索手机列表显示为列模式
	 * @param 搜索内容 $q
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadSearchMobileByList($q, $page = 1, $per = 20)
	{
		$order = null;
		switch (Factory::getSession('smorder')) {
			case 'refreshed':
				$order = 'd.id desc';
				break;
			case 'price':
				$order = 'a.price asc';
				break;
		}
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb, a.data, a.contact, c.group 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			left join `customer` as c on c.id = b.customer_id 
			left join `relation_mobile_order` as d on d.mobile_id = a.id 
			where a.title like :q 
			order by ' . $order . ' 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取搜索手机列表显示为块模式
	 * @param 搜索内容 $q
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadSearchMobileByGrid($q, $page = 1, $per = 20)
	{
		$order = null;
		switch (Factory::getSession('smorder')) {
			case 'refreshed':
				$order = 'd.id desc';
				break;
			case 'price':
				$order = 'a.price asc';
				break;
		}
		$sql = 'select a.id, a.title, a.price, a.created_at, a.thumb, a.contact, c.group 
			from `mobile` as a 
			left join `relation_customer_mobile` as b on b.mobile_id = a.id 
			left join `customer` as c on c.id = b.customer_id 
			left join `relation_mobile_order` as d on d.mobile_id = a.id 
			where a.title like :q 
			order by ' . $order . ' 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取搜索手机总量
	 * @param 搜索内容 $q
	 * @return false或总量
	 */
	public function getSearchMobileTotal($q)
	{
		$sql = 'select count(id) 
			from `mobile` 
			where title like :q';
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取手机刷新信息
	 * @param 手机信息ID $id
	 */
	public function getLastRefreshedAt($id)
	{
		$sql = 'select created_at from `mobile` where id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取所有手机列表
	 * @return false或手机列表
	 */
	public function loadAllMobile($page = 1, $per = 20)
	{
		$sql = 'select * from `mobile` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 根据提供的ID集合获取对应闲置手机列表
	 * @param ID集合数组 $ids
	 */
	public function loadMobileByIds($ids)
	{
		$sql = 'select id, title, thumb, price from `mobile` 
				where id in (' . $ids . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取对应可用手机信息记录
	 * @param 手机信息ID $id
	 */
	public function getActiveMobileRecord($id)
	{
		$sql = 'select * from `mobile` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 加载所有过期手机记录
	 * @param 过期时间 $expired_at
	 */
	public function loadExpiredMobile($expired_at)
	{
		$sql = 'select * from `mobile` 
				where created_at <= "' . $expired_at . '"'; 
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 加载推荐手机
	 * @param 当前手机ID $id
	 * @param 型号ID $model
	 * @param 品牌ID $brand
	 * @param 价格范围ID $level
	 */
	public function loadRecommendMobile($id, $model, $brand, $level)
	{
		$sql = 'select distinct a.id, a.title, a.price, a.created_at, a.thumb 
				from `mobile` as a 
				left join `relation_mobile_order` as b on b.mobile_id = a.id 
				where a.id in (
					select mobile_id from `relation_mobile_filter_value` where filter_id = 8 and value_id = :model 
					union 
					select mobile_id from `relation_mobile_filter_value` where filter_id = 2 and value_id = :brand 
					union 
					select mobile_id from `relation_mobile_filter_value` where filter_id = 1 and value_id = :level 
				) and a.id <> :id 
				order by b.id desc 
				limit 16';
		$select = $this->select($sql, array(':id' => $id, ':brand' => $brand, ':model' => $model, ':level' => $level));
		return $this->fetchAll($select);
	}

	/**
	 * 总手机信息量
	 */
	public function getAllTotal()
	{
		$sql = 'select sum(total) from (
					select count(id) as total from `mobile` 
					union 
					select count(id) as total from `mobile_inactive`
				) as tmp'; 
		$select = $this->select($sql);
		return $this->fetchOne($select);
	}

	/**
	 * 可见手机总量
	 */
	public function getAllVisibleTotal()
	{
		$sql = 'select count(id) as total from `mobile`';
		$select = $this->select($sql);
		return $this->fetchOne($select);
	}

	/**
	 * 获取同型号手机列表
	 * @param 手机型号ID $id
	 */
	public function loadModelMobileList($id)
	{
		$sql = 'select a.id, a.title, a.thumb, a.price, a.created_at, a.contact 
				from `mobile` as a 
				left join `relation_mobile_filter_value` as b on b.mobile_id = a.id 
				where b.filter_id = 8 and b.value_id = :id 
				order by a.created_at desc';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}
}
