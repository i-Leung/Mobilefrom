<?php
class Logic_Msg
{
	/**********************************************咨询**********************************************/
	
	/**
	 * 获取未读咨询信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewConsultTotal($customer_id)
	{
		$resource = Factory::getResource('msg/consult');
		return $resource->getNewReceivedConsultTotal($customer_id) + $resource->getNewRepliedConsultTotal($customer_id);
	}

	/**
	 * 获取指定咨询信息记录
	 * @param 咨询信息ID $id
	 */
	public function getConsultById($id)
	{
		return Factory::getResource('msg/consult')->getConsultById($id);
	}

	/**
	 * 获取咨询发问者邮箱地址
	 * @param 咨询发问者ID $custmer_id
	 */
	public function getConsultAskerInfo($customer_id)
	{
		$info = Factory::getLogic('customer')->getInfo($customer_id);
		return $info;
	}

	/**
	 * 获取咨询发问者邮箱地址
	 * @param 咨询信息主题类型 $type
	 * @param 咨询信息主题ID $id
	 */
	public function getConsultReceiverInfo($type, $id)
	{
		switch ($type) {
			case '1':
				return Factory::getLogic('mobile')->getPublisherInfo($id);
				break;
			case '2':
				return Factory::getLogic('purchase')->getPublisherInfo($id);
				break;
		}
	}
	
	/**
	 * 标记已阅读咨询
	 * @param 用户ID $customer_id
	 * @param 是否自己发出 $type
	 */
	public function readConsult($customer_id, $type)
	{
		if ($customer_id && $type) {
			return Factory::getResource('msg/consult')->readConsult($customer_id, $type);
		}
		return false;
	}
	
	/**
	 * 发表咨询
	 * @param 咨询内容 $data
	 */
	public function askConsult($data)
	{
		$consult = Factory::getResource('msg/consult');
		if ($consult->create($data)) {
			$msg = array(
				'customer_id' => $data['answerer'],
				'type' => '2',
				'msg_id' => $consult->getNewId()
			);
			Factory::getResource('relation/customer/msg')->create($msg);
		}
		return;
	}
	
	/**
	 * 回复咨询
	 * @param 咨询信息ID $id
	 * @param 回复者ID $customer_id
	 * @param 咨询者ID $asker
	 * @param 回复内容 $data
	 */
	public function replyConsult($id, $customer_id, $asker, $answer)
	{
		$consult = Factory::getResource('msg/consult');
		$condition = array('id = ?' => $id, 'answerer = ?' => $customer_id, 'asker = ?' => $asker);
		$data = array('answer' => $answer, 'answered_at' => time(), 'finished' => '1');
		if ($consult->modify($condition, $data)) {
			$msg = array(
				'customer_id' => $asker,
				'type' => '2',
				'msg_id' => $id
			);
			Factory::getResource('relation/customer/msg')->create($msg);
		}
		return;
	}
	
	/**********************************************站内通知**********************************************/
	/**
	 * 标记已阅读通知
	 * @param 用户ID $customer_id
	 */
	public function readNotice($customer_id)
	{
		return Factory::getResource('relation/customer/msg')->modify(
			array(
				'customer_id = ?' => $customer_id,
				'type = ?' => '1',
				'new = ?' => '1'
			),
			array('new' => '0')
		);
	}

	/**
	 * 发送站内通知
	 * @param 用户ID $customer_id
	 * @param 发送数据 $data
	 */
	public function sendNotice($customer_id, $data)
	{
		$notice = Factory::getResource('msg/notice');
		if ($notice->create($data)) {
			$msg = array(
				'customer_id' => $customer_id,
				'type' => '1',
				'msg_id' => $notice->getNewId()
			);
			Factory::getResource('relation/customer/msg')->create($msg);
		}
		return;
	}
	
	/**********************************************举报评论/回复**********************************************/
	/**
	 * 举报留言
	 * @param 用户ID $customer_id
	 * @param 信息ID $id
	 */
	public function leaveReportMsg($customer_id, $id)
	{
		if ($customer_id && $id) {
			$msg = array(
				'customer_id' => $customer_id,
				'type' => '3',
				'msg_id' => $id
			);
			return Factory::getResource('relation/customer/msg')->create($msg);
		}
		return false;
	}

	/**
	 * 查看举报留言
	 * @param 用户ID $customer_id
	 * @param 留言类型 $type(comment/reply)
	 */
	public function readReportMsg($customer_id)
	{
		if ($customer_id) {
			return Factory::getResource('relation/customer/msg')->modify(
				array('customer_id = ?' => $customer_id, 'type' => '3'),
				array('new' => '0')
			);
		}
		return false;
	}
}
