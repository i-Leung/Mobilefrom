<?php
class Logic_Store_Mobile
{
	/**
	 * 创建店铺型号出售记录
	 * @param 型号信息 $data
	 */
	public function createMobileModel($data)
	{
		$model = Factory::getResource('store/mobile/model');
		if ($model->create($data)) {
			return $model->getNewId();
		}
		return false;
	}

	/**
	 * 修改店铺型号出售信息
	 * @param 型号ID $id
	 * @param 出售信息 $data
	 */
	public function modifyMobileModel($id, $data)
	{
		if ($id) {
			return Factory::getResource('store/mobile/model')->modify(
				array('id = ?' => $id), $data
			);
		}
		return false;
	}

	/**
	 * 修改店铺型号热销状态
	 * @param 型号ID $id
	 * @param 店铺ID $store
	 * @param 出售信息 $data
	 */
	public function modifyMobileModelHot($id, $store, $data)
	{
		if ($id && $store) {
			$resource = Factory::getResource('store/mobile/model');
			if ($data) {
				$amount = $resource->getStoreHotAmount($store);
				if ($amount < 5) {
					return $resource->modify(
						array('id = ?' => $id), array('hot' => '1')
					);
				} else {
					return -1;
				}
			} else {
				return $resource->modify(
					array('id = ?' => $id), array('hot' => '0')
				);
			}
		}
		return false;
	}

	/**
	 * 修改店铺型号推荐状态
	 * @param 型号ID $id
	 * @param 店铺ID $store
	 * @param 推荐状态 $data
	 */
	public function modifyMobileModelRecommend($id, $store, $data)
	{
		if ($id && $store) {
			$resource = Factory::getResource('store/mobile/model');
			if ($data == '1') {
				$resource->modify(
					array('store_id = ?' => $store), array('recommend' => '0')
				);
			}
			return $resource->modify(
				array('id = ?' => $id), array('recommend' => $data)
			);
		}
		return false;
	}

	/**
	 * 创建店铺手机出售记录
	 * @param 手机信息 $data
	 */
	public function createMobileItem($data)
	{
		$mobile = Factory::getResource('store/mobile/item');
		return $mobile->create($data, 'multiple');
	}

	/**
	 * 修改店铺手机出售信息
	 * @param 手机出售信息ID $id
	 * @param 出售信息 $data
	 */
	public function modifyMobileItem($id, $data)
	{
		if ($id) {
			return Factory::getResource('store/mobile/item')->modify(
				array('id = ?' => $id), $data
			);
		}
		return false;
	}

	/**
	 * 根据手机型号出售信息ID删除手机出售信息
	 * @param 手机型号出售ID $smodel_id
	 */
	public function removeMobileItemBySMid($smodel_id)
	{
		if ($smodel_id) {
			return Factory::getResource('store/mobile/item')->remove(
				array('store_model_id = ?' => $smodel_id)
			);
		}
		return false;
	}
}
