<?php
class Website_ReportController extends Project_Controller_Action_Front
{
	/**
	 * 举报中心
	 */
	public function indexAction(){}

	/**
	 * 举报中心
	 */
	public function listAction()
	{
		$this->loadLayout('report');
		$this->renderLayout();
	}

	/**
	 * 我的举报
	 */
	public function myAction()
	{
		if (Factory::getSession('customer/id')) {
			$this->loadLayout('report');
			$this->renderLayout();
		} else {
			$this->redirect(Factory::getUrl('website/report/list'));
		}
	}
	
	/**
	 * 举报规则
	 */
	public function ruleAction()
	{
		$this->loadLayout('report');
		$this->renderLayout();
	}
	
	/**
	 * 举报信息
	 */
	public function itemAction()
	{
		if (!in_array(Factory::getSession('customer/id'), array('1', '2', '10'))) {
			if ($id = $this->_request->getParam('id', null)) {
				Factory::getLogic('website')->modifyReport($id, array('clicks' => '`clicks` + 1'));
			}
		}
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 发布举报信息
	 */
	public function publishAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 提交举报信息
	 */
	public function ajaxPublishAction()
	{
		$id = $this->_request->getParam('id', null);
		$report = $this->_request->getParam('report', null);
		$reporter_id = Factory::getSession('customer/id');
		if ($report && $reporter_id) {
			//举报信息整理
			$report['reporter_id'] = $reporter_id;
			if (!($report['informer_id'] = Factory::getLogic('customer')->isExist($report['informer_id']))) {//非机荒网用户不作用户登记
				unset($report['informer_id']);
			}
			if ($report['informer_id'] == '2') {//机荒网本站转发帐号不属处理范围
				unset($report['informer_id']);
			}
			if ($report['gallery'] && $report['thumb'] == '') {
				$report['thumb'] = substr($report['gallery'], 0, strpos($report['gallery'], ';'));
			}
			$report['content'] = str_replace('&lt;div&gt;&lt;br&gt;&lt;/div&gt;', '', $report['content']);
			$report['summary'] = mb_substr(strip_tags(htmlspecialchars_decode($report['content'])), 0, 100, 'utf-8');
			$report['created_at'] = time();
			$tel = $qq = array();
			if ($report['qq']) {
				$qq = explode(';', substr($report['qq'], 0, -1));
			}
			unset($report['qq']);
			if ($report['tel']) {
				$tel = explode(';', substr($report['tel'], 0, -1));
			}
			unset($report['tel']);
			$wLogic = Factory::getLogic('website');
			if ($id) {
				$id = $wLogic->modifyReport($id, $report) && $wLogic->removeReportSearch($id) ? $id : false;
			} else {
				$id = $wLogic->createReport($report);
			}
			if ($id) {
				//搜索数据入库
				$search = array(
					'rid' => array(),
					'type' => array(),
					'number' => array()
				);
				if (isset($report['informer_id'])) {
					$search['rid']['1'] = $id;
					$search['type']['1'] = '1';
					$search['number']['1'] = $report['informer_id'];
				}
				foreach ($qq as $key => $value) {
					$search['rid']['2' . $key] = $id;
					$search['type']['2' . $key] = '2';
					$search['number']['2' . $key] = $value;
				}
				foreach ($tel as $key => $value) {
					$search['rid']['3' . $key] = $id;
					$search['type']['3' . $key] = '3';
					$search['number']['3' . $key] = $value;
				}
				if ($search) {
					$wLogic->createReportSearch($search);
				}
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($id);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取举报信息进行编辑
	 */
	public function getAction()
	{
		$id = $this->_request->getParam('id');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $customer_id) {
			$view = Factory::getView('website/report');
			if ($report = $view->getReportByEdit($id, $customer_id)) {
				$contact = $view->getReportContactByEdit($id);
				$contact = $contact ? $contact : array();
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(array('report' => $report, 'contact' => $contact));
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 用户投票
	 */
	public function voteAction()
	{
		$id = $this->_request->getParam('id');
		$who = $this->_request->getParam('who');
		if ($id && $who) {
			switch ($who) {
				case 'reporter':
					$params['reporter_point'] = '`reporter_point` + 1';
					break;
				case 'informer':
					$params['informer_point'] = '`informer_point` + 1';
					break;
			}
			Factory::getLogic('website')->modifyReport($id, $params);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setCookie('vote-report' . $id, time());
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 发布举报评论
	 */
	public function commentAction()
	{
		$comment = $this->_request->getParam('comment', null);
		$can = Factory::getCookie('comment-report' . $comment['rid']);
		$can = $can ? (time() - $can) > 30 : 1;
		if (!$can) {
			echo json_encode(-1);
			exit(0);
		}
		$customer_id = Factory::getSession('customer/id');
		if ($customer_id && $comment) {
			$data = array(
				'rid' => $comment['rid'],
				'pid' => $comment['id'],
				'customer_id' => $customer_id,
				'receiver_id' => $comment['to'],
				'content' => $comment['content'],
				'created_at' => time()
			);
			$wLogic = Factory::getLogic('website');
			$cid = $wLogic->createReportComment($data);
			if ($cid) {
				$wLogic->modifyReport(
					$comment['rid'], array('comments' => '`comments` + 1')
				);
				Factory::getLogic('msg')->leaveReportMsg($comment['to'], $cid);
				$msg = Factory::getMsg();
				if (empty($msg)) {
					Factory::setCookie('comment-report' . $comment['rid'], time());
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 加载举报评论
	 */
	public function loadCommentListAction()
	{
		$rid = $this->_request->getParam('rid', null);
		$page = $this->_request->getParam('p', '1');
		if ($rid) {
			$result = Factory::getView('website/report')->loadReportCommentList($rid, $page);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 加载举报评论子评论
	 */
	public function loadCommentChildrenAction()
	{
		$pid = $this->_request->getParam('pid', null);
		if ($pid) {
			$result = Factory::getView('website/report')->loadReportCommentChildren($pid);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 加载对象举报言论
	 */
	public function loadTargetCommentListAction()
	{
		$rid = $this->_request->getParam('rid', null);
		$customer = $this->_request->getParam('customer', null);
		$comments = Factory::getView('website/report')->loadTargetCommentList($rid, $customer);
		if ($comments) {
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode($comments);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 检测卖家是否被举报
	 */
	public function checkAction()
	{
		$check = $this->_request->getParam('check', null);
		if ($check) {
			$result = Factory::getLogic('website')->checkReported($check['uid'], $check['tel'], $check['qq']);
			if ($result) {
				if ($result['type'] == '1') {
					echo json_encode($check['username']);
				} else {
					echo json_encode($result['number']);
				}
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取举报评论对话记录
	 */
	public function showCommentDialogAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			$result = Factory::getView('website/report')->showCommentDialog($id);
			if ($result) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($result);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
}
