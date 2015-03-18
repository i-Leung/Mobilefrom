<?php
class Resource_System_Mlib_Attribute_Value extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mlib_model_attribute_value';
	}

	/**
	 * 获取当前属性下所有属性值
	 * @param 属性ID $id
	 */
	public function loadAttributeValue($id = null)
	{
		$where = '';
		$params = array();
		if ($id) {
			$where = ' where attribute_id = :id ';
			$params = array(':id' => $id);
		}
		$sql = 'select attribute_id, value_id, label from `mlib_model_attribute_value` 
				' . $where . '
				order by value_id';
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}
}
