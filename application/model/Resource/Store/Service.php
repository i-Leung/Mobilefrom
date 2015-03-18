<?php
class Resource_Store_Service extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_service';
	}

	/**
	 * 获取商店服务列表
	 * @param 店铺ID $store
	 */
	public function loadMyServiceList($store)
	{
		$sql = 'select service_id as id, introduction 
				from store_service 
				where store_id = :store';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchAll($select);
	}

	/**
	 * 获取商店服务列表
	 * @param 店铺ID $store
	 */
	public function loadStoreServiceList($store)
	{
		$sql = 'select b.id, b.title, a.introduction, b.description 
				from store_service as a 
				left join service as b on b.id = a.service_id 
				where a.store_id = :store';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchAll($select);
	}

	/**
	 * 获取商店服务标题列表
	 * @param 店铺ID $store
	 */
	public function loadStoreServiceTitleList($store)
	{
		$sql = 'select b.title 
				from store_service as a 
				left join service as b on b.id = a.service_id 
				where a.store_id = :store';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchAll($select);
	}
}
