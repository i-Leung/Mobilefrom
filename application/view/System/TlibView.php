<?php
class System_TlibView extends Project_View_Block_Abstract
{
	/*********************************************属性操作*********************************************/
	/**
	 * 获取属性列表
	 */
	public function loadAttributeList()
	{
		return Factory::getResource('system/tlib/attribute')->loadAttributeList();
	}

	/**
	 * 获取当前属性标签
	 * @param 属性ID $id
	 */
	public function getAttributeLabel($id)
	{
		if ($id) {
			return Factory::getResource('system/tlib/attribute')->getAttributeLabel($id);
		}
		return false;
	}

	/**
	 * 获取属性值类型
	 * @param 类型代号 $type
	 */
	public function getAttributeValueType($type = null)
	{
		$types = array(
			'text' => '文本输入',
			'single' => '单项选择',
			'multiple' => '多项选择'
		);
		return is_null($type) ? $types : $types[$type];
	}

	/*********************************************属性值操作*********************************************/
	/**
	 * 获取当前属性下所有属性值
	 * @param 属性ID $id
	 */
	public function loadAttributeValue($id = null)
	{
		if ($result = Factory::getResource('system/tlib/attribute/value')->loadAttributeValue($id)) {
			if ($id) {
				return $result;
			} else {
				$values = array();
				foreach ($result as $value) {
					$values[$value['attribute_id']][$value['value_id']] = $value['label'];
				}
				return $values;
			}
		}
		return false;
	}

	/*********************************************手机操作*********************************************/
	/**
	 * 获取手机发布参数列表
	 */
	public function loadMobileDataInputList()
	{
		$data = array();
		$data[0] = array(
			'group' => '其它',
			'attributes' => array()
		);
		$result = Factory::getResource('system/tlib/item')->loadMobileDataInputList();
		foreach ($result as $item) {
			if ($item['group_id'] == null) {
				$group = &$data[0];
			} elseif (!isset($data[$item['group_id']])) {
				$data[$item['group_id']] = array(
					'group' => $item['group'],
					'attributes' => array()
				);
				$group = &$data[$item['group_id']];
			} else {
				$group = &$data[$item['group_id']];
			}
			if (!isset($group['attributes'][$item['attribute_id']])) {
				$group['attributes'][$item['attribute_id']] = array(
					'attribute' => $item['attribute'],
					'backend' => $item['backend'],
					'is_required' => $item['is_required'],
					'values' => $item['backend'] == 'text' ? null : array()
				);
				$attribute = &$group['attributes'][$item['attribute_id']];
			} else {
				$attribute = &$group['attributes'][$item['attribute_id']];
			}
			if ($item['value_id']) {
				$attribute['values'][$item['value_id']] = $item['value'];
			}
		}
		return $data;
	}

	/**
	 * 获取手机品牌列表
	 */
	public function loadBrandList()
	{
		$cache = Factory::getLogic('cache');
		$brands = array();
		if ($brands = $cache->getCache('tbrands')) {
			return $brands;
		} else {
			if ($result = Factory::getResource('system/tlib/brand')->loadBrandList()) {
				foreach ($result as $item) {
					$brands[$item['id']] = $item['label'];
				}
				$cache->createCache('tbrands', $brands);
			}
			return $brands;
		}
	}

	/**
	 * 获取后台手机品牌列表
	 */
	public function loadAdminBrandList()
	{
		return Factory::getResource('system/tlib/brand')->loadAdminBrandList();
	}

	/**
	 * 获取指定手机品牌标签
	 * @param 品牌ID $id
	 */
	public function getBrandLabel($id)
	{
		return Factory::getResource('system/tlib/brand')->getBrandLabel($id);
	}

	/**
	 * 获取手机品牌型号列表
	 * @param 品牌ID $bid
	 */
	public function loadModelListById($bid)
	{
		$cache = Factory::getLogic('cache');
		$models = array();
		if ($models = $cache->getCache('tmodels-' . $bid)) {
			return $models;
		} else {
			if ($result = Factory::getResource('system/tlib/model')->loadModelListById($bid)) {
				foreach ($result as $item) {
					$models[$item['id']] = $item['label'];
				}
				$cache->createCache('tmodels-' . $bid, $models);
			}
			return $models;
		}
	}

	/**
	 * 获取后台手机品牌型号列表
	 * @param 品牌ID $bid
	 * @param 排序方式 $sort
	 */
	public function loadAdminModelList($bid, $sort = 'id')
	{
		return Factory::getResource('system/tlib/model')->loadAdminModelList($bid, $sort);
	}

	/**
	 * 后台获取手机型号信息
	 * @param 型号ID $id
	 */
	public function getAdminModel($id)
	{
		if ($result = Factory::getResource('system/tlib/model')->getAdminModel($id)) {
			$result['params'] = unserialize($result['params']);
		}
		return $result;
	}

	/**
	 * 获取上市时间
	 * @param 值ID $id
	 */
	public function getReleasedDate($id = null)
	{
		$labels = array(
			'1' => '2010年前',
			'2' => '2010年',
			'3' => '2011年',
			'4' => '2012年',
			'5' => '2013年',
			'6' => '2014年'
		);
		return is_null($id) ? $labels : $labels[$id];
	}
	
	/**
	 * 获取推荐图片上传url
	 */
	public function getMajorImgUrl()
	{
		return Factory::getUrl('plugin/swfupload/tlib-major-img');
	}
}
