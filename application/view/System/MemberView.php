<?php
class System_MemberView extends Project_View_Block_Abstract
{
	/**************************************************网站成员群组操作**************************************************/
	/**
	 * 获取网站成员群组列表
	 */
	public function loadGroupList()
	{
		return Factory::getResource('system/member/group')->loadGroupList();
	}

	/**
	 * 获取网站成员群组信息
	 * @param 群组ID $id
	 */
	public function getGroup($id)
	{
		if ($id) {
			$result = Factory::getResource('system/member/group')->getGroup($id);
			if ($result) {
				$group = array();
				foreach ($result as $item) {
					$group['resource'][] = $item['resource_id'];
				}
				$group['id'] = $item['id'];
				$group['label'] = $item['label'];
				$group['description'] = $item['description'];
				$group['created_at'] = $item['created_at'];
				return $group;
			}
		}
		return false;
	}

	/**
	 * 获取群组内成员列表
	 * @param 群组ID $id
	 */
	public function loadGroupMember($id)
	{
		if ($id) {
			return Factory::getResource('system/member')->loadGroupMember($id);
		}
		return false;
	}

	/**************************************************网站成员操作**************************************************/
	/**
	 * 获取成员信息
	 * @param 成员ID $id
	 */
	public function getMember($id)
	{
		return Factory::getResource('system/member')->getMember($id);
	}

	/**
	 * 获取网站成员列表
	 * @param 组ID $gid
	 * @param 状态 $status
	 */
	public function loadMemberList($gid, $status)
	{
		return Factory::getResource('system/member')->loadMemberList($gid, $status);
	}

	/**
	 * 获取网站成员后台操作历史
	 * @param 网站成员ID $id
	 * @param 时间范围 $range
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMemberOperationList($id, $range, $page = 1, $per = 50)
	{
		return Factory::getResource('system/member/operation')->loadMemberOperationList($id, $range, $page, $per);
	}

	/**
	 * 获取网站成员后台操作历史总是
	 * @param 网站成员ID $id
	 * @param 时间范围 $range
	 */
	public function getMemberOperationTotal($id, $range)
	{
		return Factory::getResource('system/member/operation')->getMemberOperationTotal($id, $range);
	}
}
