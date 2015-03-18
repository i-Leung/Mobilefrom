<?php
class Resource_Website_Report_Search extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'website_report_search';
	}

	/**
	 * 获取指定举报信息的联系信息进行编辑
	 * @param 信息ID $id
	 */
	public function getReportContactByEdit($id)
	{
		$sql = 'select type, number 
				from website_report_search 
				where rid = :rid';
		return $this->fetchAll(
			$this->select($sql, array(':rid' => $id))
		);
	}

	/**
	 * 检测卖家是否被举报
	 * @param 卖家ID $customer_id
	 * @param 联系电话 $tel
	 * @param QQ $qq
	 */
	public function checkReported($customer_id, $tel, $qq)
	{
		$sql = 'select type, number 
				from website_report_search 
				where (type = 1 and number = :customer) 
				or (type = 2 and number = :qq) 
				or (type = 3 and number = :tel) 
				limit 1';
		return $this->fetchRow(
			$this->select($sql, array(':customer' => $customer_id, ':qq' => $qq, ':tel' => $tel))
		);
	}

	/**
	 * 获取举报联系信息总数
	 */
	public function getReportSearchTotal()
	{
		$sql = 'select count(*) from website_report_search';
		return $this->fetchOne(
			$this->select($sql)
		);
	}
}
