<?php
class Customer_AccountController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$action = $this->_request->getActionName();
		$isLoged = Factory::isLoged();
		if (
			(!$isLoged && in_array($action, array('info-base', 'info-contact', 'set-password', 'email-verify', 'thumb', 'account-binding', 'apply-service', 'myservice')))
			 || 
			($isLoged && in_array($action, array('login', 'register', 'binding')))
			) {
			$this->redirect(Factory::getBaseUrl());
		}
	}
	
	/**
	 * 注册页面
	 */
	public function registerAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 注册操作
	 */
	public function registerPostAction()
	{
		$data = $this->_request->getParam('customer');
		foreach ($data as &$value) {
			$value = trim($value);
		}
		$validator = Factory::getLogic('validator');
		if (
			$validator->checkUsername($data['username'])
			 && 
			$validator->checkEmail($data['email'])
			 && 
			$validator->checkPwd($data['pwd'], $data['repwd'])
		) {
			$customer = Factory::getLogic('customer');
			unset($data['repwd']);
			$data['pwd'] = $customer->encryptPwd($data['pwd']);
			$data['registered_at'] = $data['logon_latest'] = time();
			if ($id = $customer->create($data)) {
				$session = array('id' => $id, 'username' => $data['username'], 'group' => '1', 'verified' => '0');
				Factory::setSession('customer', $session);
				//发送欢迎信息
			    $notice = array(
					'type' => '3',
					'title' => '欢迎来到机荒网！',
					'content' => '亲爱的用户，欢迎注册访问机荒网，机荒网致力为用户提供最方便、最快捷、也就会最有保障的手机交易服务。
								您的支持，是我们进步的源动力！',
					'created_at' => time()
				);	
				Factory::getLogic('msg')->sendNotice($id, $notice);
				if ($this->_request->getParam('remember')) {
					Factory::setCookie('customer_id', $id);
				}
				echo json_encode($id);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 登录页面
	 */
	public function loginAction()
	{
		//暂无页面
//		$this->loadLayout();
//		$this->renderLayout();
	}
	
	/**
	 * 登录操作
	 */
	public function loginPostAction()
	{
		$customer = $this->_request->getParam('customer');
		$pwd = $this->_request->getParam('pwd');
		$model = Factory::getLogic('customer');
		if ($customer && $pwd && $info = $model->checkAuth($customer, $pwd)) {
			$data = array(
						'logon_times' => '`logon_times` + 1',
						'logon_latest' => time()
					);
			$model->modify($info['id'], $data);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setSession('customer', $info);
				if ($this->_request->getParam('remember')) {
					Factory::setCookie('customer_id', $info['id']);
				}
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 注销操作
	 */
	public function logoutAction()
	{
		Factory::unsetTrace();
		$this->redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * 重设密码
	 */
	public function setPasswordAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 *基本信息
	 */
	public function infoBaseAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 *联系信息
	 */
	public function infoContactAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 查询用户名或邮箱是否已存在
	 */
	public function existAction()
	{
		$customer = $this->_request->getParam('customer', null);
		if ($customer) {
			$isExist = Factory::getLogic('customer')->isExist($customer);
			$msg = Factory::getMsg();
			if (empty($msg) && $isExist) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 用户重命名
	 */
	public function renameAction()
	{
		$username = $this->_request->getParam('newname', null);
		$customer_id = Factory::getSession('customer/id');
		if ($username && $customer_id) {
			$result = Factory::getLogic('customer')->rename($customer_id, $username);
			$msg = Factory::getMsg();
			if (empty($msg) && $result) {
				Factory::setSession('customer/username', $username);
//				Factory::setCookie('username', $username);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 修改联系信息
	 */
	public function editContactAction()
	{
		$contact = $this->_request->getParam('contact', null);
		$customer_id = Factory::getSession('customer/id');
		if ($contact && $customer_id) {
			$result = Factory::getLogic('customer')->modify(
						$customer_id, 
						array('contact' => serialize($contact))
					);
			$msg = Factory::getMsg();
			if (empty($msg) && $result) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 密码检测
	 */
	public function checkPwdAction()
	{
		$pwd = $this->_request->getParam('pwd', null);
		$username = Factory::getSession('customer/username');
		if ($pwd && $username) {
			$result = Factory::getLogic('customer')->checkAuth($username, $pwd);
			$msg = Factory::getMsg();
			if (empty($msg) && $result) {
				Factory::setSession('ischecked', 1);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 密码重设
	 */
	public function repwdAction()
	{
		$pwd = $this->_request->getParam('pwd', null);
		$newpwd = $this->_request->getParam('newpwd', null);
		$repwd = $this->_request->getParam('repwd', null);
		$ischecked = Factory::getSession('ischecked');
		if ($pwd && $newpwd && $repwd && $newpwd == $repwd && $ischecked) {
			$customer = Factory::getLogic('customer');
			$result = $customer->modify(
						Factory::getSession('customer/id'), 
						array('pwd' => $customer->encryptPwd($newpwd))
					);
			$msg = Factory::getMsg();
			if (empty($msg) && $result) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 获取用户联系信息
	 */
	public function getContactAction()
	{
		$customer_id = Factory::getSession('customer/id');
		if ($customer_id) {
			$contact = Factory::getView('customer/info')->getContact($customer_id);
			$msg = Factory::getMsg();
			if (empty($msg) && $contact) {
				echo json_encode($contact);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 找回密码请求
	 */
	public function forgetPwdAction()
	{
		$email = $this->_request->getParam('email');
		$username = $this->_request->getParam('username');
		$customer = Factory::getLogic('customer');
		if ($id = $customer->checkUsernameEmailMatch($username, $email)) {
			$pwd = rand(100000, 999999);
			$customer->modify($id, array('pwd' => $customer->encryptPwd($pwd)));
			$subject = '机荒网找回密码结果通知';
			$body = '<p>
						亲爱的机荒网用户"<span style="color:blue;">' . $username . '</span>"，您的找回密码请求已被受理，系统将重设您的账号密码，新的密码如下：<span style="color:red;">' . $pwd . '</span>。
					</p>
					<p>
						为了您的账号安全，请您尽快使用以上密码登录机荒网，并重新设置密码。
						<a style="color:blue;" target="_blank" href="http://www.mobilefrom.com">点击立马登陆</a>
					</p>
					<p>
						若有疑问，请联系<a style="color:blue;" href="http://www.mobilefrom.com/default/website/contact">机荒网客服</a>。
					</p>
					<p>
						谢谢您对机荒网的支持，我们将会继续努力，为您提供最方便、最快捷、也将会最安全的手机交易服务。
					</p>';
			Factory::getLogic('plugin/mailer')->SendSMTP($email, $username, $subject, $body);
			//发送操作提醒
			$notice = array(
				'type' => '2',
				'title' => '找回密码操作提醒',
				'content' => '亲爱的用户，您的找回密码请求已提交并受理，系统已将您的登录密码重置并将新的密码发送到你邮箱，请及时查收！',
				'created_at' => time()
			);	
			Factory::getLogic('msg')->sendNotice($id, $notice);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 帐号绑定页面
	 */
	public function bindingAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * 注册绑定
	 */
	public function ajaxBindingRegisterAction()
	{
		$data = $this->_request->getParam('customer');
		foreach ($data as &$value) {
			$value = trim($value);
		}
		$validator = Factory::getLogic('validator');
		if (
			$validator->checkUsername($data['username'])
			 && 
			$validator->checkEmail($data['email'])
			 && 
			$validator->checkPwd($data['pwd'], $data['repwd'])
		) {
			$customer = Factory::getLogic('customer');
			unset($data['repwd']);
			$data['pwd'] = $customer->encryptPwd($data['pwd']);
			$data['registered_at'] = $data['logon_latest'] = time();
			//绑定操作
			switch ($this->_request->getParam('third', null)) {
				case 'sina':
					$data['sina'] = $this->_request->getParam('binding', 0);
					break;
				case 'tencent':
					$data['tencent'] = $this->_request->getParam('binding', 0);
					break;
				default:
					break;
			}
			if ($id = $customer->create($data)) {
				$session = array('id' => $id, 'username' => $data['username'], 'group' => $data['group']);
				Factory::setSession('customer', $session);
				//发送欢迎信息
			    $notice = array(
					'type' => '3',
					'title' => '欢迎来到机荒网！',
					'content' => '亲爱的用户，欢迎注册访问机荒网，您的账号已经绑定成功，以后可以通过您所绑定的第三方应用在机荒网登陆了！机荒网致力为用户提供最方便、最快捷、也就会最有保障的手机交易服务。
								您的支持，是我们进步的源动力！',
					'created_at' => time()
				);	
				Factory::getLogic('msg')->sendNotice($id, $notice);
				Factory::setCookie('customer_id', $id);
				echo json_encode($id);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 登陆绑定
	 */
	public function ajaxBindingLoginAction()
	{
		$customer = $this->_request->getParam('customer');
		$pwd = $this->_request->getParam('pwd');
		$model = Factory::getLogic('customer');
		if ($customer && $pwd && $info = $model->checkAuth($customer, $pwd)) {
			$data = array(
						'logon_times' => '`logon_times` + 1',
						'logon_latest' => time()
					);
			//绑定操作
			switch ($this->_request->getParam('third', null)) {
				case 'sina':
					$data['sina'] = $this->_request->getParam('binding', 0);
					break;
				case 'tencent':
					$data['tencent'] = $this->_request->getParam('binding', 0);
					break;
				default:
					break;
			}
			$model->modify($info['id'], $data);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				Factory::setSession('customer', $info);
				//发送绑定成功信息
			    $notice = array(
					'type' => '3',
					'title' => '帐号绑定成功！',
					'content' => '亲爱的用户，您的账号已经绑定成功，以后可以通过您所绑定的第三方应用在机荒网登陆了！机荒网致力为用户提供最方便、最快捷、也就会最有保障的手机交易服务。您的支持，是我们进步的源动力！',
					'created_at' => time()
				);	
				Factory::getLogic('msg')->sendNotice($info['id'], $notice);
				Factory::setCookie('customer_id', $info['id']);
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 新浪登陆操作
	 */
	public function sinaAction()
	{
		$key = '2782468044';
		$secrect = 'e8618f6dd89dfe07584f56217f87488c';
		$back = Factory::getUrl('customer/account/sina');
		if ($code = $this->_request->getParam('code', null)) {//获取授权号码
			$url = 'https://api.weibo.com/oauth2/access_token?client_id=' . $key . '&client_secret=' . $secrect . '&grant_type=authorization_code&redirect_uri=' . $back . '&code=' . $code;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
			$json = json_decode(curl_exec($ch));
			curl_close($ch);				
			if ($uid = $json->{'uid'}) {//微博ID
				if ($customer_id = Factory::getSession('customer/id')) {
					Factory::getLogic('customer')->modify($customer_id, array('sina' => $uid));
					$this->redirect(Factory::getUrl('customer/account/account-binding'));
				} else {
					if ($info = Factory::getLogic('customer')->getCustomerByThirdParty($uid, 'sina')) {//若以绑定则直接登录
						Factory::setSession('customer', $info);
						Factory::setCookie('customer_id', $info['id']);
						$this->redirect(Factory::getBaseUrl());
					} else {
						$url = Factory::getUrl('customer/account/binding') . 'third=sina&binding=' . $uid;
						$this->redirect($url);
					}
				}
			} else {//跳转绑定页面
				Factory::setMsg(json_encode($json));
				$this->redirect(Factory::getBaseUrl());
			}
		} elseif ($this->_request->getParam('act', null) == 'authorize') {//授权
			$url = 'https://api.weibo.com/oauth2/authorize?client_id=' . $key . '&response_type=code&redirect_uri=' . $back;
			$this->redirect($url);
		} else {
			$this->redirect(Factory::getBaseUrl());
		}
	}

	/**
	 * 腾讯登陆操作
	 */
	public function tencentAction()
	{
		$id = '100489084';	
		$key = 'a0e5df2c7111048b6b22fcb43b25f0f4';
		$back = Factory::getUrl('customer/account/tencent');
		if ($this->_request->getParam('act', null) == 'authorize') {//获取Authorization Code
			$url = 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=' . $id . '&redirect_uri=' . $back . '&state=production';
			$this->redirect($url);
		} elseif (($this->_request->getParam('state', null) == 'production')) {
			//通过Authorization Code获取Access Token
			$code = $this->_request->getParam('code', null); 
			$url ='https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=' . $id . '&client_secret=' . $key . '&code=' . $code . '&redirect_uri=' . $back;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
			$result = curl_exec($ch);
			curl_close($ch);				
			$from = strpos($result, '=') + 1;
			$to = strpos($result, '&');
			$token = substr($result, $from, ($to - $from));
			//根据access_token获得对应用户身份的openid
			$url = 'https://graph.qq.com/oauth2.0/me?access_token=' . $token;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
			$json = str_replace(array('callback( ', ' );'), array(''), curl_exec($ch));
			$json = json_decode($json);
			curl_close($ch);
			$openid = $json->{'openid'};
			$client_id = $json->{'client_id'};
			if ($client_id == $id && $openid) {//检测是否有凭证返回
				if ($customer_id = Factory::getSession('customer/id')) {
					Factory::getLogic('customer')->modify($customer_id, array('tencent' => $openid));
					$this->redirect(Factory::getUrl('customer/account/account-binding'));
				} else {
					if ($info = Factory::getLogic('customer')->getCustomerByThirdParty($openid, 'tencent')) {//若以绑定则直接登录
						Factory::setSession('customer', $info);
						Factory::setCookie('customer_id', $info['id']);
						$this->redirect(Factory::getBaseUrl());
					} else {//跳转绑定页面
						$url = Factory::getUrl('customer/account/binding') . 'third=tencent&binding=' . $openid;
						$this->redirect($url);
					}
				}
			} else {
				Factory::setMsg(json_encode($json));
				$this->redirect(Factory::getBaseUrl());
			}
		} else {
			$this->redirect(Factory::getBaseUrl());
		}
	}

	/**
	 * 邮箱验证
	 */
	public function emailVerifyAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}

	/**
	 * 发送验证邮件
	 */
	public function sendVerifyEmailAction()
	{
		$step = $this->_request->getParam('step', '0');
		$newmail = $this->_request->getParam('newmail', '');
		$customer = Factory::getSession('customer');
		if ($newmail) {
			$email = $newmail;
		} else {
			$email = Factory::getView('customer/info')->getEmail($customer['id']);
		}
		if ($customer && $email) {
			try {
				$code = rand(100000, 999999);
				Factory::setSession('verify', $code);
				$link = '';
				switch ($step) {
					case '1':
					case '2':
						$link = 'http://www.mobilefrom.com/customer/account/email-verify?cfrom=email&step=' . $step . '&code=' . $code;
						break;
					case '3':
						$link = 'http://www.mobilefrom.com/customer/account/email-verify?cfrom=email&newmail=' . $newmail . '&step=' . $step . '&code=' . $code;
						break;
					default:
						$link = 'http://www.mobilefrom.com/customer/account/email-verify?cfrom=email&code=' . $code;
						break;
				}
				$subject = '机荒网：这是您的邮箱验证邮件，快点验证吧！';
				$body = '<p>
							点击以下链接，即刻完成机荒网的邮箱验证操作！
							<a style="color:red;" href="' . $link . '">马上点击，完成验证</a>
						</p>
						<p>
							<a style="color:red;font-weight:bold;" href="http://www.mobilefrom.com">机荒网（http://www.mobilefrom.com）</a>是顺德唯一专注闲置手机交易平台，专门提供有助于顺德地区手机交易的线上服务。
						</p>
						<p>
							关注机荒网的新浪官方微博（<a style="color:red;font-weight:bold;" href="http://weibo.com/mobilefrom">@机荒网</a>），将会有机荒网的最新动态，同时我们也会转发在机荒网上发布的手机信息，希望各位买手机或卖手机的朋友能够关注我们，谢谢！！！
						</p>';
				Factory::getLogic('plugin/mailer')->SendSMTP($email, $customer['username'], $subject, $body);
				echo json_encode(1);
				exit(0);
			} catch (Exception $e) {
				Factory::setMsg($e->getMessage());
			}
		}
		echo json_encode(0);
	}

	/**
	 * 设置头像
	 */
	public function thumbAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}

	/**
	 * 上传图像
	 */
	public function uploadThumbAction()
	{
		//不存在当前上传文件则上传
		if(!file_exists($_FILES['upload']['name'])) {
			$tmp = 'var/tmp/thumb-';
			$ext = pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
			if (in_array($ext, array('jpg', 'JPG'))) {
				try {
					$this->_resize($_FILES['upload']['tmp_name'], $tmp . Factory::getSession('customer/id') . '.' . $ext);
					//输出图片文件<img>标签
					echo json_encode('/var/tmp/thumb-' . Factory::getSession('customer/id') . '.' . $ext);
					exit(0);
				} catch (Exception $e) {
					Factory::setMsg($e->getMessage());
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 调整图片尺寸
	 */
	private function _resize($src, $dest)
	{
		$org = imagecreatefromjpeg($src);
		list($width, $height) = getimagesize($src);
		if ($width >= $height) {
			$newheight = 200;
			$newwidth = ($width / $height) * $newheight;
		} else {
			$newwidth = 200;
			$newheight = ($height / $width) * $newwidth;
		}
		$tmp = imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp, $org, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); 
		imagejpeg($tmp, $dest, 100);
		imagedestroy($org);
		imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created when the request
	}

	/**
	 * 上传图像
	 */
	public function saveThumbAction()
	{
		$target = $this->_request->getParam('target');
		if ($target) {
			$src_x = $target['x1'];
			$src_y = $target['y1'];
			$src_w = $target['x2'] - $target['x1'];
			$src_h = $target['y2'] - $target['y1'];
		}
		$customer_id = Factory::getSession('customer/id');
		$src = 'var/tmp/thumb-' . $customer_id . '.jpg';
		$img = imagecreatefromjpeg($src);
		list($width, $height) = getimagesize($src);
		$new_img = ImageCreateTrueColor(200, 200);
		if (@imagecopyresampled($new_img, $img, 0, 0, $src_x, $src_y, 200, 200, $src_w, $src_h)) {
			imagejpeg($new_img, 'upload/thumbnail/' . $customer_id . '.jpg', 100);
			echo json_encode(1);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 帐号绑定
	 */
	public function accountBindingAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
}
