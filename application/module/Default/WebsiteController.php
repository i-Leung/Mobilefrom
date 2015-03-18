<?php
class Default_WebsiteController extends Project_Controller_Action_Front
{
	/**
	 * 关于我们
	 */
	public function aboutAction()
	{
		$this->loadLayout('website');
		$this->renderLayout();
	}
	
	/**
	 * 联系我们
	 */
	public function contactAction()
	{
		$this->loadLayout('website');
		$this->renderLayout();
	}
	
	/**
	 * 广告合作
	 */
	public function cooperationAction()
	{
		$this->loadLayout('website');
		$this->renderLayout();
	}
	
	/**
	 * 机荒公告
	 */
	public function announcementAction()
	{
		$this->loadLayout('website');
		$this->renderLayout();
	}
	
	/**
	 * 问卷调查
	 */
	public function surveyAction()
	{
		$this->loadLayout('website');
		$this->renderLayout();
	}
	
	/**
	 * 网站建议
	 */
	public function suggestAction()
	{
		$qq = $this->_request->getParam('qq');
		$content = $this->_request->getParam('content');
		if ($content) {
			Factory::getLogic('website')->makeSuggestion($qq, $content);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				$subject = '机荒网收到网友留言反馈！';
				$body = '<h3>用户：' . $qq . ' 在机荒网留言“' . $content . '”</h3>';
				Factory::getLogic('plugin/mailer')->SendSMTP('2824673297@qq.com', '机荒网', $subject, $body);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 404页面
	 */
	public function notfoundAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 机荒网使用协议
	 */
	public function contractAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}
