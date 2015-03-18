<?php
class Monitor_Info_MobileController extends Project_Controller_Action_Admin
{
	/**
	 * 手机列表
	 */
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 冻结信息
	 */
	public function statusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id && $status) {
			$mobile = Factory::getLogic('mobile');
			$info = $mobile->getSummarize($id);
			$mobile->removeOrder($id);
			$mobile->changeStatus($id, $status);
			//信息冻结提醒
			$notice = array(
				'type' => '2',
				'title' => '您所发布的手机信息已被冻结',
				'content' => '亲爱的用户，由于您所发布的手机信息"' . $info['title'] . '"不符合要求（如：没有手机图片，所填写的资料不真实，您的信息被其他用户举报等），系统已将您的信息进行冻结，若有疑问，请联系机荒网客服，谢谢！',
				'created_at' => time()
			);	
			if ($customer_id = Factory::getLogic('mobile')->getPublisher($id)) {
				Factory::getLogic('msg')->sendNotice($customer_id, $notice);
			}
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::getLogic('cache')->clearCache('mobile', true);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
