<?php
class Store_ItemView extends Project_View_Block_Abstract
{
	/**
	 * 获取店铺信息
	 * @param 店铺ID $id
	 */
	public function getStoreInfo($id)
	{
		if ($id && $store = Factory::getResource('store')->getStoreInfo($id)) {
			global $CStatic;
			$store['created_at'] = date('Y年m月d日', $store['created_at']);
			$store['gallery'] = $store['gallery'] ? explode(';', substr($store['gallery'], 0, -1)) : '';
			$store['introduction'] = htmlspecialchars_decode($store['introduction']);
			if ($addrs = Factory::getResource('store/addr')->getStoreAddr($id)) {
				global $CStatic;
				$store['addr'] = array();
				foreach ($addrs as $item) {
					$store['addr'][] = array(
						'region' => $CStatic['region'][$item['region_id']],
						'addr' => $item['addr']
					);
				}
			}
			return $store;
		}
		return false;
	}

	/**
	 * 获取店铺地图
	 * @param 店铺ID $id
	 */
	public function getStoreMap($id)
	{
		if ($id && $map = Factory::getResource('store/setting')->getStoreMap($id)) {
			return $map;
		}
		return false;
	}

	/**
	 * 获取店铺公用信息
	 * @param 店铺ID $id
	 */
	public function getStorePublicInfo($id)
	{
		if ($id && $info = Factory::getResource('store')->getStorePublicInfo($id)) {
			$info['working'] = $info['working'] ? array_reverse(unserialize($info['working'])) : '';
			$info['tel'] = unserialize($info['tel']);
			$info['qq'] = unserialize($info['qq']);
			return $info;
		}
		return false;
	}

	/**
	 * 判断店铺是否属于用户的
	 * @param 店铺ID $store_id
	 * @param 用户ID $customer_id
	 */
	public function isMyStore($store_id, $customer_id)
	{
		if ($customer_id && $store_id) {
			return Factory::getResource('store')->isMyStore($store_id, $customer_id);
		}
	}

	/**
	 * 获取商店服务列表
	 * @param 店铺ID $store
	 */
	public function loadStoreServiceList($store)
	{
		if ($store && $services = Factory::getResource('store/service')->loadStoreServiceList($store)) {
			foreach ($services as &$item) {
				$item['description'] = htmlspecialchars_decode($item['description']);
			}
			return $services;
		}
		return false;
	}

	/**
	 * 获取商店服务标题列表
	 * @param 店铺ID $store
	 */
	public function loadStoreServiceTitleList($store)
	{
		if ($store) {
			return Factory::getResource('store/service')->loadStoreServiceTitleList($store);
		}
		return false;
	}

	/**
	 * 获取商家微博帐号
	 * @param 店铺ID $id
	 */
	public function getSinaAccount($id)
	{
		if ($id) {
			return Factory::getResource('store')->getSinaAccount($id);
		}
		return false;
	}

	/**
	 * 获取店铺优惠活动
	 * @param 店铺ID
	 */
	public function getStoreActivity($id)
	{
		if ($id && $result = Factory::getResource('store/activity')->getStoreActivity($id)) {
			return $result;
		}
		return false;
	}
	
	/*******************************************店铺手机*******************************************/
	/**
	 * 店铺是否有在售手机信息
	 * @param 店铺ID $id
	 */
	public function hasSellingMobile($id)
	{
		return Factory::getResource('store/mobile/model')->hasSellingMobile($id);
	}

	/**
	 * 店铺列表页获取店铺在售型号
	 * @param 店铺ID $id
	 * @param 显示数量 $amount
	 */
	public function getSellingMobileModelList($id, $amount = 6)
	{
		return Factory::getResource('store/mobile/model')->getSellingMobileModelList($id, $amount);
	}

	/**
	 * 店铺手机页获取在售型号列表
	 * @param 店铺ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadSellingMobileModelList($id, $page = 1, $per = 9)
	{
		$sort = Factory::getSession('store-sort-' . $id, null);
		$filter = Factory::getSession('store-filter-' . $id, null);
		return Factory::getResource('store/mobile/model')->loadSellingMobileModelList($id, $filter, $sort, $page, $per);
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getSellingMobileModelTotal($id)
	{
		$sort = Factory::getSession('store-sort-' . $id, null);
		$filter = Factory::getSession('store-filter-' . $id, null);
		return Factory::getResource('store/mobile/model')->getSellingMobileModelTotal($id, $filter, $sort);
	}

	/**
	 * 获取店铺在售品牌
	 * @param 店铺ID $id
	 */
	public function loadSellingMobileBrandList($id)
	{
		return Factory::getResource('store/mobile/model')->loadSellingMobileBrandList($id);
	}

	/**
	 * 获取我的店铺销售型号
	 * @param 店铺ID $id
	 */
	public function loadMyMobileModelList($id)
	{
		if ($result = Factory::getResource('store/mobile/model')->loadMyMobileModelList($id)) {
			$models = array();
			foreach ($result as $item) {
				if (!isset($models[$item['brand_id']])) {
					$models[$item['brand_id']] = array(
						'label' => $item['brand'],
						'model' => array()
					);
				}
				if (!isset($models[$item['brand_id']]['model'][$item['store_model_id']])) {
					$models[$item['brand_id']]['model'][$item['store_model_id']] = array(
						'label' => $item['model'],
						'status' => $item['model_status'],
						'hot' => $item['hot'],
						'recommend' => $item['recommend'],
						'mobile' => array()
					);
				}
				$models[$item['brand_id']]['model'][$item['store_model_id']]['mobile'][] = array(
					'store_mobile_id' => $item['store_mobile_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price'],
					'status' => $item['mobile_status']
				);
			}
			return $models;
		}
		return false;
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getMyMobileModelTotal($id)
	{
		return Factory::getResource('store/mobile/model')->getMyMobileModelTotal($id);
	}

	/**
	 * 获取指定销售型号信息
	 * @param 型号ID $id
	 */
	public function getMobileModelInfo($id)
	{
		if ($result = Factory::getResource('store/mobile/model')->getMobileModelInfo($id)) {
			$model = array();
			foreach ($result as $item) {
				if (empty($model)) {
					$model = array(
						'store_model_id' => $item['store_model_id'],
						'label' => $item['brand'] . '&nbsp;' . $item['model'],
						'gallery' => array_reverse(explode(';', substr($item['gallery'], 0, -1))),//倒序并返回图片
						'remark' => htmlspecialchars_decode($item['mremark']),
						'mobile' => array()
					);
				}
				$model['mobile'][] = array(
					'store_mobile_id' => $item['store_mobile_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price']
				);
			}
			return $model;
		}
		return false;
	}

	/**
	 * 坚持手机型号出售信息是否存在
	 * @param 品牌ID $brand_id
	 * @param 型号ID $model_id
	 * @param 店铺ID $store_id
	 */
	public function checkMobileModelExist($brand_id, $model_id, $store_id)
	{
		return Factory::getResource('store/mobile/model')->checkMobileModelExist($brand_id, $model_id, $store_id);
	}

	/**
	 * 判断指定手机型号出售信息所属
	 * @param 型号ID $store_model_id
	 * @param 店铺ID $store_id
	 */
	public function isMyMobileModel($store_model_id, $store_id)
	{
		return Factory::getResource('store/mobile/model')->isMyMobileModel($store_model_id, $store_id);
	}

	/**
	 * 判断指定手机出售信息所属
	 * @param 手机出售信息ID $id
	 * @param 店铺ID $store_id
	 */
	public function isMyMobileItem($id, $store_id)
	{
		return Factory::getResource('store/mobile/item')->isMyMobileItem($id, $store_id);
	}

	/**
	 * 获取店铺出售手机型号信息
	 * @param 店铺手机型号ID $id
	 */
	public function getStoreMobileModelInfo($id)
	{
		$model = Factory::getResource('store/mobile/model')->getStoreMobileModelInfo($id);
		$mobiles = Factory::getResource('store/mobile/item')->loadStoreModelMobileList($id);
		if ($model && $mobiles) {
			$model['released_at'] = Factory::getView('system/mlib')->getReleasedDate($model['released_at']);
			$model['params'] = Factory::getView('mlib/item')->getModelData($id, $model['params']);
			$model['remark'] = htmlspecialchars_decode($model['remark']);
			$model['gallery'] = $model['sgallery'] ? explode(';', substr($model['sgallery'], 0, -1)) : explode(';', substr($model['ogallery'], 0, -1));
			return array('model' => $model, 'mobiles' => $mobiles);
		}
		return false;
	}

	/**
	 * 获取店铺销售报价单
	 * @param 店铺ID $id
	 */
	public function loadStoreMobileModelList($id)
	{
		if ($result = Factory::getResource('store/mobile/model')->loadStoreMobileModelList($id)) {
			$models = array();
			foreach ($result as $item) {
				if (!isset($models[$item['brand_id']])) {
					$models[$item['brand_id']] = array(
						'label' => $item['brand'],
						'model' => array()
					);
				}
				if (!isset($models[$item['brand_id']]['model'][$item['store_model_id']])) {
					$models[$item['brand_id']]['model'][$item['store_model_id']] = array(
						'label' => $item['model'],
						'mobile' => array()
					);
				}
				$models[$item['brand_id']]['model'][$item['store_model_id']]['mobile'][] = array(
					'store_mobile_id' => $item['store_mobile_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price']
				);
			}
			return $models;
		}
		return false;
	}

	/**
	 * 获取店铺手机型号对应资料库型号ID
	 * @param 店铺型号ID $id
	 */
	public function getMlibModelId($id)
	{
		return Factory::getResource('store/mobile/model')->getMlibModelId($id);
	}

	/**
	 * 获取热销列表
	 * @param 店铺ID $id
	 */
	public function loadMobileHotList($id)
	{
		if ($id) {
			return Factory::getResource('store/mobile/model')->loadMobileHotList($id);
		}
		return false;
	}

	/**
	 * 获取推荐列表
	 * @param 店铺ID $id
	 */
	public function loadMobileRecommendList($id)
	{
		if ($id) {
			return Factory::getResource('store/mobile/model')->loadMobileRecommendList($id);
		}
		return false;
	}
	
	/*******************************************店铺平板*******************************************/
	/**
	 * 店铺是否有在售平板信息
	 * @param 店铺ID $id
	 */
	public function hasSellingTablet($id)
	{
		return Factory::getResource('store/tablet/model')->hasSellingTablet($id);
	}

	/**
	 * 店铺列表页获取店铺在售型号
	 * @param 店铺ID $id
	 * @param 显示数量 $amount
	 */
	public function getSellingTabletModelList($id, $amount = 6)
	{
		return Factory::getResource('store/tablet/model')->getSellingTabletModelList($id, $amount);
	}

	/**
	 * 店铺平板页获取在售型号列表
	 * @param 店铺ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadSellingTabletModelList($id, $page = 1, $per = 9)
	{
		$sort = Factory::getSession('store-tsort-' . $id, null);
		$filter = Factory::getSession('store-tfilter-' . $id, null);
		return Factory::getResource('store/tablet/model')->loadSellingTabletModelList($id, $filter, $sort, $page, $per);
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getSellingTabletModelTotal($id)
	{
		$sort = Factory::getSession('store-tsort-' . $id, null);
		$filter = Factory::getSession('store-tfilter-' . $id, null);
		return Factory::getResource('store/tablet/model')->getSellingTabletModelTotal($id, $filter, $sort);
	}

	/**
	 * 获取店铺在售品牌
	 * @param 店铺ID $id
	 */
	public function loadSellingTabletBrandList($id)
	{
		return Factory::getResource('store/tablet/model')->loadSellingTabletBrandList($id);
	}

	/**
	 * 获取我的店铺销售型号
	 * @param 店铺ID $id
	 */
	public function loadMyTabletModelList($id)
	{
		if ($result = Factory::getResource('store/tablet/model')->loadMyTabletModelList($id)) {
			$models = array();
			foreach ($result as $item) {
				if (!isset($models[$item['brand_id']])) {
					$models[$item['brand_id']] = array(
						'label' => $item['brand'],
						'model' => array()
					);
				}
				if (!isset($models[$item['brand_id']]['model'][$item['store_model_id']])) {
					$models[$item['brand_id']]['model'][$item['store_model_id']] = array(
						'label' => $item['model'],
						'status' => $item['model_status'],
						'hot' => $item['hot'],
						'recommend' => $item['recommend'],
						'tablet' => array()
					);
				}
				$models[$item['brand_id']]['model'][$item['store_model_id']]['tablet'][] = array(
					'store_tablet_id' => $item['store_tablet_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price'],
					'status' => $item['tablet_status']
				);
			}
			return $models;
		}
		return false;
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getMyTabletModelTotal($id)
	{
		return Factory::getResource('store/tablet/model')->getMyTabletModelTotal($id);
	}

	/**
	 * 获取指定销售型号信息
	 * @param 型号ID $id
	 */
	public function getTabletModelInfo($id)
	{
		if ($result = Factory::getResource('store/tablet/model')->getTabletModelInfo($id)) {
			$model = array();
			foreach ($result as $item) {
				if (empty($model)) {
					$model = array(
						'store_model_id' => $item['store_model_id'],
						'label' => $item['brand'] . '&nbsp;' . $item['model'],
						'gallery' => array_reverse(explode(';', substr($item['gallery'], 0, -1))),//倒序并返回图片
						'remark' => htmlspecialchars_decode($item['mremark']),
						'tablet' => array()
					);
				}
				$model['tablet'][] = array(
					'store_tablet_id' => $item['store_tablet_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price']
				);
			}
			return $model;
		}
		return false;
	}

	/**
	 * 坚持平板型号出售信息是否存在
	 * @param 品牌ID $brand_id
	 * @param 型号ID $model_id
	 * @param 店铺ID $store_id
	 */
	public function checkTabletModelExist($brand_id, $model_id, $store_id)
	{
		return Factory::getResource('store/tablet/model')->checkTabletModelExist($brand_id, $model_id, $store_id);
	}

	/**
	 * 判断指定平板型号出售信息所属
	 * @param 型号ID $store_model_id
	 * @param 店铺ID $store_id
	 */
	public function isMyTabletModel($store_model_id, $store_id)
	{
		return Factory::getResource('store/tablet/model')->isMyTabletModel($store_model_id, $store_id);
	}

	/**
	 * 判断指定平板出售信息所属
	 * @param 手机出售信息ID $id
	 * @param 店铺ID $store_id
	 */
	public function isMyTabletItem($id, $store_id)
	{
		return Factory::getResource('store/tablet/item')->isMyTabletItem($id, $store_id);
	}

	/**
	 * 获取店铺出售平板型号信息
	 * @param 店铺平板型号ID $id
	 */
	public function getStoreTabletModelInfo($id)
	{
		$model = Factory::getResource('store/tablet/model')->getStoreTabletModelInfo($id);
		$tablets = Factory::getResource('store/tablet/item')->loadStoreModelTabletList($id);
		if ($model && $tablets) {
			$model['released_at'] = Factory::getView('system/mlib')->getReleasedDate($model['released_at']);
			$model['params'] = Factory::getView('mlib/item')->getModelData($id, $model['params']);
			$model['remark'] = htmlspecialchars_decode($model['remark']);
			$model['gallery'] = $model['sgallery'] ? explode(';', substr($model['sgallery'], 0, -1)) : explode(';', substr($model['ogallery'], 0, -1));
			return array('model' => $model, 'tablets' => $tablets);
		}
		return false;
	}

	/**
	 * 获取店铺销售报价单
	 * @param 店铺ID $id
	 */
	public function loadStoreTabletModelList($id)
	{
		if ($result = Factory::getResource('store/tablet/model')->loadStoreTabletModelList($id)) {
			$models = array();
			foreach ($result as $item) {
				if (!isset($models[$item['brand_id']])) {
					$models[$item['brand_id']] = array(
						'label' => $item['brand'],
						'model' => array()
					);
				}
				if (!isset($models[$item['brand_id']]['model'][$item['store_model_id']])) {
					$models[$item['brand_id']]['model'][$item['store_model_id']] = array(
						'label' => $item['model'],
						'mobile' => array()
					);
				}
				$models[$item['brand_id']]['model'][$item['store_model_id']]['tablet'][] = array(
					'store_tablet_id' => $item['store_tablet_id'],
					'version' => $item['version'],
					'color' => $item['color'],
					'storage' => $item['storage'],
					'isnew' => $item['isnew'],
					'newly' => $item['newly'],
					'remark' => $item['remark'],
					'price' => $item['price']
				);
			}
			return $models;
		}
		return false;
	}

	/**
	 * 获取店铺平板型号对应资料库型号ID
	 * @param 店铺型号ID $id
	 */
	public function getTlibModelId($id)
	{
		return Factory::getResource('store/tablet/model')->getTlibModelId($id);
	}

	/**
	 * 获取热销列表
	 * @param 店铺ID $id
	 */
	public function loadTabletHotList($id)
	{
		if ($id) {
			return Factory::getResource('store/tablet/model')->loadTabletHotList($id);
		}
		return false;
	}

	/**
	 * 获取推荐列表
	 * @param 店铺ID $id
	 */
	public function loadTabletRecommendList($id)
	{
		if ($id) {
			return Factory::getResource('store/tablet/model')->loadTabletRecommendList($id);
		}
		return false;
	}
}
