<?php
class System_ServiceView extends Project_View_Block_Abstract
{
	/**
	 * 获取服务列表
	 */
	public function loadServiceList()
	{
		if ($services = Factory::getResource('system/service')->loadServiceList()) {
			foreach ($services as &$item) {
				$item['description'] = htmlspecialchars_decode($item['description']);
			}
		}
		return $services;
	}

	/**
	 * 获取指定服务信息
	 * @param 服务ID $id
	 */
	public function getServiceInfo($id)
	{
		if ($service = Factory::getResource('system/service')->getServiceInfo($id)) {
			$service['description'] = htmlspecialchars_decode($service['description']);
		}
		return $service;
	}
}
