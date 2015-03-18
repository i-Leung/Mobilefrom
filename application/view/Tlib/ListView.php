<?php
class Tlib_ListView extends Project_View_Block_Abstract
{
	/**
	 * 获取过滤条件
	 * @param 条件名 $name
	 */
	public function loadFilterOptionList($name = null)
	{
		$result = array(
			'brand' => array(
				'01' => 'Apple/苹果',
				'02' => 'SAMSUNG/三星',
				'03' => 'Microsoft/微软',
				'04' => 'Lenovo/联想',
				'05' => 'Google/谷歌',
				'06' => 'Motorola/摩托罗拉'
			),
			'price' => array(
				'1' => array(
					'min' => '0',
					'max' => '1000',
					'label' => '1000以下'
				),
				'2' => array(
					'min' => '1000',
					'max' => '1500',
					'label' => '1000-1500'
				),
				'3' => array(
					'min' => '1500',
					'max' => '2000',
					'label' => '1500-2000'
				),
				'4' => array(
					'min' => '2000',
					'max' => '2500',
					'label' => '2000-2500'
				),
				'5' => array(
					'min' => '2500',
					'max' => '3000',
					'label' => '2500-3000'
				),
				'6' => array(
					'min' => '3000',
					'max' => '3500',
					'label' => '3000-3500'
				),
				'7' => array(
					'min' => '3500',
					'max' => '4000',
					'label' => '3500-4000'
				),
				'8' => array(
					'min' => '4000',
					'max' => '99999',
					'label' => '4000以上'
				),
			),
			'os' => array(
				'2' => 'iOS',
				'1' => 'Android',
				'3' => 'Windows'
			)
		);
		return $name ? $result[$name] : $result;
	}
	/**
	 * 获取缩略图
	 * @param 路径 $img
	 */
	public function getThumbImg($img)
	{
		return $img ? 'http://mobilefrom-upload.stor.sinaapp.com/tlib/thumb/' . $img : '';
	}
	
	/**
	 * 加载型号列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadModelList($page = 1, $per = 16)
	{
		/**
		$available = Factory::getSession('tlib-available');
		*/
		$filter = Factory::getSession('tlib-filter');
		$order = Factory::getSession('tlib-order');
		if (isset($filter['price'])) {
			$price = $this->loadFilterOptionList('price');
			$filter['price'] = $price[$filter['price']];
		}
		$models = Factory::getResource('system/tlib/model')->loadModelList($filter, $order, $page, $per);//, $available
		return $models;
	}
	
	/**
	 * 热门型号列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadHotModelList($page = 1, $per = 6)
	{
		return Factory::getResource('system/tlib/model')->loadModelList(null, 'sales', $page, $per);
	}
	
	/**
	 * 加载更多型号列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMoreModelList($page = 1, $per = 16)
	{
		$models = $this->loadModelList($page);
		if ($models) {
			$ids = array();
			foreach ($models as $model) {
				$ids[] = $model['id'];
			}
			$quotes = $this->loadModelQuote($ids);
			foreach ($models as &$model) {
				$model['news'] = $quotes[$model['id']]['news'];
				$model['used'] = $quotes[$model['id']]['used'];
				$model['thumb'] = $this->getThumbImg($model['thumb']);
			}
			return $models;
		}
		return false;
	}

	/**
	 * 获取平板型号总量
	 */
	public function getModelTotal()
	{
		$filter = Factory::getSession('tlib-filter');
		return Factory::getResource('system/tlib/model')->getModelTotal($filter);
	}

	/**
	 * 获取指定平板型号报价信息
	 * @param 型号ID $data
	 */
	public function loadModelQuote($data)
	{
		$data = '"' . implode('", "', $data) . '"';
		$resource = Factory::getResource('system/tlib/model');
		$new = $resource->loadModelQuote($data, '1');
		$used = $resource->loadModelQuote($data, '0');
		$result = array();
		foreach ($new as $value) {
			$result[$value['id']]['news'] = $value['price'];
		}
		foreach ($used as $value) {
			$result[$value['id']]['used'] = $value['price'];
		}
		return $result;
	}

	/**
	 * 获取推荐平板列表型号
	 * @param 主题型号ID $id
	 * @param 型号报价 $quote
	 * @param 展示数量 $amount
	 */
	public function loadRecommendModelList($id, $quote, $amount = 3)
	{
		if ($quote['new'] && $quote['used']) {
			$price = $quote['new'] >= $quote['used'] ? $quote['used'] : $quote['new'];
		} elseif ($quote['new'] || $quote['used']) {
			$price = $quote['new'] ? $quote['new'] : $quote['used'];
		} else {
			$price = 0;
		}
		/**
		if ($price) {
			$price = ceil($price / 500);
			return Factory::getResource('system/tlib/model')->loadRecommendModelPriceList($id, ($price - 1) * 500, $price * 500, $amount);
		} else {
		}
		**/
			return Factory::getResource('system/tlib/model')->loadRecommendModelRandList($id, $amount);
	}
	
	/**
	 * 加载型号搜索列表
	 * @param 搜索内容 $q
	 */
	public function loadModelSearchList($q)
	{
		return Factory::getResource('system/tlib/model')->loadOutsideRecords($q, null);
	}
}
