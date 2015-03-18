<?php
class Resource_Customer extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'customer';
	}
	
	/**
	 * 判断用户名或邮箱存在性
	 * @param 用户名或邮箱 $customer
	 */
	public function isExist($customer)
	{
		$sql = 'select `id` from `customer` 
				where `username` = :username or `email` = :email 
				limit 1';
		$params = array(':username' => $customer, ':email' => $customer);
		return $this->fetchOne(
			$this->select($sql, $params)
		);
	}
	
	/**
	 * 检测登录信息是否正确
	 * @param 用户名/邮箱 $customer
	 * @param 密码 $pwd
	 * @return false或对应用户信息
	 */
	public function checkAuth($customer, $pwd)
	{
		$sql = 'select `id`, `username`, `group`, `verified` from `customer` 
				where (`username` = :username 
				or `email` = :email) 
				and `pwd` = :pwd 
				and `group` <> 99 
				limit 1';
		$params = array(':username' => $customer, ':email' => $customer, ':pwd' => $pwd);
		return $this->fetchRow(
			$this->select($sql, $params)
		);
	}
	
	/**
	 * 获取用户帐号信息
	 * @param 用户ID $customer_id
	 * @return false或用户信息
	 */
	public function getInfo($customer_id)
	{
		$sql = 'select `id`, `group`, `email`, `username`, `registered_at`, `logon_times`, `logon_latest`, `verified` 
				from `customer` 
				where `id` = :customer_id 
				limit 1';
		$params = array(':customer_id' => $customer_id);
		return $this->fetchRow(
				$this->select($sql, $params)
		);
	}
	
	/**
	 * 获取用户联系信息
	 * @param 用户ID $customer_id
	 * @return false或用户联系信息
	 */
	public function getContact($customer_id)
	{
		$sql = 'select `contact` 
				from `customer` 
				where `id` = :customer_id 
				limit 1';
		$params = array(':customer_id' => $customer_id);
		return $this->fetchOne(
				$this->select($sql, $params)
		);
	}
	
	/**
	 * 获取用户列表
	 * @param 参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadCustomerList($params, $page = 1, $per = 10)
	{
		//过滤
		$filter = $this->_getCustomerListFilter($params['group'], $params['search']);
		//排序
		$order = '';
		if ($params['sequence']) {
			switch ($params['sequence']) {
				case 'id':
					$order = 'order by `id` desc';
					break;
				case 'logon_latest':
					$order = 'order by `logon_latest` desc';
					break;
				case 'logon_times':
					$order = 'order by `logon_times` desc';
					break;
			}
		}
		$sql = 'select `id`, `username`, `email`, `group`, `registered_at`, `logon_times`, `logon_latest` 
			from `customer` ' . $filter['where'] . ' ' . $order . ' 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $filter['params']);
		return $this->fetchAll($select);
	}

	/**
	 * 获取商家列表
	 * @param 参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadStoreList($region = null, $page = 1, $per = 10)
	{
		$where = '';
		$params = array();
		if ($region) {
			$where = ' and b.region = :region ';
			$params['region'] = $region;
		}
		$sql = 'select a.id, a.username, b.name, b.created_at, b.status 
				from `customer` as a 
				left join `store` as b on b.customer_id = a.id 
				where a.group = 2 ' . $where . '
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取商家总数
	 * @param 所在地区 $region 
	 */
	public function getStoreTotal($region = null)
	{
		$where = '';
		$params = array();
		if ($region) {
			$where = ' and b.region = :region ';
			$params['region'] = $region;
		}
		$sql = 'select count(id) from `customer` where a.group = 2 ' . $where;
		$select = $this->select($sql, $params);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户总数
	 * @param 参数 $params
	 */
	public function getCustomerTotal($params)
	{
		//过滤
		$filter = $this->_getCustomerListFilter($params['group'], $params['search']);
		$sql = 'select count(`id`) from `customer` ' . $filter['where'];
		$select = $this->select($sql, $filter['params']);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户列表where内容
	 * @param 所属群组 $group
	 * @param 搜索内容 $search
	 * @return where内容
	 */
	private function _getCustomerListFilter($group, $search)
	{
		$where = array();//where语句
		$params = array();//where参数
		if ($group) {//筛选群组
			$where[] = '(`group` = :group)';
			$params[':group'] = $group;
		}
		if ($search) {//搜索用户名或邮箱
			$where[] = '(`username` like :search or `email` like :search)';
			$params[':search'] = '%' . $search . '%';
		}
		$where = $where ? 'where ' . implode(' and ', $where) : '';
		$params = $params ? $params : null;
		return array('where' => $where, 'params' => $params);
	}
	
	/**
	 * 获取所有用户
	 */
	public function loadAllCustomer($page = 1, $per = 20)
	{
		$sql = 'select * from `customer` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 检测用户名、邮箱是否匹配
	 * 用于找回密码
	 * @param 用户名 $username
	 * @param 邮箱地址 $email
	 * @return boolean
	 */
	public function checkUsernameEmailMatch($username, $email)
	{
		$sql = 'select id from `customer` where username = :username and email = :email limit 1';
		$select = $this->select($sql, array(':username' => $username, ':email' => $email));
		return $this->fetchOne($select);
	}

	/**
	 * 根据第三方凭证获取用户信息
	 * @param 绑定凭证 $binding
	 * @param 第三方平台 $third
	 */
	public function getCustomerByThirdParty($binding, $third)
	{
		$where = '';
		switch ($third) {
			case 'sina':
				$where = 'where `sina` = :binding and `sina` <> 0';
				break;
			case 'tencent':
				$where = 'where `tencent` = :binding and `tencent` <> "0"';
				break;
			default:
				return false;
		}
		$sql = 'select `id`, `username`, `group` from `customer` ' . $where . ' limit 1';
		$select = $this->select($sql, array(':binding' => $binding));
		return $this->fetchRow($select);
	}

	/**
	 * 获取用户注册邮箱
	 * @param 用户ID $id
	 */
	public function getEmail($id)
	{
		$sql = 'select `email` from `customer` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取用户第三方帐号绑定情况
	 * @param 用户ID $id
	 */
	public function getCustomerThirdParty($id)
	{
		$sql = 'select `sina`, `tencent` from `customer` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取本地商家列表
	 */
	public function loadLocalStore()
	{
		$sql = 'select `id`, `username`, `contact` from `customer` where `group` = 2';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
