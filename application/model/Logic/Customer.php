<?php
class Logic_Customer
{
	private $_customerResource = NULL;
	
	public function __construct()
	{
		$this->_customerResource = Factory::getResource('customer');
	}
	/**
	 * 判断用户名或邮箱存在性
	 * @param 用户名或邮箱 $customer
	 */
	public function isExist($customer)
	{
		return $this->_customerResource->isExist($customer);
	}
	
	/**
	 * 加密用户密码
	 * @param 密码 $pwd
	 */
	public function encryptPwd($pwd)
	{
		return md5(md5($pwd));
	}
	
	/**
	 * 创建用户记录
	 * @param 用户信息 $data
	 */
	public function create($data)
	{
		$customer = $this->_customerResource;
		if ($customer->create($data)) {
			$id = $customer->getNewId();
			mkdir('upload/mobile/' . $id, 0777);
			mkdir('upload/mobile/thumb/' . $id, 0777);
			return $id;
		}
		return false;
	}
	
	/**
	 * 修改用户信息
	 * @param 筛选条件 $condition
	 * @param 需要更新的数据 $data
	 * @return boolean
	 */
	public function modify($id, $data)
	{
		return $this->_customerResource
				->modify(
					array('id = ?' => $id), $data
				);
	}
	
	/**
	 * 检测登录信息是否正确
	 * @param 用户名/邮箱 $customer
	 * @param 密码 $pwd
	 * @return false或对应用户信息
	 */
	public function checkAuth($customer, $pwd)
	{
		$pwd = $this->encryptPwd($pwd);
		return $this->_customerResource->checkAuth($customer, $pwd);
	}
	
	/**
	 * 获取用户信息
	 * @param 用户ID $customer_id
	 * @return false或用户信息
	 */
	public function getInfo($customer_id)
	{
		return $customer_id ? $this->_customerResource->getInfo($customer_id) : null;
	}
	
	/**
	 * 更新最新登录信息
	 * @param 用户ID $customer_id
	 * @return boolean
	 */
	public function reflashLogonLatest($customer_id)
	{
		$data = array(
					'logon_latest' => time(),
					'logon_times' => '`logon_times` + 1'
				);
		return $this->_customerResource
				->modify(
						array('id = ?' => $customer_id), $data
				);
	}
	
	/**
	 * 用户重命名
	 * @param 用户ID $customer_id
	 * @param 用户名 $username
	 * @return boolean
	 */
	public function rename($customer_id, $username)
	{
		if (!$this->isExist($username)) {
			$this->modify($customer_id, array('username' => $username));
			return true;
		}
		return false;
	}
	
	/**
	 * 添加关注
	 * @param 用户ID $customer_id
	 * @param 信息类型 $type
	 * @param 主题ID $topic_id
	 * @return boolean
	 */
	public function addFavor($customer_id, $type, $topic_id)
	{
		return Factory::getResource('relation/customer/favor')->create(
			array(
				'customer_id' => $customer_id,
				'type' => $type,
				'topic_id' => $topic_id,
				'favor_at' => time()
			)
		);
	}
	
	/**
	 * 删除关注
	 * @param 用户ID $customer_id
	 * @param 信息类型 $type
	 * @param 主题ID $topic_id
	 * @return boolean
	 */
	public function removeFavor($customer_id, $type, $topic_id)
	{
		return Factory::getResource('relation/customer/favor')->remove(
			array(
				'customer_id = ?' => $customer_id,
				'type = ?' => $type,
				'topic_id = ?' => $topic_id
			)
		);
	}

	/**
	 * 检测用户名、邮箱是否匹配
	 * 用于找回密码
	 * @param 用户名 $username
	 * @param 邮箱地址 $email
	 * @return boolean
	 */
	public function checkUsernameEmailMatch($username, $email)
	{
		return $this->_customerResource->checkUsernameEmailMatch($username, $email);
	}

	/**
	 * 根据第三方凭证获取用户信息
	 * @param 绑定凭证 $binding
	 * @param 第三方平台 $third
	 */
	public function getCustomerByThirdParty($binding, $third)
	{
		if ($binding && $third) {
			return $this->_customerResource->getCustomerByThirdParty($binding, $third);
		}
		return false;
	}

	/**
	 * 验证邮箱返回码
	 * @param 返回码 $code
	 */
	public function verifyEmail($code)
	{
		if ($code == Factory::getSession('verify')) {
			if ($this->modify(Factory::getSession('customer/id'), array('verified' => '1'))) {
				Factory::setSession('customer/verified', '1');
				return true;
			}
		}
		return false;
	}

	/**
	 * 接受服务申请
	 * @param 服务数据 $data
	 */
	public function createService($data)
	{
		return Factory::getResource('website/service')->create($data);
	}

	/**
	 * 修改服务申请
	 * @param 服务ID $id
	 * @param 服务信息 $data
	 */
	public function modifyService($id, $data)
	{
		return Factory::getResource('website/service')->modify(
			array('id = ?' => $id), $data
		);
	}

	/**
	 * 删除服务申请
	 * @param 服务ID $id
	 */
	public function removeService($id)
	{
		return Factory::getResource('website/service')->remove(
			array('id = ?' => $id)
		);
	}

	/**
	 * 判断对服务是否有操作权
	 * @param 服务ID $id
	 * @param 用户ID $customer
	 */
	public function isMyService($id, $customer)
	{
		return Factory::getResource('website/service')->isMyService($id, $customer);
	}
}
