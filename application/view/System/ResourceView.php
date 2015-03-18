<?php
class System_ResourceView extends Project_View_Block_Abstract
{
	/****************************************资源组操作************************************/
	/**
	 * 获取资源组列表
	 * @param 父组ID $pid
	 */
	public function loadGroupList($pid = 0)
	{
		return Factory::getResource('system/resource/group')->loadGroupList($pid);
	}

	/**
	 * 获取资源组信息
	 * @param 组ID $id
	 */
	public function getGroupInfo($id)
	{
		return Factory::getResource('system/resource/group')->getGroupInfo($id);
	}

	/****************************************资源操作************************************/
	/**
	 * 获取资源列表 
	 * @param 所属资源组ID $group_id
	 * @param 资源状态 $status 
	 */
	public function loadResourceList($group_id, $status)
	{
		if ($group_id) {
			return Factory::getResource('system/resource')->loadResourceList($group_id, $status);
		}
	}

	/**
	 * 获取资源信息 
	 * @param 所属资源ID $id
	 */
	public function getResource($id)
	{
		if ($id) {
			return Factory::getResource('system/resource')->getResource($id);
		}
	}	

	/**
	 * 获取当前资源父子关系 
	 * @param 所属资源ID $id
	 */
	public function getCurrentResource($id)
	{
		return Factory::getResource('system/resource')->getCurrentResource($id);
	}

	/**
	 * 获取资源操作历史
	 * @param 资源ID $id
	 * @param 时间范围 $range
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadResourceOperationList($id, $range, $page = 1, $per = 50)
	{
		return Factory::getResource('system/member/operation')->loadResourceOperationList($id, $range, $page, $per);
	}

	/**
	 * 获取资源操作历史总数
	 * @param 资源ID $id
	 * @param 时间范围 $range
	 */
	public function getResourceOperationTotal($id, $range)
	{
		return Factory::getResource('system/member/operation')->getResourceOperationTotal($id, $range);
	}
	
	/**
	 * 获取网站资源树
	 */
	public function loadResourceTree()
	{
		$tree = array();
		$result = Factory::getResource('system/resource')->loadResourceTree();
		foreach ($result as $item) {
			if ($item['group']) {
				if ($item['id']) {
					$tree[$item['mgroup']][$item['group']][] = array(
						'id' => $item['id'],
						'item' => $item['item'],
						'url' => $item['controller'] . '/' . $item['action']
					);
				} else {
					if (!isset($tree[$item['mgroup']][$item['group']])) {
						$tree[$item['mgroup']][$item['group']] = null;
					}
				}
			} else {
				if (!isset($tree[$item['mgroup']])) {
					$tree[$item['mgroup']] = null;
				}
			}
		}
		return $tree;
	}
}
