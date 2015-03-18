<?php
class Resource_System_Tlib_Brand extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'tlib_brand';
	}

	/**
	 * 获取表中最后一个品牌ID
	 */
	public function getLastId()
	{
		$sql = 'select `id` from `tlib_brand` order by id desc limit 1';
		$select = $this->select($sql);
		return $this->fetchOne($select);
	}

	/**
	 * 获取品牌列表
	 */
	public function loadBrandList()
	{
		$sql = 'select `id`, `label` 
				from `tlib_brand` 
				where status = 1 
				order by sort desc, id asc';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取后台品牌列表
	 */
	public function loadAdminBrandList()
	{
		$sql = 'select a.*, count(b.id) as amount 
				from `tlib_brand` as a 
				left join `tlib_model` as b on b.bid = a.id 
				group by a.id';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取指定平板品牌标签
	 * @param 品牌ID $id
	 */
	public function getBrandLabel($id)
	{
		$sql = 'select `label` from `tlib_brand` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
}
