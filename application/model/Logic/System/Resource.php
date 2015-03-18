<?php
class Logic_System_Resource
{
	/****************************************资源组操作************************************/
	/**
	 * 添加资源组
	 * @param 资源组信息 $params($pid, $label, $controller)
	 */
	public function addGroup($params)
	{
		if ($params) {
			return Factory::getResource('system/resource/group')->create($params);
		}
	}

	/**
	 * 修改资源组
	 * @param 资源组ID $id
	 * @param 资源组信息 $params($pid, $label, $controller)
	 */
	public function modifyGroup($id, $params)
	{
		if ($id && $params) {
			return Factory::getResource('system/resource/group')->modify(
				array('id = ?' => $id), $params
			);
		}
	}

	/**
	 * 修改资源组状态
	 * @param 资源组ID $id
	 * @param 资源组父ID $pid
	 * @param 资源组状态 $status
	 */
	public function changeGroupStatus($id, $pid, $status)
	{
		$result = false;
		if ($id) {
			$result = Factory::getResource('system/resource/group')->modify(
				array('id = ?' => $id), array('status' => $status)
			);
			if ($pid == '0') {
				$result &= Factory::getResource('system/resource/group')->modify(
					array('pid = ?' => $id), array('status' => $status)
				);
			}
		}
		return $result;
	}

	/****************************************资源操作************************************/
	/**
	 * 添加资源
	 * @param 资源信息 $params($group_id, $label, $action)
	 */
	public function addItem($params)
	{
		if ($params) {
			return Factory::getResource('system/resource')->create($params);
		}
	}

	/**
	 * 修改资源
	 * @param 资源ID $id
	 * @param 资源信息 $params($group_id, $label, $action)
	 */
	public function modifyItem($id, $params)
	{
		if ($id && $params) {
			return Factory::getResource('system/resource')->modify(
				array('id = ?' => $id), $params
			);
		}
	}

	/**
	 * 修改资源状态
	 * @param 资源组ID $id
	 * @param 资源组状态 $status
	 */
	public function changeItemStatus($id, $status)
	{
		if ($id) {
			return Factory::getResource('system/resource')->modify(
				array('id = ?' => $id), array('status' => $status)
			);
		}
	}
}
