<?php
class Resource_Relation_Mlib_Groupattr extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_mlib_group_attribute';
	}
}
