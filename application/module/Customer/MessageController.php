<?php
class Customer_MessageController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$isLoged = Factory::isLoged();
		if (!$isLoged) {
			$this->redirect(Factory::getBaseUrl());
		}
	}
	
	/**
	 * 站内信
	 */
	public function noticeAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			Factory::getLogic('msg')->readNotice($customer_id);
		}
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 获取通知内容
	 */
	public function getNoticeContentAction()
	{
		$id = $this->_request->getParam('id');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $customer_id) {
			$content = Factory::getView('customer/msg')->getNoticeContent($id, $customer_id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode($content);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 手机咨询
	 */
	public function consultAction()
	{
		$iam = $this->_request->getParam('iam');
		$finished = $this->_request->getParam('finished', '1');
		$customer_id = Factory::getSession('customer/id');
		if ($iam == 'asker' && $finished == '1') {
			Factory::getLogic('msg')->readConsult($customer_id, 'asker');
		}
		if ($iam == 'answerer' && $finished == '0') {
			Factory::getLogic('msg')->readConsult($customer_id, 'answerer');
		}
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * ajax提交咨询
	 */
	public function askConsultAction()
	{
		$consult = $this->_request->getParam('consult');
		$customer_id = Factory::getSession('customer/id');
		if ($consult && $customer_id) {
			if ($customer_id == $consult['answerer']) {
				echo json_encode(2);
				exit(0);
			}
			if ((time() - Factory::getCookie('last_consult_time')) < 30) {
				echo json_encode(3);
				exit(0);
			}
			$consult['asker'] = $customer_id;
			$consult['asked_at'] = time();
			$msgLogic = Factory::getLogic('msg');
			$msgLogic->askConsult($consult);
			//发送提醒邮件
			if ($msgLogic->getNewConsultTotal($consult['answerer']) == '1') {
				$receiverInfo = $msgLogic->getConsultReceiverInfo($consult['type'], $consult['topic_id']);
				switch ($consult['type']) {
					case '1':
						$subject = '机荒网：您所发布的手机出售信息收到咨询了！';
						$mobile = Factory::getLogic('mobile')->getSummarize($consult['topic_id']);
						$body = '<p>
									您在机荒网发布的手机出售信息"<a href="http://www.mobilefrom.com/mobile/item?id=' . $mobile['id'] . '" style="color:blue;">' . $mobile['title'] . '</a>"收到了买家咨询，尽快看一下吧，
									<a style="color:red;" href="http://www.mobilefrom.com/customer/message/consult?iam=answerer&finished=0">马上点击查看</a>
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						break;
					case '2':
						$subject = '机荒网：您所发布的手机求购信息收到咨询了！';
						$purchase = Factory::getLogic('purchase')->getSummarize($consult['topic_id']);
						$body = '<p>
									您在机荒网发布的手机求购信息"<a href="http://www.mobilefrom.com/purchase/item?id=' . $purchase['id'] . '" style="color:blue;">' . $purchase['title'] . '</a>"收到了卖家咨询，尽快看一下吧，
									<a style="color:red;" href="http://www.mobilefrom.com/customer/message/consult?iam=answerer&finished=0">马上点击查看</a>
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						break;
				}
				Factory::getLogic('plugin/mailer')->SendSMTP($receiverInfo['email'], $receiverInfo['username'], $subject, $body);
			}
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setCookie('last_consult_time', time());
				$consult['asker'] = Factory::getSession('customer/username');
				$consult['asked_at'] = date('Y-m-d H:i:s', $consult['asked_at']);
				echo json_encode($consult);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * ajax回复咨询
	 */
	public function replyConsultAction()
	{
		$id = $this->_request->getParam('id');
		$asker = $this->_request->getParam('asker');
		$answer = $this->_request->getParam('answer');
		$customer = Factory::getSession('customer/id');
		if ($id && $answer && $customer) {
			$msgLogic = Factory::getLogic('msg');
			$msgLogic->replyConsult($id, $customer, $asker, $answer);
			if ($msgLogic->getNewConsultTotal($asker) == '1') {
				//发送提醒邮件
				$consult = $msgLogic->getConsultById($id);
				$askerInfo = $msgLogic->getConsultAskerInfo($consult['asker']);
				switch ($consult['type']) {
					case '1':
						$subject = '机荒网：您所发出的手机咨询信息收到回复了！';
						$mobile = Factory::getLogic('mobile')->getSummarize($consult['topic_id']);
						$body = '<p>
									您在机荒网发出的手机咨询信息"<a href="http://www.mobilefrom.com/mobile/item?id=' . $mobile['id'] . '" style="color:blue;">' . $mobile['title'] . '</a>"收到了发布人回复，尽快看一下吧，
									<a style="color:red;" href="http://www.mobilefrom.com/customer/message/consult?iam=asker">马上点击查看</a>
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						break;
					case '2':
						$subject = '机荒网：您所发出的求购咨询信息收到回复了！';
						$purchase = Factory::getLogic('purchase')->getSummarize($consult['topic_id']);
						$body = '<p>
									您在机荒网发出的求购咨询信息"<a href="http://www.mobilefrom.com/purchase/item?id=' . $purchase['id'] . '" style="color:blue;">' . $purchase['title'] . '</a>"收到了发布人回复，尽快看一下吧，
									<a style="color:red;" href="http://www.mobilefrom.com/customer/message/consult?iam=asker">马上点击查看</a>
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						break;
				}
				Factory::getLogic('plugin/mailer')->SendSMTP($askerInfo['email'], $askerInfo['username'], $subject, $body);
			}
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 举报信息相关 
	 */
	public function reportAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
}
