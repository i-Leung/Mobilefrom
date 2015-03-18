<?php
class Resource_Store_Addr extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_addr';
	}

	/**
	 * 获取店铺地址信息
	 * @param 店铺ID $store_id
	 */
	public function getStoreAddr($store_id)
	{
		$sql = 'select region_id, addr 
				from store_addr 
				where store_id = :id';
		$select = $this->select($sql, array(':id' => $store_id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺地址列表
	 * @param 店铺ID集合 $ids
	 */
	public function loadStoreAddrList($ids)
	{
		$ids = '"' . implode('", "', $ids) . '"';
		$sql = 'select store_id, region_id, addr 
				from store_addr 
				where store_id in (' . $ids . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺区域列表
	 * @param 店铺ID集合 $ids
	 */
	public function loadStoreRegionList($ids)
	{
		$ids = '"' . implode('", "', $ids) . '"';
		$sql = 'select distinct store_id, region_id 
				from store_addr 
				where store_id in (' . $ids . ')';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
