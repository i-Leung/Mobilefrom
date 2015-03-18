<?php
class Default_BatchController extends Project_Controller_Action_Front
{
	/**
	 * 旧数据库时间字段修改
	 */
//	public function timeAction()
//	{
//		$type = $this->_request->getParam('type');
//		try {
//			switch ($type) {
//				case 'customer':
//					$customer = Factory::getResource('customer');
//					$collection = $customer->loadAllCustomer(1, 300);
//					foreach ($collection as $item) {
//						$customer->modify(
//							array('id = ?' => $item['id']), 
//							array(
//								'registered_at' => strtotime($item['registered_at_bk']),
//								'logon_latest' => strtotime($item['logon_latest_bk'])
//							)
//						);
//					}
//					break;
//				case 'mobile':
//					$mobile = Factory::getResource('mobile');
//					$collection = $mobile->loadAllMobile(1, 1000);
//					foreach ($collection as $item) {
//						$end_at = $item['end_at_bk'] == '0000-00-00' ? 0 : $item['end_at_bk'];
//						$mobile->modify(
//							array('id = ?' => $item['id']), 
//							array(
//								'created_at' => strtotime($item['created_at_bk']),
//								'end_at' => strtotime($end_at)
//							)
//						);
//					}
//					break;
//				case 'purchase':
//					$purchase = Factory::getResource('purchase');
//					$collection = $purchase->loadAllPurchase(1, 100);
//					foreach ($collection as $item) {
//						$end_at = $item['end_at_bk'] == '0000-00-00' ? 0 : $item['end_at_bk'];
//						$purchase->modify(
//							array('id = ?' => $item['id']), 
//							array(
//								'created_at' => strtotime($item['created_at_bk']),
//								'end_at' => strtotime($end_at)
//							)
//						);
//					}
//					break;
//				case 'msg_consult':
//					$consult = Factory::getResource('msg/consult');
//					$collection = $consult->loadAllConsult(1, 100);
//					foreach ($collection as $item) {
//						$answered_at = $item['answered_at_bk'] == '0000-00-00 00:00:00' ? 0 : $item['answered_at_bk'];
//						$consult->modify(
//							array('id = ?' => $item['id']), 
//							array(
//								'asked_at' => strtotime($item['asked_at_bk']),
//								'answered_at' => strtotime($answered_at)
//							)
//						);
//					}
//					break;
//				case 'favor':
//					$favor = Factory::getResource('relation/customer/favor');
//					$collection = $favor->loadAllFavor(1, 100);
//					foreach ($collection as $item) {
//						$favor->modify(
//							array(
//								'customer_id = ?' => $item['customer_id'],
//								'type = ?' => $item['type'],
//								'topic_id = ?' => $item['topic_id']
//							), 
//							array(
//								'favor_at' => strtotime($item['favor_at_bk'])
//							)
//						);
//					}
//					break;
//				case 'mobile_order':
//					$order = Factory::getResource('relation/mobile/order');
//					$collection = $order->loadAllOrder(1, 500);
//					foreach ($collection as $item) {
//						$order->modify(
//							array(
//								'id = ?' => $item['id']
//							), 
//							array(
//								'refreshed_at' => strtotime($item['refreshed_at_bk'])
//							)
//						);
//					}
//					break;
//				case 'purchase_order':
//					$order = Factory::getResource('relation/purchase/order');
//					$collection = $order->loadAllOrder(1, 500);
//					foreach ($collection as $item) {
//						$order->modify(
//							array(
//								'id = ?' => $item['id']
//							), 
//							array(
//								'refreshed_at' => strtotime($item['refreshed_at_bk'])
//							)
//						);
//					}
//					break;
//			}
//			echo 'success';
//		} catch (Exception $e) {
//			echo $e->getMessage();
//		}
//	}

	/**
	 * 信息发布时间批量同步为最后刷新时间
	 */
//	public function createdToRefreshedAction()
//	{
//		$info = $this->_request->getParam('info');
//		switch ($info) {
//			case 'mobile':
//				$records = Factory::getResource('relation/mobile/order')->loadAllOrder(1, 1000);
//				$mobile = Factory::getResource('mobile');
//				foreach ($records as $record) {
//					$mobile->modify(
//						array('id = ?' => $record['mobile_id']),
//						array('created_at' => $record['refreshed_at'])
//					);	
//				}
//				break;
//			case 'purchase':
//				$records = Factory::getResource('relation/purchase/order')->loadAllOrder(1, 1000);
//				$mobile = Factory::getResource('purchase');
//				foreach ($records as $record) {
//					$mobile->modify(
//						array('id = ?' => $record['purchase_id']),
//						array('created_at' => $record['refreshed_at'])
//					);	
//				}
//				break;
//		}
//	}

	/**
	 * 闲置手机手机型号信息移至资料库
	 */
	public function movePersonalToMlibAction()
	{
		$records = Factory::getResource('relation/mobile/filter')->loadAllMobileModelId();
		if ($records) {
			$logic = Factory::getLogic('system/mlib');
			foreach ($records as $record) {
				$logic->modifyModel($record['value_id'], array('offers' => '`offers` + ' . $record['amount']));
			}
		}
	}

	/**
	 * 批量清除过期信息
	 */
	public function clearInfoAction()
	{
		$info = $this->_request->getParam('info');
		switch ($info) {
			case 'mobile':
				$expired_at = time() - 15 * 24 * 60 * 60;
				$records = Factory::getResource('mobile')->loadExpiredMobile($expired_at);
				$logic = Factory::getLogic('mobile');
				foreach ($records as $record) {
					$status = '2';
					$logic->moveMobileActiveToInactive($record['id'], $status);
					$logic->removeOrder($record['id']);
					//信息过期提醒
					$notice = array(
						'type' => '2',
						'title' => '您所发布的手机信息已置为过期',
						'content' => '亲爱的用户，由于您所发布的手机信息"' . $record['title'] . '"已超过15天没有刷新，系统已将您的信息置为过期，若有疑问，请联系机荒网客服，谢谢！',
						'created_at' => time()
					);	
					if ($customer_id = $logic->getPublisher($record['id'])) {
						Factory::getLogic('msg')->sendNotice($customer_id, $notice);
						//发送通知邮件
						$customer = Factory::getLogic('customer')->getInfo($customer_id);
						$subject = '机荒网：您所发布的手机信息已置为过期';
						$body = '<p>
									亲爱的用户，由于您所发布的手机信息"' . $record['title'] . '"已超过15天没有刷新，系统已将您的信息置为过期，若有疑问，请联系机荒网客服，谢谢！
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						Factory::getLogic('plugin/mailer')->SendSMTP($customer['email'], $customer['username'], $subject, $body);
					}
				}
				Factory::getLogic('cache')->clearCache('mobile', true);
				break;
			case 'purchase':
				$expired_at = time() - 30 * 24 * 60 * 60;
				$records = Factory::getResource('purchase')->loadExpiredPurchase($expired_at);
				$logic = Factory::getLogic('purchase');
				foreach ($records as $record) {
					$status = '2';
					$logic->movePurchaseActiveToInactive($record['id'], $status);
					$logic->removeOrder($record['id']);
					//信息过期提醒
					$notice = array(
						'type' => '2',
						'title' => '您所发布的求购信息已置为过期',
						'content' => '亲爱的用户，由于您所发布的求购信息"' . $record['title'] . '"已超过30天没有刷新，系统已将您的信息置为过期，若有疑问，请联系机荒网客服，谢谢！',
						'created_at' => time()
					);	
					if ($customer_id = $logic->getPublisher($record['id'])) {
						Factory::getLogic('msg')->sendNotice($customer_id, $notice);
						//发送通知邮件
						$customer = Factory::getLogic('customer')->getInfo($customer_id);
						$subject = '机荒网：您所发布的求购信息已置为过期';
						$body = '<p>
									亲爱的用户，由于您所发布的求购信息"' . $record['title'] . '"已超过30天没有刷新，系统已将您的信息置为过期，若有疑问，请联系机荒网客服，谢谢！
								</p>
								<p>
									<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
								</p>
								<p>
									关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
								</p>';
						Factory::getLogic('plugin/mailer')->SendSMTP($customer['email'], $customer['username'], $subject, $body);
					}
				}
				Factory::getLogic('cache')->clearCache('purchase', true);
				break;
		}
	}

	/**
	 * 清空缓存文档
	 */
	public function clearCacheAction()
	{
		Factory::getLogic('cache')->clearAllCache();
	}
}
