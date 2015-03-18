<?php
class Resource_System_Member_Authority extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'system_member_authority';
	}
}
