<?php
class Resource_Store extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store';
	}

	/**
	 * 获取商店列表
	 * @param 服务类型 $service
	 * @param 地区 $region
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadStoreList($service = null, $region = null, $page = 1, $per = 9)
	{
		$where = '';
		$params = array();
		if ($region) {
			$where = ' and c.region_id = :region ';
			$params = array(':region' => $region);
		}
		if ($service) {
			$where = ' and b.service_id = :service ';
			$params = array(':service' => $service);
		}
		$sql = 'select distinct a.id, a.customer_id, a.name, a.service, a.introduction, a.clicks 
				from store as a 
				left join store_service as b on b.store_id = a.id 
				left join store_addr as c on c.store_id = a.id 
				where a.status = 1 ' . $where . ' 
				order by id asc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取商店总数
	 * @param 服务类型 $service
	 * @param 地区 $region
	 */
	public function getStoreTotal($service = null, $region = null)
	{
		$where = '';
		$params = array();
		if ($region) {
			$where = ' and c.region_id = :region ';
			$params = array(':region' => $region);
		}
		if ($service) {
			$where = ' and b.service_id = :service ';
			$params = array(':service' => $service);
		}
		$sql = 'select count(distinct a.id) 
				from store as a 
				left join store_service as b on b.store_id = a.id 
				left join store_addr as c on c.store_id = a.id 
				where a.status = 1 ' . $where; 
		$select = $this->select($sql, $params);
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺设置
	 * @param 用户ID $customer_id
	 */
	public function getMyStoreInfo($customer_id)
	{
		$sql = 'select id, name, license, introduction, gallery, username, mobile, qq  
				from store 
				where customer_id = :id 
				limit 1'; 
		$select = $this->select($sql, array(':id' => $customer_id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取店铺ID
	 * @param 用户ID $customer_id
	 */
	public function getStoreId($customer_id)
	{
		$sql = 'select id from store where customer_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺信息
	 * @param 店铺ID $id
	 */
	public function getStoreInfo($id)
	{
		$sql = 'select name, introduction, gallery, created_at 
				from store 
				where id = :id 
				limit 1'; 
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取店铺公用信息
	 * @param 店铺ID $id
	 */
	public function getStorePublicInfo($id)
	{
		$sql = 'select a.customer_id, a.name, a.clicks, b.working, b.tel, b.qq, b.notice, count(c.id) as total, xbaidu, ybaidu 
				from store as a 
				left join store_setting as b on b.store_id = a.id 
				left join store_mobile_model as c on c.store_id = a.id 
				where a.id = :id and c.status = 1 
				limit 1'; 
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 判断店铺是否属于用户的
	 * @param 店铺ID $store_id
	 * @param 用户ID $customer_id
	 */
	public function isMyStore($store_id, $customer_id)
	{
		$sql = 'select id 
				from store 
				where id = :store and customer_id = :customer 
				limit 1'; 
		$select = $this->select($sql, array(':store' => $store_id, ':customer' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 检测店铺是否通过验证
	 * @param 用户ID $customer_id
	 */
	public function isValidated($customer_id)
	{
		$sql = 'select status 
				from store 
				where customer_id = :customer 
				limit 1'; 
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取指定型号出售店铺列表
	 * @param 手机型号 $id
	 */
	public function loadStoreListByMobileModel($id)
	{
		$sql = 'select a.id, a.name, a.customer_id, d.region_id, b.id as model 
				from store as a 
				left join store_mobile_model as b on b.store_id = a.id 
				left join store_mobile_item as c on c.store_model_id = b.id 
				left join store_addr as d on d.store_id = a.id 
				where a.status = 1 and b.model_id = :model and b.status = 1 and c.status = 1 
				group by a.id 
				order by a.id asc';
		$select = $this->select($sql, array(':model' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取指定型号出售店铺列表
	 * @param 手机型号 $id
	 */
	public function loadStoreListByTabletModel($id)
	{
		$sql = 'select a.id, a.name, a.customer_id, d.region_id, b.id as model 
				from store as a 
				left join store_tablet_model as b on b.store_id = a.id 
				left join store_tablet_item as c on c.store_model_id = b.id 
				left join store_addr as d on d.store_id = a.id 
				where a.status = 1 and b.model_id = :model and b.status = 1 and c.status = 1 
				group by a.id 
				order by a.id asc';
		$select = $this->select($sql, array(':model' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取入驻商店列表
	 * @param 读取数量 $limit
	 */
	public function loadNewStoreList($limit = 4)
	{
		$sql = 'select a.id, a.customer_id, a.name, b.region_id 
				from store as a 
				left join store_addr as b on b.store_id = a.id 
				where a.id in (select id from (select id from store where status = 1 order by id desc limit ' . $limit . ') as tmp)';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取商家微博帐号
	 * @param 店铺ID $id
	 */
	public function getSinaAccount($id)
	{
		$sql = 'select a.sina 
				from customer as a 
				left join store as b on b.customer_id = a.id 
				where b.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
}
