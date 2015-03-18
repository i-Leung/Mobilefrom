<?php
class Resource_Website_Report_Comment extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'website_report_comment';
	}

	/**
	 * @param 判断对象是否发表过举报言论 
	 * @param 举报信息ID $rid
	 * @param 目标对象ID $customer_id
	 */
	public function hasTargetComment($rid, $customer_id)
	{
		if ($rid && $customer_id) {
			$sql = 'select count(id) from website_report_comment where rid = :rid and customer_id = :customer_id';
			$params = array(
				':rid' => $rid,
				':customer_id' => $customer_id,
			);
			return $this->fetchOne(
				$this->select($sql, $params)
			);
		}
		return false;
	}

	/**
	 * 加载举报人、被举报人评论集合ID
	 * @param 举报信息ID $rid
	 * @param 目标对象ID $customer_id
	 */
	public function loadTargetCommentListId($rid, $customer_id)
	{
		if ($rid && $customer_id) {
			$sql = 'select distinct id from (
						select id from website_report_comment where rid = :rid1 and customer_id = :customer_id1 and pid = 0 
						union 
						select pid as id from website_report_comment where rid = :rid2 and customer_id = :customer_id2 and pid <> 0
					) as tmp';
			$params = array(
				':rid1' => $rid,
				':customer_id1' => $customer_id,
				':rid2' => $rid,
				':customer_id2' => $customer_id
			);
			return $this->fetchAll(
				$this->select($sql, $params)
			);
		}
		return false;
	}

	/**
	 * 加载举报人、被举报人评论集合
	 * @param 举报信息ID $rid
	 * @param 目标对象ID $customer_id
	 */
	public function loadTargetCommentList($rid, $customer_id)
	{
		$ids = $this->loadTargetCommentListId($rid, $customer_id);
		if ($ids) {
			$tmp = array();
			foreach ($ids as $item) {
				$tmp[] = $item['id'];
			}
			$tmp = implode(',', $tmp);
			$sql = 'select a.id, a.pid, a.customer_id, b.username, a.content, a.created_at 
					from website_report_comment as a 
					left join customer as b on b.id = a.customer_id 
					where a.id in (' . $tmp . ') or a.pid in (' . $tmp . ') 
					group by a.id, a.pid 
					order by a.pid, a.id';
			return $this->fetchAll($this->select($sql));
		}
		return false;
	}

	/**
	 * 加载举报评论列表
	 * @param 举报ID $rid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadReportCommentList($rid, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.customer_id, b.username, a.content, c.children, a.created_at 
				from website_report_comment as a 
				left join customer as b on b.id = a.customer_id 
				left join ( 
				select pid, count(id) as children from website_report_comment 
				where rid = :irid and pid <> 0 
				group by pid 
				) as c on c.pid = a.id 
				where a.rid = :rid and a.pid = 0 
				order by a.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		return $this->fetchAll(
			$this->select($sql, array(':irid' => $rid, ':rid' => $rid))
		);
	}

	/**
	 * 加载举报评论子评论列表
	 * @param 举报父评论ID $pid
	 */
	public function loadReportCommentChildren($pid)
	{
		$sql = 'select a.customer_id, b.username, a.content, a.created_at 
				from website_report_comment as a 
				left join customer as b on b.id = a.customer_id 
				where pid = :pid';
		return $this->fetchAll(
			$this->select($sql, array(':pid' => $pid))
		);
	}
	
	/**
	 * 获取用户举报相关新信息
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getNewMsgTotal($customer_id)
	{
		$sql = 'select count(msg_id) 
				from `relation_customer_msg` 
				where customer_id = :customer_id 
				and type = 3 
				and new = 1';
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 加载新留言回复
	 * @param 用户ID $customer_id
	 */
	public function loadNewMsgList($customer_id)
	{
		$sql = 'select a.id, a.rid, a.pid, a.customer_id, b.username, d.title, a.content, a.created_at 
				from `website_report_comment` as a 
				left join `customer` as b on b.id = a.customer_id 
				left join `relation_customer_msg` as c on c.msg_id = a.id 
				left join `website_report` as d on d.id = a.rid 
				where c.customer_id = :customer_id and c.type = 3 and c.new = 1 
				order by a.id desc';
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户举报相关信息
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getMsgTotal($customer_id)
	{
		$sql = 'select count(id) 
				from `website_report_comment` 
				where receiver_id = :customer_id';
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 加载所有举报留言回复
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMsgList($customer_id, $page = '1', $per = '10')
	{
		$sql = 'select a.id, a.rid, a.pid, a.customer_id, b.username, c.title, a.content, a.created_at 
				from `website_report_comment` as a 
				left join `customer` as b on b.id = a.customer_id 
				left join `website_report` as c on c.id = a.rid 
				where a.receiver_id = :customer_id 
				order by a.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':customer_id' => $customer_id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取举报信息评论对话记录
	 * @param 对话父ID $id
	 */
	public function showCommentDialog($id)
	{
		$sql = 'select a.id, a.customer_id, b.username, a.content, a.created_at 
				from `website_report_comment` as a 
				left join `customer` as b on b.id = a.customer_id 
				where a.id = :id or a.pid = :pid  
				order by a.id asc';
		$select = $this->select($sql, array(':id' => $id, ':pid' => $id));
		return $this->fetchAll($select);
	}
}
