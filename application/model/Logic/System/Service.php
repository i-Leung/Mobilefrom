<?php
class Logic_System_Service
{
	/**
	 * 创建服务
	 * @param 服务信息 $data
	 */
	public function createService($data)
	{
		return Factory::getResource('system/service')->create($data);
	}

	/**
	 * 修改服务信息
	 * @param 服务ID $id
	 * @param 服务信息 $data
	 */
	public function modifyService($id, $data)
	{
		if ($id) {
			return Factory::getResource('system/service')->modify(
				array('id = ?' => $id), $data
			);
		}
	}
}
