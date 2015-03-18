<?php
class Resource_System_Service extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'service';
	}

	/**
	 * 获取服务列表
	 */
	public function loadServiceList()
	{
		$sql = 'select * from service';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取指定服务信息
	 * @param 服务ID $id
	 */
	public function getServiceInfo($id)
	{
		$sql = 'select * from service where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取服务列表
	 */
	public function loadServiceTitleList()
	{
		$sql = 'select id, title from service';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
