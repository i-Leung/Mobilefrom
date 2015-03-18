<?php
class Resource_System_Member_Group extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_member_group';
	}

	/**
	 * 获取网站成员群组列表
	 */
	public function loadGroupList()
	{
		$sql = 'select * from `system_member_group`';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取网站成员群组信息
	 * @param 群组ID $id
	 */
	public function getGroup($id)
	{
		$sql = 'select a.*, b.resource_id 
			from `system_member_group` as a 
			left join `system_member_authority` as b on b.group_id = a.id 
			where a.id = :id 
			order by b.resource_id asc';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取成员群组可操作资源列表
	 * @param 群组ID $id
	 */
	public function loadGroupResource($id)
	{
		$sql = 'select a.resource_id, b.action, c.controller 
			from `system_member_authority` as a 
			left join `system_resource` as b on b.id = a.resource_id 
			left join `system_resource_group` as c on c.id = b.group_id 
			where a.group_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}
}
