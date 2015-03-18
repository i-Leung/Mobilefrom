<?php
class Resource_System_Mlib_Attribute_Index extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mlib_model_attribute_index';
	}

	/**
	 * 获取手机型号参数占比
	 * @param 参数 $data
	 */
	public function getModelDataPercent($data)
	{
		$sql = array();
		foreach ($data as $key => &$value) {
			$sql[] = 'attribute_id = ' . $key . ' and value_id < ' . $value;
		}
		$sql = '(' . implode(') or (', $sql) . ')';
		$sql = 'select attribute_id, count(*) as total from `mlib_model_attribute_index` where ' . $sql . ' group by attribute_id';
		$sql .= ' union all select attribute_id, count(*) as total from `mlib_model_attribute_index` group by attribute_id';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
