<?php 
class Logic_System_Member
{
	/**************************************************网站成员群组操作**************************************************/
	/**
	 * 添加群组
	 * @param 群组名称 $label
	 * @param 职责描述 $description
	 */
	public function addGroup($label, $description)
	{
		$group = Factory::getResource('system/member/group');
		$result = $group->create(
					array(
						'label' => $label, 
						'description' => $description,
						'created_at' => time()
					)
				);	
		if ($result) {
			return $group->getNewId();
		}
		return false;
	}

	/**
	 * 添加群组所操作资源
	 * @param 群组ID $id
	 * @param 资源数组 $resources
	 */
	public function addGroupResource($id, $resources)
	{
		if ($id && $resources) {
			$authority = Factory::getResource('system/member/authority');
			foreach ($resources as $resource) {
				$authority->create(
					array('group_id' => $id, 'resource_id' => $resource)
				);
			}
			return true;
		}
		return false;
	}

	/**
	 * 修改群组
	 * @param 群组ID $id
	 * @param 群组名称 $label
	 * @param 职责描述 $description
	 */
	public function modifyGroup($id, $label, $description)
	{
		return Factory::getResource('system/member/group')->modify(
					array('id = ?' => $id),
					array('label' => $label, 'description' => $description)
				);	
	}

	/**
	 * 修改群组所操作资源
	 * @param 群组ID $id
	 * @param 资源数组 $resources
	 * @param 资源操作模式 $mode
	 */
	public function modifyGroupResource($id, $resources, $mode)
	{
		if ($id && $resources && $mode) {
			$authority = Factory::getResource('system/member/authority');
			switch ($mode) {
				case 'add':
					foreach ($resources as $resource) {
						$authority->create(
							array('group_id' => $id, 'resource_id' => $resource)
						);
					}
					break;
				case 'delete':
					foreach ($resources as $resource) {
						$authority->remove(
							array('group_id = ?' => $id, 'resource_id = ?' => $resource)
						);
					}
					break;
			}
			return true;
		}
		return false;
	}

	/**
	 * 修改群组状态
	 * @param 群组ID $id
	 * @param 状态 $status
	 */
	public function changeGroupStatus($id, $status)
	{
		if ($id) {
			return Factory::getResource('system/member/group')->modify(
					array('id = ?' => $id),
					array('status' => $status)
				);
		}
		return false;
	}

	/**
	 * 获取成员群组可操作资源列表
	 * @param 群组ID $id
	 */
	public function loadGroupResource($id)
	{
		if ($id) {
			$resources = array();
			$result = Factory::getResource('system/member/group')->loadGroupResource($id);
			if ($result) {
				foreach ($result as $item) {
					$resources[$item['controller'] . '/' . $item['action']] = $item['resource_id'];
				}
				return $resources;
			}
		}
		return false;
	}

	/**************************************************网站成员操作**************************************************/
	/**
	 * 创建网站成员记录
	 * @param 成员参数 $member
	 */
	public function createMember($member)
	{
		$member['pwd'] = $this->encrypt($member['pwd'], $member['authorized_at']);
		return Factory::getResource('system/member')->create($member);
	}

	/**
	 * 修改网站成员记录
	 * @param 成员ID $id
	 * @param 成员参数 $member
	 */
	public function modifyMember($id, $member)
	{
		return Factory::getResource('system/member')->modify(array('id = ?' => $id), $member);
	}

	/**
	 * 后台登录验证
	 * @param 客户ID $customer_id
	 * @param 后台登录密码 $pwd
	 */
	public function authMember($customer_id, $pwd)
	{
		if ($customer_id && $pwd) {
			$member = Factory::getResource('system/member')->authMember($customer_id);
			if ($member['pwd'] == $this->encrypt($pwd, $member['authorized_at'])) {
				unset($member['pwd']);
				unset($member['authorized_at']);	
				return $member;
			}
		}
		return false;
	}

	/**
	 * 后台登陆密码加密
	 * @param 密码 $pwd
	 * @param 授权时间 $authorized_at
	 */
	public function encrypt($pwd, $authorized_at)
	{
		return md5(md5($pwd . $authorized_at));
	}

	/**
	 * 记录网站成员操作历史
	 * @param 成员ID $member_id
	 * @param 资源ID $resource_id
	 */
	public function operate($member_id, $resource_id)
	{
		$operation = array();
		$operation['member_id'] = $member_id;
		$operation['resource_id'] = $resource_id;
		$operation['operated_at'] = time();
		return Factory::getResource('system/member/operation')->create($operation);
	}
}
