<?php
class Resource_System_Tlib_Attribute extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'tlib_model_attribute';
	}

	/**
	 * 获取属性列表
	 */
	public function loadAttributeList()
	{
		$sql = 'select id, label, type, status from `tlib_model_attribute`';
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取当前属性标签
	 * @param 属性ID $id
	 */
	public function getAttributeLabel($id)
	{
		$sql = 'select label from `tlib_model_attribute` 
				where id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
}
