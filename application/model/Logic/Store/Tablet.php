<?php
class Logic_Store_Tablet
{
	/**
	 * 创建店铺型号出售记录
	 * @param 型号信息 $data
	 */
	public function createTabletModel($data)
	{
		$model = Factory::getResource('store/tablet/model');
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
	public function modifyTabletModel($id, $data)
	{
		if ($id) {
			return Factory::getResource('store/tablet/model')->modify(
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
	public function modifyTabletModelHot($id, $store, $data)
	{
		if ($id && $store) {
			$resource = Factory::getResource('store/tablet/model');
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
	public function modifyTabletModelRecommend($id, $store, $data)
	{
		if ($id && $store) {
			$resource = Factory::getResource('store/tablet/model');
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
	 * 创建店铺平板出售记录
	 * @param 平板信息 $data
	 */
	public function createTabletItem($data)
	{
		$Tablet = Factory::getResource('store/tablet/item');
		return $Tablet->create($data, 'multiple');
	}

	/**
	 * 修改店铺平板出售信息
	 * @param 平板出售信息ID $id
	 * @param 出售信息 $data
	 */
	public function modifyTabletItem($id, $data)
	{
		if ($id) {
			return Factory::getResource('store/tablet/item')->modify(
				array('id = ?' => $id), $data
			);
		}
		return false;
	}

	/**
	 * 根据平板型号出售信息ID删除平板出售信息
	 * @param 平板型号出售ID $smodel_id
	 */
	public function removeTabletItemBySMid($smodel_id)
	{
		if ($smodel_id) {
			return Factory::getResource('store/tablet/item')->remove(
				array('store_model_id = ?' => $smodel_id)
			);
		}
		return false;
	}
}
