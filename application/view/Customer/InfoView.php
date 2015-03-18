<?php
class Customer_InfoView extends Project_View_Block_Abstract
{
	/**************************************************用户操作**************************************************/
	/**
	 * 获取用户信息
	 * @param 用户ID $customer_id
	 * @return false或用户信息
	 */
	public function getInfo($customer_id)
	{
		return $customer_id ? Factory::getResource('customer')->getInfo($customer_id) : null;
	}
	
	/**
	 * 获取用户所在组信息
	 * @param 组ID $group_id
	 * @return 组名称
	 */
	public function getGroupName($group_id = NULL)
	{
		$groups = array(
					'1' => '个人',
					'2' => '商家',
					'3' => '网站成员',
					'99' => '屏蔽用户'
				);
		return $group_id ? $groups[$group_id] : $groups;
	}
	
	/**
	 * 获取用户联系信息
	 * @param 用户ID $customer_id
	 * @return false或用户联系信息
	 */
	public function getContact($customer_id)
	{
		$contact = Factory::getResource('customer')->getContact($customer_id);
		return $contact ? unserialize($contact) : array();
	}
	
	/**
	 * 获取用户关注信息总量
	 * @return int
	 */
	public function getFavorAmount()
	{
		$amount = Factory::getResource('relation/customer/favor')->getFavorAmount($customer);
		return $amount ? $amount : 0;
	}

	/**
	 * 获取用户注册邮箱
	 * @param 用户ID $id
	 */
	public function getEmail($id)
	{
		return Factory::getResource('customer')->getEmail($id);
	}

	/**
	 * 获取用户第三方帐号绑定情况
	 * @param 用户ID $id
	 */
	public function getCustomerThirdParty($id)
	{
		return Factory::getResource('customer')->getCustomerThirdParty($id);
	}
	
	/**************************************************用户列表操作**************************************************/
	/**
	 * 获取用户列表
	 * @param 参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadCustomerList($params, $page = 1, $per = 10)
	{
		return Factory::getResource('customer')->loadCustomerList($params, $page, $per);
	}
	
	/**
	 * 获取用户总数
	 * @param 参数 $params
	 */
	public function getCustomerTotal($params)
	{
		return Factory::getResource('customer')->getCustomerTotal($params);
	}
	
	/**************************************************用户服务**************************************************/
	/**
	 * 获取用户服务类型
	 * @param 类型ID $id
	 */
	public function getServiceType($id = null)
	{
		$types = array(
			'1' => '解锁',
			'2' => '验机',
			'3' => '刷机'
		);
		return $id ? $types[$id] : $types;
	}

	/**
	 * 获取用户服务列表
	 * @param 用户ID $id
	 */
	public function loadCustomerService()
	{
		$id = Factory::getSession('customer/id');
		return $id ? Factory::getResource('website/service')->loadCustomerService($id) : false;
	}

	/**
	 * 获取服务内容
	 * @param 服务类型ID $sid
	 * @param 用户ID $customer_id
	 */
	public function getService($sid, $customer_id)
	{
		if ($result = Factory::getResource('website/service')->getService($sid, $customer_id)) {
			$result['contact'] = unserialize($result['contact']);
		}
		return $result;
	}

	/**
	 * 获取地区提供服务列表
	 * @param 地区ID $region
	 */
	public function loadRegionService($region)
	{
		if ($result = Factory::getResource('website/service')->loadRegionService($region)) {
			$services = array();
			foreach ($result as $item) {
				$services[$item['sid']][] = array(
					'id' => $item['id'], 
					'sid' => $item['sid'], 
					'customer_id' => $item['customer_id'], 
					'username' => $item['username'],
					'price' => $item['price']
				);
			}
			return $services;
		}
		return false;
	}

	/**
	 * 获取本地商家列表
	 */
	public function loadLocalStore()
	{
		$store = array('region' => array(), 'list' => array());
		if ($result = Factory::getResource('customer')->loadLocalStore()) {
			foreach ($result as &$item) {
				$contact = unserialize($item['contact']);
				if (!in_array($contact['region'], $store['region'])) {
					$store['region'][] = $contact['region'];
				}
				$store['list'][] = array(
					'id' => $item['id'],
					'username' => $item['username'],
					'region' => $contact['region'],
					'location' => $contact['location']
				);
			}
		}
		return $store;
	}
}
