<?php
class Resource_System_Resource_Group extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_resource_group';
	}

	/**
	 * 加载资源组列表
	 * @param 父组ID $pid:为0则主资源组
	 */
	public function loadGroupList($pid = 0)
	{
		$sql = 'select * from `system_resource_group` 
			where `pid` = :pid 
			order by pid asc';
		$select = $this->select($sql, array(':pid' => $pid));
		return $this->fetchAll($select);
	}

	/**
	 * 获取资源组信息
	 * @param 组ID $id
	 */
	public function getGroupInfo($id)
	{
		$sql = 'select * from `system_resource_group` 
			where `id` = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
