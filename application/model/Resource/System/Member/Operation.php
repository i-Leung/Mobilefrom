<?php
class Resource_System_Member_Operation extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_member_operation';
	}

	/**
	 * 获取网站成员后台操作历史
	 * @param 网站成员ID $id
	 * @param 时间范围 $range
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMemberOperationList($id, $range, $page = 1, $per = 50)
	{
		$where = array();
		$params = array();
		//成员筛选
		if ($id) {
			$where[] = 'a.member_id = :member';
			$params[':member'] = $id;
		}
		//时间筛选
		if ($range) {
			$now = time();
			$before = 0;
			switch ($range) {
				case '1':
					$before = $now - 60 * 60 * 24;//24小时内
					break;
				case '2':
					$before = $now - 60 * 60 * 24 * 3;//72小时内
					break;
				case '3':
					$before = $now - 60 * 60 * 24 * 7;//一周内
					break;
				case '4':
					$before = $now - 60 * 60 * 24 * 15;//半个月内
					break;
				case '5':
					$before = $now - 60 * 60 * 24 * 30;//一个月内
					break;
			}
			$where[] = 'a.operated_at > "' . $before . '" and a.operated_at < "' . $now . '"';
		}
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select c.username, e.controller, d.action, d.label, a.operated_at 
			from `system_member_operation` as a 
			left join `system_member` as b on b.id = a.member_id 
			left join `customer` as c on c.id = b.customer_id 
			left join `system_resource` as d on d.id = a.resource_id 
			left join `system_resource_group` as e on e.id = d.group_id 
			' . $where . ' order by a.id desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取网站成员后台操作历史总数
	 * @param 网站成员ID $id
	 * @param 时间范围 $range
	 */
	public function getMemberOperationTotal($id, $range)
	{
		$where = array();
		$params = array();
		//成员筛选
		if ($id) {
			$where[] = 'member_id = :member';
			$params[':member'] = $id;
		}
		//时间筛选
		if ($range) {
			$now = time();
			$before = 0;
			switch ($range) {
				case '1':
					$before = $now - 60 * 60 * 24;//24小时内
					break;
				case '2':
					$before = $now - 60 * 60 * 24 * 3;//72小时内
					break;
				case '3':
					$before = $now - 60 * 60 * 24 * 7;//一周内
					break;
				case '4':
					$before = $now - 60 * 60 * 24 * 15;//半个月内
					break;
				case '5':
					$before = $now - 60 * 60 * 24 * 30;//一个月内
					break;
			}
			$where[] = 'operated_at > "' . $before . '" and operated_at < "' . $now . '"';
		}
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select count(id) from `system_member_operation`' . $where; 
		$select = $this->select($sql, $params);
		return $this->fetchOne($select);
	}

	/**
	 * 获取资源操作历史
	 * @param 资源ID $id
	 * @param 时间范围 $range
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadResourceOperationList($id, $range, $page = 1, $per = 50)
	{
		$where = array();
		$params = array();
		//资源筛选
		if ($id) {
			$where[] = 'a.resource_id = :resource';
			$params[':resource'] = $id;
		}
		//时间筛选
		if ($range) {
			$now = time();
			$before = 0;
			switch ($range) {
				case '1':
					$before = $now - 60 * 60 * 24;//24小时内
					break;
				case '2':
					$before = $now - 60 * 60 * 24 * 3;//72小时内
					break;
				case '3':
					$before = $now - 60 * 60 * 24 * 7;//一周内
					break;
				case '4':
					$before = $now - 60 * 60 * 24 * 15;//半个月内
					break;
				case '5':
					$before = $now - 60 * 60 * 24 * 30;//一个月内
					break;
			}
			$where[] = 'a.operated_at > "' . $before . '" and a.operated_at < "' . $now . '"';
		}
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select c.username, a.operated_at 
			from `system_member_operation` as a 
			left join `system_member` as b on b.id = a.member_id 
			left join `customer` as c on c.id = b.customer_id 
			' . $where . ' order by a.id desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取资源操作历史总数
	 * @param 资源ID $id
	 * @param 时间范围 $range
	 */
	public function getResourceOperationTotal($id, $range)
	{
		$where = array();
		$params = array();
		//资源筛选
		if ($id) {
			$where[] = 'resource_id = :resource';
			$params[':resource'] = $id;
		}
		//时间筛选
		if ($range) {
			$now = time();
			$before = 0;
			switch ($range) {
				case '1':
					$before = $now - 60 * 60 * 24;//24小时内
					break;
				case '2':
					$before = $now - 60 * 60 * 24 * 3;//72小时内
					break;
				case '3':
					$before = $now - 60 * 60 * 24 * 7;//一周内
					break;
				case '4':
					$before = $now - 60 * 60 * 24 * 15;//半个月内
					break;
				case '5':
					$before = $now - 60 * 60 * 24 * 30;//一个月内
					break;
			}
			$where[] = 'operated_at > "' . $before . '" and operated_at < "' . $now . '"';
		}
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select count(id) from `system_member_operation`' . $where; 
		$select = $this->select($sql, $params);
		return $this->fetchOne($select);
	}
}
