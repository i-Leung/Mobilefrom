<?php
class Resource_Store_Visitor extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_visitor';
	}

	/**
	 * 获取店铺IP量
	 * @param 过滤条件 $range
	 */
	public function getIpAmount($range)
	{
		$range['where'] = implode(' and ', $range['where']);
		$sql = 'select count(distinct ip) from store_visitor where ' . $range['where'];
		$select = $this->select($sql, $range['params']);
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺IP量
	 * @param 过滤条件 $range
	 */
	public function getPvAmount($range)
	{
		$range['where'] = implode(' and ', $range['where']);
		$sql = 'select count(id) from store_visitor where ' . $range['where'];
		$select = $this->select($sql, $range['params']);
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺IP量
	 * @param 过滤条件 $range
	 */
	public function getConsultAmount($range)
	{
		$range['where'] = implode(' and ', $range['where']);
		$sql = 'select count(id) from store_visitor where ' . $range['where'] . ' and consult = 1';
		$select = $this->select($sql, $range['params']);
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺浏览记录
	 * @param 过滤条件 $range
	 */
	public function loadVisitorLog($range)
	{
		$range['where'] = implode(' and ', $range['where']);
		$sql = 'select url, count(id) as amount, date from store_visitor 
				where ' . $range['where'] . ' 
				group by url 
				order by amount desc';
		$select = $this->select($sql, $range['params']);
		return $this->fetchAll($select);
	}
}
