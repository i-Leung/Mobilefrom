<?php
class Resource_Website_Suggestion extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'website_suggestion';
	}
}