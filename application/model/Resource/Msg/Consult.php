<?php
class Resource_Msg_Consult extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'msg_consult';
	}

	/**
	 * 获取指定咨询信息记录
	 * @param 咨询信息ID $id
	 */
	public function getConsultById($id)
	{
		$sql = 'select * from `msg_consult` where id = :id limit 1'; 
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
	
	/**
	 * 获取手机咨询列表
	 * @param 手机信息ID $id
	 * @param 主题类型 $type
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或咨询列表
	 */
	public function loadTopicConsult($type, $id, $page = 1, $per = 10)
	{
		$sql = 'select a.id, a.asker, a.ask, a.asked_at, a.answerer, a.answer, a.answered_at, b.username 
				from `msg_consult` as a 
				left join `customer` as b on b.id = a.asker 
				where a.type = :type and a.topic_id = :topic 
				order by a.id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, array(':type' => $type, ':topic' => $id));
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取手机咨询总量
	 * @param 手机信息ID $id
	 * @param 主题类型 $type
	 * @return false或咨询总量
	 */
	public function getTopicConsultTotal($type, $id)
	{
		$sql = 'select count(id) 
				from `msg_consult` 
				where type = :type and topic_id = :topic';
		$select = $this->select($sql, array(':type' => $type, ':topic' => $id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户发出的咨询列表
	 * @param 用户ID $customer_id
	 * @param 是否回复 $finished
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 */
	public function loadMySentConsult($customer_id, $finished = '1', $page = 1, $per = 10)
	{
		$sql = 'select * from (
				select a.*, b.title 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.asker = :masker and a.finished = :finished 
				union 
				select a.*, b.title 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.asker = :pasker and a.finished = :finished 
				) as result 
				order by id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select(
			$sql, 
			array(
				':masker' => $customer_id,
				':pasker' => $customer_id,
				':finished' => $finished
			)
		);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户收到的咨询列表
	 * @param 用户ID $customer_id
	 * @param 是否回复 $finished
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 */
	public function loadMyRecieveConsult($customer_id, $finished = '0', $page = 1, $per = 10)
	{
		$sql = 'select * from (
				select a.*, b.title 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.answerer = :manswerer and a.finished = :finished 
				union 
				select a.*, b.title 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.answerer = :panswerer and a.finished = :finished 
				) as result 
				order by id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select(
			$sql, 
			array(
				':manswerer' => $customer_id,
				':panswerer' => $customer_id,
				':finished' => $finished
			)
		);
		return $this->fetchAll($select);
	}
	
	/**
	 * 获取用户发出的咨询总数
	 * @param 用户ID $customer_id
	 * @param 是否回复 $finished
	 */
	public function getMySentConsultTotal($customer_id, $finished = '1')
	{
		$sql = 'select count(id) from (
				select a.id 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.asker = :masker and a.finished = :finished 
				union 
				select a.id 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.asker = :pasker and a.finished = :finished 
				) as result'; 
		$select = $this->select(
			$sql, 
			array(
				':masker' => $customer_id,
				':pasker' => $customer_id,
				':finished' => $finished
			)
		);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取用户收到的咨询总数
	 * @param 用户ID $customer_id
	 * @param 是否回复 $finished
	 */
	public function getMyRecieveConsultTotal($customer_id, $finished = '0')
	{
		$sql = 'select count(id) from (
				select a.id 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.answerer = :manswerer and a.finished = :finished 
				union 
				select a.id 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.answerer = :panswerer and a.finished = :finished 
				) as result';
		$select = $this->select(
			$sql, 
			array(
				':manswerer' => $customer_id,
				':panswerer' => $customer_id,
				':finished' => $finished
			)
		);
		return $this->fetchOne($select);
	}
	
	/**
	 * 获取未读收到咨询信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewReceivedConsultTotal($customer_id)
	{
		$sql = 'select count(id) from (
				select a.id 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.answerer = :manswerer 
				union 
				select a.id 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.answerer = :panswerer 
				) as result 
				where id in (
					select msg_id from `relation_customer_msg` where customer_id = :my and new = 1 and type = 2 
				)';
		$select = $this->select($sql, array(':manswerer' => $customer_id, ':panswerer' => $customer_id, ':my' => $customer_id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取未读咨询回复信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewRepliedConsultTotal($customer_id)
	{
		$sql = 'select count(id) from (
				select a.id 
				from `msg_consult` as a 
				right join `mobile` as b on b.id = a.topic_id 
				where a.type = 1 and a.asker = :masker 
				union 
				select a.id 
				from `msg_consult` as a 
				right join `purchase` as b on b.id = a.topic_id 
				where a.type = 2 and a.asker = :pasker 
				) as result 
				where id in (
					select msg_id from `relation_customer_msg` where customer_id = :my and new = 1 and type = 2 
				)';
		$select = $this->select($sql, array(':masker' => $customer_id, ':pasker' => $customer_id, ':my' => $customer_id));
		return $this->fetchOne($select);
	}
	
	/**
	 * 标记已阅读咨询
	 * @param 用户ID $customer_id
	 * @param 是否自己发出 $type
	 */
	public function readConsult($customer_id, $type)
	{
		switch ($type) {
			case 'asker':
				$sql = 'select `id` 
							from `msg_consult` 
							where `asker` = ' . $customer_id . ' 
							and `finished` = 1';
				break;
			case 'answerer':
				$sql = 'select `id` 
							from `msg_consult` 
							where `answerer` = ' . $customer_id . ' 
							and `finished` = 0';
				break;
		}
		$sql = 'update `relation_customer_msg` 
				set `new` = 0 
				where `customer_id` = ' . $customer_id . ' 
				and `type` = 2 
				and `new` = 1 
				and `msg_id` in (' . $sql . ')';
		try {
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_db->beginTransaction();
			$this->_db->exec($sql);
			$this->_db->commit();
			return true;
		} catch (PDOException $e) {
			$this->_db->rollBack();
			Factory::setMsg($e->getMessage());
			return false;
		}
	}
	
	/**
	 * 获取所有咨询列表
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 */
	public function loadAllConsult($page = 1, $per = 10)
	{
		$sql = 'select * from `msg_consult` limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}}
