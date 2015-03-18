<?php
class Resource_System_Mlib_Attribute extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mlib_model_attribute';
	}

	/**
	 * 获取属性列表
	 */
	public function loadAttributeList()
	{
		$sql = 'select id, label, type, status from `mlib_model_attribute`';
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取当前属性标签
	 * @param 属性ID $id
	 */
	public function getAttributeLabel($id)
	{
		$sql = 'select label from `mlib_model_attribute` 
				where id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取属性组内子属性列表
	 * @param 群组ID $group
	public function loadGroupAttributeList($group = 0)
	{
		$where = '';
		$params = array();
		if ($group) {
			$where = ' where id in(select attribute_id from `relation_mlib_group_attribute` where group_id = :group) ';
			$params[':group'] = $group;
		}
		$sql = 'select id, label from `mlib_attribute`' . $where; 
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}
	 */
}
