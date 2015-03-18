<?php
class Resource_Store_Activity extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_activity';
	}

	/**
	 * 获取商家所有活动列表
	 * @param 店铺ID $store
	 */
	public function loadMyActivityList($store)
	{
		$sql = 'select id, type, title, `limit`, status 
				from store_activity 
				where store_id = :store';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺活动列表
	 * @param 活动ID $id
	 * @param 店铺ID $store
	 */
	public function getMyActivityInfo($id, $store)
	{
		$sql = 'select id, type, title, `limit`, thumb, gallery, description, status 
				from store_activity 
				where id = :id and store_id = :store 
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':store' => $store));
		return $this->fetchRow($select);
	}

	/**
	 * 店铺是否已参与活动
	 * @param 店铺ID $store
	 */
	public function hasActiveActivity($store)
	{
		$sql = 'select id 
				from store_activity 
				where store_id = :store and status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				limit 1';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchOne($select);
	}

	/**
	 * 判断是否有效活动
	 * @param 店铺ID $store
	 * @param 活动ID $id
	 */
	public function isActiveActivity($store, $id)
	{
		$sql = 'select id 
				from store_activity 
				where id = :id and store_id = :store and status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				limit 1';
		$select = $this->select($sql, array(':store' => $store, ':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 判断是否我的活动
	 * @param 店铺ID $store
	 * @param 活动ID $id
	 */
	public function isMyActivity($store, $id)
	{
		$sql = 'select id 
				from store_activity 
				where id = :id and store_id = :store 
				limit 1';
		$select = $this->select($sql, array(':store' => $store, ':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取活动列表
	 * @param 地区 $region
	 * @param 类型 $type
	 * @param 排序器 $sort
	 */
	public function loadStoreActivityList($region, $type, $sort)
	{
		$where = '';
		$params = array();
		if ($type) {
			$where .= ' type = :type and ';
			$params[':type'] = $type;
		}
		if ($region) {
			$where .= ' store_id in (select store_id from store_addr where region_id = :region) and ';
			$params[':region'] = $region;
		}
		switch ($sort) {
			case 'clicks':
				$sort = ' order by clicks desc ';
				break;
			case 'date':
				$sort = ' order by id desc ';
				break;
		}
		$sql = 'select id, store_id, title, thumb, description, `limit` 
				from store_activity 
				where ' . $where . ' status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				 ' . $sort;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取活动资料
	 * @param 活动ID $id
	 */
	public function getStoreActivityInfo($id)
	{
		$sql = 'select a.id, a.store_id, a.title, a.`limit`, a.gallery, a.description, a.clicks, b.name as store, c.tel, c.qq 
				from store_activity as a 
				left join store as b on b.id = a.store_id 
				left join store_setting as c on c.store_id = b.id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取下一条活动记录
	 * @param 当前活动ID $current
	 */
	public function getNextStoreActivity($current)
	{
		$sql = 'select id, title 
				from store_activity 
				where id > :current and status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				limit 1';
		$select = $this->select($sql, array(':current' => $current));
		return $this->fetchRow($select);
	}

	/**
	 * 获取活动页面Meta信息
	 * @param 活动ID $id
	 */
	public function getStoreActivityMeta($id)
	{
		$sql = 'select a.title, b.name as store 
				from store_activity as a 
				left join store as b on b.id = a.store_id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取最新活动信息
	 * @param 信息数量 $num
	 */
	public function loadNewStoreActivityList($amount = 1)
	{
		$sql = 'select id, store_id, title, thumb, description, `limit` 
				from store_activity 
				where status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				order by id desc 
				limit ' . $amount;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺优惠活动
	 * @param 店铺ID
	 */
	public function getStoreActivity($id)
	{
		$sql = 'select id, title 
				from store_activity 
				where store_id = :store and status = 1 and (`limit` > UNIX_TIMESTAMP(NOW()) OR `limit` = 0) 
				limit 1';
		$select = $this->select($sql, array(':store' => $id));
		return $this->fetchRow($select);
	}
}
