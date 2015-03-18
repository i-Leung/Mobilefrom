<?php
class Resource_Msg_Notice extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'msg_notice';
	}
	
	/**
	 * 加载用户信息列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return 用户通知列表或false
	 */
	public function loadMyNotice($customer_id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.type, a.title, a.created_at 
				from `msg_notice` as a 
				left join `relation_customer_msg` as b on b.msg_id = a.id 
				where b.customer_id = :customer_id 
				and b.type = 1 
				order by a.id desc 
				limit ' . $per . ' offset ' . $per * ($page - 1);
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户信息总数
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getMyNoticeTotal($customer_id)
	{
		$sql = 'select count(msg_id) 
				from `relation_customer_msg` 
				where customer_id = :customer_id 
				and type = 1';
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取未读信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewNoticeTotal($customer_id)
	{
		$sql = 'select count(*) 
				from `relation_customer_msg` 
				where customer_id = :customer_id and type = 1 and new = 1';
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取通知内容
	 * @param 通知ID $id
	 * @param 用户ID $customer_id
	 * @return 信息内容或false
	 */
	public function getNoticeContent($id, $customer_id)
	{
		$sql = 'select a.content 
		from `msg_notice` as a 
		left join `relation_customer_msg` as b on b.msg_id = a.id 
		where b.customer_id = :customer_id 
		and b.type = 1 
		and b.msg_id = :id';
		$select = $this->select(
					$sql, 
					array(
						':customer_id' => $customer_id,
						':id' => $id
					)
				);
		return $this->fetchOne($select);
	}
}
