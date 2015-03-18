<?php
class Resource_Store_Setting extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_setting';
	}

	/**
	 * 获取店铺设置
	 * @param 店铺ID $store_id
	 */
	public function getStoreSetting($store_id)
	{
		$sql = 'select * from store_setting where store_id = :id limit 1';
		$select = $this->select($sql, array(':id' => $store_id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取店铺地图
	 * @param 店铺ID $id
	 */
	public function getStoreMap($id)
	{
		$sql = 'select xbaidu, ybaidu 
				from store_setting 
				where store_id = :id 
				limit 1'; 
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
