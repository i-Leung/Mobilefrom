<?php
class Resource_Relation_Customer_Msg extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'relation_customer_msg';
	}

	/**
	 * 查看举报留言
	 * @param 用户ID $customer_id
	 * @param 留言类型 $type(comment/reply)
	 */
	public function readReportMsg($customer_id, $type)
	{}
}
