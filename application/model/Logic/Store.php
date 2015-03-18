<?php
class Logic_Store
{
	/**
	 * 商铺入驻提交申请资料
	 * @param 申请资料 $data
	 */
	public function createStore($data)
	{
		$addr = array('region_id' => $data['region'], 'addr' => $data['addr']);
		unset($data['region']);
		unset($data['addr']);
		$resource = Factory::getResource('store');
		if ($resource->create($data)) {
			$store_id = $resource->getNewId();
			if ($this->createStoreSetting($store_id) && $this->createStoreAddr($store_id, $addr)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 修改商铺资料
	 * @param 店铺ID $id
	 * @param 资料 $data
	 */
	public function modifyStore($id, $data)
	{
		$result = false;
		if ($id && $data) {
			if (isset($data['region'])) {
				$addr = array('region_id' => $data['region'], 'addr' => $data['addr']);
				unset($data['region']);
				unset($data['addr']);
				$result = $this->removeStoreAddr($id) && $this->createStoreAddr($id, $addr);
			}
			$result &= Factory::getResource('store')->modify(
				array('id = ?' => $id), $data
			);
			return $result;
		}
		return $result;
	}

	/**
	 * 创建店铺地址
	 * @param 店铺ID 
	 * @param 地址信息 $data
	 */
	public function createStoreAddr($id, $data)
	{
		$num = count($data['region_id']);
		for ($i = 1; $i <= $num; $i++) {
			$data['store_id'][] = $id;
		}
		return Factory::getResource('store/addr')->create($data, 'multiple');
	}

	/**
	 * 移除店铺地址
	 * @param 店铺ID 
	 */
	public function removeStoreAddr($id)
	{
		return Factory::getResource('store/addr')->remove(
			array('store_id = ?' => $id)
		);
	}

	/**
	 * 创建店铺设置记录
	 * @param 店铺ID $id
	 */
	public function createStoreSetting($id)
	{
		if ($id && Factory::getResource('store/setting')->create(array('store_id' => $id))) {
			return true;
		}
		return false;
	}

	/**
	 * 修改店铺设置
	 * @param 店铺ID $id
	 * @param 店铺设置 $data
	 */
	public function modifyStoreSetting($id, $data)
	{
		if ($id && Factory::getResource('store/setting')->modify(array('store_id = ?' => $id), $data)) {
			return true;
		}
		return false;
	}

	/**
	 * 创建店铺服务
	 * @param 服务信息 $data
	 */
	public function createStoreService($data)
	{
		return Factory::getResource('store/service')->create($data, 'multiple');
	}

	/**
	 * 移除店铺服务
	 * @param 店铺ID $store_id
	 */
	public function removeStoreService($store_id)
	{
		return Factory::getResource('store/service')->remove(
			array('store_id = ?' => $store_id)
		);
	}

	/**
	 * 记录访客访问情况
	 * @param 访问信息 $data
	 */
	public function markVisitor($data)
	{
		return Factory::getResource('store/visitor')->create($data);
	}

	/**
	 * 创建店铺活动
	 * @param 活动资料 $data
	 */
	public function createActivity($data)
	{
		return Factory::getResource('store/activity')->create($data);
	}

	/**
	 * 修改店铺活动
	 * @param 活动ID $id
	 * @param 店铺ID $store
	 * @param 活动资料 $data
	 */
	public function modifyActivity($id, $store, $data)
	{
		return Factory::getResource('store/activity')->modify(
			array('id = ?' => $id, 'store_id = ?' => $store), $data
		);
	}

	/**
	 * 记录店铺活动访问情况
	 * @param 活动ID $id
	 */
	public function markActivityClicks($id)
	{
		return Factory::getResource('store/activity')->modify(
			array('id = ?' => $id), array('clicks' => '`clicks` + 1')
		);
	}
}
