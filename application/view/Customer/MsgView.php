<?php
class Customer_MsgView extends Project_View_Block_Abstract
{
	/**
	 * 获取未读信息数量
	 */
	public function getNewMsgTotal($customer_id)
	{
		//$total = $this->getNewConsultTotal($customer_id) + $this->getNewNoticeTotal($customer_id);
		$total = $this->getNewNoticeTotal($customer_id) + $this->getNewReportMsgTotal($customer_id);
		return $total ? $total : 0;
	}
	
	/**
	 * 获取未读咨询信息数量
	 */
	public function getNewConsultTotal($customer_id)
	{
		return $this->getNewReceivedConsultTotal($customer_id) + $this->getNewRepliedConsultTotal($customer_id);
	}
	
	/**
	 * 获取未读收到咨询信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewReceivedConsultTotal($customer_id)
	{
		$total = Factory::getResource('msg/consult')->getNewReceivedConsultTotal($customer_id);
		return $total ? $total : 0;
	}

	/**
	 * 获取未读咨询回复信息数量
	 * @param 用户ID $customer_id
	 */
	public function getNewRepliedConsultTotal($customer_id)
	{
		$total = Factory::getResource('msg/consult')->getNewRepliedConsultTotal($customer_id);
		return $total ? $total : 0;
	}
	
	/**
	 * 获取未读信息数量
	 */
	public function getNewNoticeTotal($customer_id)
	{
		$total = Factory::getResource('msg/notice')->getNewNoticeTotal($customer_id);
		return $total ? $total : 0;
	}
	
	/**
	 * 获取用户发出的咨询列表
	 * @param 是否回复 $finished
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 */
	public function loadMySentConsult($finished = '1', $page = 1, $per = 10)
	{
		return Factory::getResource('msg/consult')->loadMySentConsult(
			Factory::getSession('customer/id'), $finished, $page, $per
		);
	}
	
	/**
	 * 获取用户收到的咨询列表
	 * @param 是否回复 $finished
	 * @param 请求页面 $page
	 * @param 单页数量 $per
	 */
	public function loadMyRecieveConsult($finished = '0', $page = 1, $per = 10)
	{
		return Factory::getResource('msg/consult')->loadMyRecieveConsult(
			Factory::getSession('customer/id'), $finished, $page, $per
		);
	}
	
	/**
	 * 获取用户发出的咨询总数
	 * @param 是否回复 $finished
	 */
	public function getMySentConsultTotal($finished = '1')
	{
		return Factory::getResource('msg/consult')->getMySentConsultTotal(
			Factory::getSession('customer/id'), $finished
		);
	}
	
	/**
	 * 获取用户收到的咨询总数
	 * @param 是否回复 $finished
	 */
	public function getMyRecieveConsultTotal($finished = '0')
	{
		return Factory::getResource('msg/consult')->getMyRecieveConsultTotal(
			Factory::getSession('customer/id'), $finished
		);
	}
	
	/**
	 * 获取用户站内通知列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMyNotice($customer_id, $page = 1, $per = 10)
	{
		return Factory::getResource('msg/notice')->loadMyNotice($customer_id, $page, $per);
	}
	
	/**
	 * 获取用户信息总数
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getMyNoticeTotal($customer_id)
	{
		return Factory::getResource('msg/notice')->getMyNoticeTotal($customer_id);
	}
	
	/**
	 * 获取通知类型
	 * @param 类型子 $type
	 */
	public function getNoticeType($type)
	{
		$label = '';
		switch ($type) {
			case '1':
				$label = '全站通告';
				break;
			case '2':
				$label = '操作提醒';
				break;
			case '3':
				$label = '站内信息';
				break;
			default:
				$label = '公共通知';
				break;
		}
		return $label;
	}
	
	/**
	 * 获取通知内容
	 * @param 通知ID $id
	 * @param 用户ID $customer_id
	 * @return 信息内容或false
	 */
	public function getNoticeContent($id, $customer_id)
	{
		return Factory::getResource('msg/notice')->getNoticeContent($id, $customer_id);
	}
	
	/**
	 * 获取用户举报相关新信息数量
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getNewReportMsgTotal($customer_id)
	{
		$total = Factory::getResource('website/report/comment')->getNewMsgTotal($customer_id);
		return $total ? $total : 0;
	}

	/**
	 * 加载新留言回复
	 * @param 用户ID $customer_id
	 */
	public function loadNewReportMsgList($customer_id)
	{
		return Factory::getResource('website/report/comment')->loadNewMsgList($customer_id);
	}
	
	/**
	 * 获取用户举报相关信息数量
	 * @param 用户ID $customer_id
	 * @return 用户信息总数或false
	 */
	public function getReportMsgTotal($customer_id)
	{
		return Factory::getResource('website/report/comment')->getMsgTotal($customer_id);
	}

	/**
	 * 加载所有举报留言回复
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadReportMsgList($customer_id, $page = '1', $per = '10')
	{
		return Factory::getResource('website/report/comment')->loadMsgList($customer_id, $page, $per);
	}
}
