<?php
class Resource_System_Member extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_member';
	}

	/**
	 * 获取群组内成员列表
	 * @param 群组ID $id
	 */
	public function loadGroupMember($id)
	{
		$sql = 'select a.id, a.authorized_at, a.status, c.username as member, d.username as author 
			from `system_member` as a 
			left join `system_member` as b on b.id = a.authorized_by 
			left join `customer` as c on c.id = a.customer_id 
			left join `customer` as d on d.id = b.customer_id 
			where a.group_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取成员信息
	 * @param 成员ID $id
	 */
	public function getMember($id)
	{
		$sql = 'select a.id, a.group_id, e.label as `group`, a.authorized_at, a.logon_latest, a.status, c.username as member, d.username as author 
			from `system_member` as a 
			left join `system_member` as b on b.id = a.authorized_by 
			left join `customer` as c on c.id = a.customer_id 
			left join `customer` as d on d.id = b.customer_id 
			left join `system_member_group` as e on e.id = a.group_id 
			where a.id = :id 
			limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取网站成员列表
	 * @param 组ID $gid
	 * @param 状态 $status
	 */
	public function loadMemberList($gid, $status)
	{
		$where = array();
		$params = array();
		if ($gid !== '') {
			$where[] = 'a.group_id = :gid';
			$params[':gid'] = $gid;
		}
		if ($status !== '') {
			$where[] = 'a.status = :status';
			$params[':status'] = $status;
		}
		$where = $where ? ' where ' . implode(' and ', $where) . ' ' : '';
		$sql = 'select a.id, b.username, c.label as `group`, a.authorized_at, a.logon_latest, a.status 
			from `system_member` as a 
			left join `customer` as b on b.id = a.customer_id 
			left join `system_member_group` as c on c.id = a.group_id' . $where; 
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 后台登录验证
	 * @param 客户ID $customer_id
	 */
	public function authMember($customer_id)
	{
		$sql = 'select a.id, b.username as member, a.group_id, c.label as `group`, a.logon_latest, a.pwd, a.authorized_at 
			from `system_member` as a 
			left join `customer` as b on b.id = a.customer_id 
			left join `system_member_group` as c on c.id = a.group_id 
			where a.customer_id = :customer and a.status = 1  
			limit 1';
		$select = $this->select($sql, array(':customer' => $customer_id));
		return $this->fetchRow($select);
	}
}
