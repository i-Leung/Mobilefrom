<?php
class Tlib_ItemView extends Project_View_Block_Abstract
{
	/**
	 * 获取缩略图
	 * @param 路径 $img
	 */
	public function getThumbImg($img)
	{
		return $img ? 'http://mobilefrom-upload.stor.sinaapp.com/tlib/thumb/' . $img : '';
	}

	/**
	 * 获取平板型号概述
	 * @param 型号ID $id
	 */
	public function getModelSummary($id)
	{
		if ($id) {
			$model = Factory::getResource('system/tlib/model')->getModelSummary($id);
			if ($model) {
				$attributes = Factory::getView('system/tlib')->loadAttributeValue();
				$model['params'] = unserialize($model['params']);
				$model['params']['os'] = $attributes['5'][$model['params']['os']];
				$model['params']['core_number'] = array('index' => $model['params']['core_number'], 'label' => $attributes['8'][$model['params']['core_number']]);
				$model['params']['screen_resolution'] = array('index' => $model['params']['screen_resolution'], 'label' => $attributes['1'][$model['params']['screen_resolution']]);
				$model['params']['screen_size'] = array('index' => $model['params']['screen_size'], 'label' => $attributes['2'][$model['params']['screen_size']]);
				$model['params']['front_camera'] = array('index' => $model['params']['front_camera'], 'label' => $attributes['3'][$model['params']['front_camera']]);
				$model['params']['back_camera'] = array('index' => $model['params']['back_camera'], 'label' => $attributes['4'][$model['params']['back_camera']]);
				$model['params']['ram'] = array('index' => $model['params']['ram'], 'label' => $attributes['9'][$model['params']['ram']]);
				$model['params']['expansion'] = $attributes['10'][$model['params']['expansion']];
				$model['released_at'] = Factory::getView('system/tlib')->getReleasedDate($model['released_at']);
				return $model;
			}
		}
		return false;
	}

	/**
	 * 获取平板相关图片集
	 * @param 型号ID $id
	 */
	public function getModelGallery($id)
	{
		if ($id && $result = Factory::getResource('system/tlib/model')->getModelGallery($id)) {
			$result = explode(';', substr($result, 0, -1));
			return $result;
		}
		return false;
	}

	/**
	 * 获取平板重要参数
	 * @param 型号ID $id
	 * @param 型号参数
	 */
	public function getModelData($id, $data = null)
	{
		if ($id) {
			$data = $data ? $data : Factory::getResource('system/tlib/model')->getModelData($id);
			if ($data) {
				$data = unserialize($data);
				$attributes = Factory::getView('system/tlib')->loadAttributeValue();
				$data['os'] = $attributes['5'][$data['os']];
				$data['core_number'] = array('index' => $data['core_number'], 'label' => $attributes['8'][$data['core_number']]);
				$data['screen_resolution'] = array('index' => $data['screen_resolution'], 'label' => $attributes['1'][$data['screen_resolution']]);
				$data['screen_size'] = array('index' => $data['screen_size'], 'label' => $attributes['2'][$data['screen_size']]);
				$data['front_camera'] = array('index' => $data['front_camera'], 'label' => $attributes['3'][$data['front_camera']]);
				$data['back_camera'] = array('index' => $data['back_camera'], 'label' => $attributes['4'][$data['back_camera']]);
				$data['ram'] = array('index' => $data['ram'], 'label' => $attributes['9'][$data['ram']]);
				return $data;
			}
		}
		return false;
	}

	/**
	 * 获取平板型号参数占比
	 * @param 参数 $data
	 */
	public function getModelDataPercent($data)
	{
		if ($result = Factory::getResource('system/tlib/attribute/index')->getModelDataPercent($data)) {
			$tmp = array();
			foreach ($result as $value) {
				$tmp[$value['attribute_id']][] = $value['total'];
			}
			$percent = array();
			foreach ($tmp as $key => $value) {
				if (isset($value[1])) {
					$percent[$key] = ceil(($value[0] / $value[1]) * 100);
				}
			}
			return $percent;
		}
	}

	/**
	 * 获取指定平板型号报价信息
	 * @param 型号ID $id
	 */
	public function getModelQuote($id)
	{
		$resource = Factory::getResource('system/tlib/model');
		$new = $resource->getModelQuote($id, '1');
		$used = $resource->getModelQuote($id, '0');
		return array('new' => $new, 'used' => $used);
	}

	/**
	 * 获取指定平板型号店铺销售信息
	 * @param 型号ID $model_id
	 */
	public function loadModelStoreSales($model_id)
	{
		$stores = array();
		$addrs = array();
		if ($model_id) {
			if ($new = Factory::getResource('store/tablet/model')->loadModelStoreNewSales($model_id)) {
				foreach ($new as $item) {
					if (!$stores[$item['store_id']]) {
						$stores[$item['store_id']] = array(
							'customer_id' => $item['customer_id'],
							'name' => $item['name'],
							'store_model_id' => $item['store_model_id'],
							'children' => array()
						);
					}
					$stores[$item['store_id']]['children'][1] = array(
						'version' => $item['version'],
						'price' => $item['price'],
						'color' => $item['color'],
						'storage' => $item['storage'],
						'newly' => $item['newly']
					);
				}
			}
			if ($used = Factory::getResource('store/tablet/model')->loadModelStoreUsedSales($model_id)) {
				foreach ($used as $item) {
					if (!$stores[$item['store_id']]) {
						$stores[$item['store_id']] = array(
							'customer_id' => $item['customer_id'],
							'name' => $item['name'],
							'store_model_id' => $item['store_model_id'],
							'children' => array()
						);
					}
					$stores[$item['store_id']]['children'][0] = array(
						'version' => $item['version'],
						'price' => $item['price'],
						'color' => $item['color'],
						'storage' => $item['storage'],
						'newly' => $item['newly']
					);
				}
			}
			if ($ids = array_keys($stores)) {
				if ($arecords = Factory::getResource('store/addr')->loadStoreAddrList($ids)) {
					global $CStatic;
					foreach ($arecords as $item) {
						$addrs[$item['store_id']][] = array(
							'region' => $CStatic['region'][$item['region_id']],
							'addr' => $item['addr']
						);
					}
				}
			}
		}
		return $stores && $addrs ? array('store' => $stores, 'addr' => $addrs) : false;
	}

	/**
	 * 获取平板型号名称
	 * @param 平板型号ID $id
	 */
	public function getModelName($id)
	{
		if ($id) {
			return Factory::getResource('system/tlib/model')->getModelName($id);
		}
	}
}
