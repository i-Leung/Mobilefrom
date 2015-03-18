<?php
class Resource_System_Resource extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_resource';
	}

	/**
	 * 获取资源列表 
	 * @param 所属资源组ID $group_id
	 * @param 资源状态 $status
	 */
	public function loadResourceList($group_id, $status = null)
	{
		switch ($status) {
			case '1':
				$status = 'and a.status = 1';
				break;
			case '0':
				$status = 'and a.status = 0';
				break;
			default:
				$status = '';
				break;
		}
		$sql = 'select a.*, b.label as `group`, b.controller 
			from `system_resource` as a 
			left join `system_resource_group` as b on b.id = a.group_id 
			where a.group_id = :group_id ' . $status;
		$select = $this->select($sql, array(':group_id' => $group_id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取资源信息 
	 * @param 所属资源ID $id
	 */
	public function getResource($id)
	{
		$sql = 'select a.*, b.label as `group` 
			from `system_resource` as a 
			left join `system_resource_group` as b on b.id = a.group_id 
			where a.id = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取当前资源父子关系 
	 * @param 所属资源ID $id
	 */
	public function getCurrentResource($id)
	{
		$sql = 'select a.label as `resource`, b.label as `group`, c.label as `mgroup` 
			from `system_resource` as a 
			left join `system_resource_group` as b on b.id = a.group_id 
			left join `system_resource_group` as c on c.id = b.pid 
			where a.id = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取网站资源树
	 */
	public function loadResourceTree()
	{
		$sql = 'select a.label as `mgroup`, b.label as `group`, b.controller, c.id, c.label as `item`, c.action 
			from `system_resource_group` as a 
			left join `system_resource_group` as b on b.pid = a.id 
			left join `system_resource` as c on c.group_id = b.id 
			where a.pid = 0 and a.status = 1 
			group by a.id, b.id, c.id';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
