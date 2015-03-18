<?php
class Monitor_EntranceController extends Project_Controller_Action_Admin
{
	/**
	 * 后台登录
	 */
	public function indexAction()
	{
		$member = Factory::getSession('member');
		if ($member['id'] && $member['group']) {
			$this->redirect('/monitor');
		}
		$this->loadLayout('login');
		$this->renderLayout();
	}

	/**
	 * 网站成员后台登录
	 */
	public function loginAction()
	{
		$pwd = $this->_request->getParam('pwd');
		$verify = $this->_request->getParam('verify');
		if ($pwd && $verify && Factory::getSession('verify') == $verify) {
			$logic = Factory::getLogic('system/member');
			$member = $logic->authMember(Factory::getSession('customer/id'), $pwd);	
			if ($member) {
				Factory::setSession('member', $member);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 网站成员后台注销
	 */
	public function logoutAction()
	{
		Factory::setSession('member', null);
		$this->redirect('/monitor/entrance');
	}

	/**
	 * 获取验证码
	 */
	public function verifyAction()
	{
		$num='';
		for ($i = 0; $i < 4; $i++) {
			$num .= dechex(rand(0,10));  //dechex函数是十进制转会二进制
		}
		Factory::setSession('verify', $num);  //用session记住这个验证数字		
		header("Content-type:image/PNG");
		$im = imagecreate(50,20);  //创建一个画布
		$back = imagecolorallocate($im, 255, 255, 255);//字体颜色
		$gray = ImageColorAllocate($im, 2, 23, 58); //背景色
		imagefill($im,0,0,$gray);   //填充颜色
		$style = array($back,$back,$back,$back,$back,$gray,$gray,$gray,$gray,$gray); //生成数组
		imagesetstyle($im,$style);    //设定画线风格
		$y1 = rand(0,20);
		$y2 = rand(0,20);
		$y3 = rand(0,20);
		$y4 = rand(0,20);
		$str = rand(3,8);
		for ($i = 0; $i < strlen($_SESSION['verify']); $i++) {
			$strp = rand(1,4);
			imagestring($im,4,$str,$strp,substr($num,$i,1),$back);
			$str += rand(8,12);
		}
		ImagePNG($im);
		Imagedestroy($im);	
	}

}
