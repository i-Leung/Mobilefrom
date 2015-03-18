<?php
class Tablet_ListView extends Project_View_Block_Abstract
{
	private $_src = 'http://mobilefrom-upload.stor.sinaapp.com/tablet/';
	
	/**
	 * 获取我所发布手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadMyTablet($customer_id, $page, $per = 10)
	{
		return Factory::getResource('tablet')->loadMyTablet($customer_id, $page, $per);
	}
	
	/**
	 * 获取我所发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyTabletTotal($customer_id)
	{
		return Factory::getResource('tablet')->getMyTabletTotal($customer_id);
	}
	
	/**
	 * 获取我所发布过期手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadMyInactiveTablet($customer_id, $page, $per = 10)
	{
		return Factory::getResource('inactive/tablet')->loadMyInactiveTablet($customer_id, $page, $per);
	}
	
	/**
	 * 获取我所发布过期手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyInactiveTabletTotal($customer_id)
	{
		return Factory::getResource('inactive/tablet')->getMyInactiveTabletTotal($customer_id);
	}
	
	/**
	 * 获取用户发布手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadCustomerTablet($customer_id, $page)
	{
		return Factory::getResource('tablet')->loadCustomerTablet($customer_id, $page);
	}
	
	/**
	 * 获取用户发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getCustomerTabletTotal($customer_id)
	{
		return Factory::getResource('tablet')->getCustomerTabletTotal($customer_id);
	}
	
	/**
	 * 获取用户其它发布手机信息
	 * @param 用户ID $customer_id
	 * @param 已查看手机ID $tablet_id
	 * @return false或手机列表
	 */
	public function loadCustomerOther($customer_id, $tablet_id)
	{
		return Factory::getResource('tablet')->loadCustomerOther($customer_id, $tablet_id);
	}
	
	/**
	 * 后台获取用户所有发布手机列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadAdminCustomerTablet($customer_id, $params, $page = 1, $per = 10)
	{
		return Factory::getResource('tablet')->loadAdminCustomerTablet($customer_id, $params, $page, $per);
	}

	/**
	 * 后台获取用户所有发布手机总量
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @return false或总量
	 */
	public function getAdminCustomerTabletTotal($customer_id, $params)
	{
	 	return Factory::getResource('tablet')->getAdminCustomerTabletTotal($customer_id, $params);
	}

	/**
	 * 获取可见手机列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @param 展示模式 $mview
	 * @return false或手机列表
	 */
	public function loadVisibleTablet($page = 1, $per = 10)//, $mview
	{
		$cache = Factory::getLogic('cache');
		$tfilter = Factory::getSession('tfilter');
		$torder = Factory::getSession('torder');
		global $CStatic;
		if (empty($tfilter) && $tablets = $cache->getCache('tablet-grid-' . $torder . '-' . $page)) {
			return $tablets;
		} else {
			$tablets = Factory::getResource('tablet')->loadVisibleTabletByGrid($page, $per);
			if ($tablets) {
				foreach ($tablets as &$tablet) {
					$tablet['price'] = $tablet['price'] ? '￥' . $tablet['price'] : '换机';
					$tablet['contact'] = unserialize($tablet['contact']);
					$tablet['region'] = $CStatic['region'][$tablet['contact']['region']];
				}
				if (empty($tfilter)) {
					$cache->createCache('tablet-grid-' . $torder . '-' . $page, $tablets);
				}
				return $tablets;
			}
		}
		/**
			break;
		}
		*/
		return array();
	}
	
	/**
	 * 获取可见手机总量
	 * @return false或总量
	 */
	public function getVisibleTabletTotal()
	{
		return Factory::getResource('tablet')->getVisibleTabletTotal();
	}
	
	/**
	 * 获取最新手机列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadNewTablet($page = 1, $per = 8)
	{
		$cache = Factory::getLogic('cache');
		if ($tablets = $cache->getCache('tablet-new')) {
			return $tablets;
		} else {
			if ($tablets = Factory::getResource('tablet')->loadNewTablet($page, $per)) {
				global $CStatic;
				foreach ($tablets as &$tablet) {
					$tablet['contact'] = unserialize($tablet['contact']);
					$tablet['region'] = $CStatic['region'][$tablet['contact']['region']];
				}
				$cache->createCache('tablet-new', $tablets);
				return $tablets;
			}
		}
		return array();
	}
	
	/**
	 * 获取手机状态
	 * @param 状态ID $id
	 */
	public function getStatus($id)
	{
		$status = '';
		switch ($id) {
			case '1':
				$status = '有效';
				break;
			case '2':
				$status = '过期';
				break;
			case '3':
				$status = '冻结';
				break;
		}
		return $status;
	}
	
	/**
	 * 转换手机过滤条件
	 * @param 过滤器 $filter
	 * @return 过滤值字符串
	 */
	function translate($filter)
	{
		$result = array();
		global $MStatic;
		if (isset($filter[1])) {//价格范围
			$result[1] = $MStatic['level'][$filter[1]]['label'];
		}
		if (isset($filter[2])) {//手机品牌
			$brands = Factory::getView('system/tlib')->loadBrandList();
			$result[2] = $brands[$filter[2]];
		}
		if (isset($filter[3])) {//新旧程度
			$result[3] = $MStatic['newly'][$filter[3]];
		}
		if (isset($filter[4])) {//交易地区
			global $CStatic;
			$result[4] = $CStatic['region'][$filter[4]];
		}
		if (isset($filter[5])) {//手机型号
			$brands = Factory::getView('system/tlib')->loadModelList();
			$result[5] = $brands[$filter[5]];
		}
		if (empty($result)) {
			return array();
		}
		return $result;
	}
	
	/**
	 * 获取可见手机列表
	 * @param 搜索内容 $q
	 * @param 显示模式 $mview
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadSearchTablet($q, $page = 1, $per = 20)//, $mview
	{
		global $CStatic;
		$tablets = Factory::getResource('tablet')->loadSearchTabletByGrid($q, $page, $per);
		if ($tablets) {
			foreach ($tablets as &$tablet) {
				$tablet['price'] = $tablet['price'] ? '￥' . $tablet['price'] : '换机';
				$tablet['contact'] = unserialize($tablet['contact']);
				$tablet['region'] = $CStatic['region'][$tablet['contact']['region']];
			}
		}
		return $tablets;
	}
	
	/**
	 * 获取搜索手机总量
	 * @param 搜索内容 $q
	 * @return false或总量
	 */
	public function getSearchTabletTotal($q)
	{
		return Factory::getResource('tablet')->getSearchTabletTotal($q);
	}
	
	/**
	 * 根据提供的ID集合获取对应闲置手机列表
	 * @param ID集合数组 $ids
	 */
	public function loadTabletByIds($ids)
	{
		if ($ids) {
			$ids = implode(',', $ids);
			return Factory::getResource('tablet')->loadTabletByIds($ids);
		}
		return false;
	}
	
	/**
	 * 获取缩略图
	 * @param 图片路径 $pic
	 * @return string
	 */
	public function getThumbPicture($pic)
	{
		return $this->_src . 'thumb/' . $pic;
	}

	/**
	 * 加载推荐手机列表
	 * @param 当前手机ID $id
	 * @param 型号ID $model
	 * @param 品牌ID $brand
	 * @param 价格范围ID $level
	 */
	public function loadRecommendTablet($id, $model, $brand, $price)
	{
		$level = getLevel($price);
		return Factory::getResource('tablet')->loadRecommendTablet($id, $model, $brand, $level);
	}

	/**
	 * 加载有效筛选条件
	 */
	public function loadAvailableFilterOption()
	{
		$cache = Factory::getLogic('cache');
		if ($options = $cache->getCache('tablet-options')) {
			return $options;
		} else {
			$options = array();
			$result = Factory::getResource('relation/tablet/filter')->loadAvailableFilterOption();
			foreach ($result as $item) {
				$options[$item['filter_id']][$item['value_id']] = $item['value_id'];
			}
			$cache->createCache('tablet-options', $options);
			return $options;
		}
		return array();
	}

	/**
	 * 加载前五手机品牌
	 * @param 排名数量 $num
	 */
	public function loadTopBrandList($num = null)
	{
		return Factory::getResource('relation/tablet/filter')->loadTopBrandList($num);
	}

	/**
	 * 总手机信息量
	 */
	public function getAllTotal()
	{
		return Factory::getResource('tablet')->getAllTotal();
	}

	/**
	 * 可见手机总量
	 */
	public function getAllVisibleTotal()
	{
		return Factory::getResource('tablet')->getAllVisibleTotal();
	}

	/**
	 * 获取同型号手机列表
	 * @param 手机型号ID $id
	 */
	public function loadModelTabletList($id)
	{
		$tablets = Factory::getResource('tablet')->loadModelTabletList($id);
		if ($tablets) {
			global $CStatic;
			foreach ($tablets as &$tablet) {
				$tablet['price'] = $tablet['price'] ? '￥' . $tablet['price'] : '换机';
				$tablet['contact'] = unserialize($tablet['contact']);
				$tablet['region'] = $CStatic['region'][$tablet['contact']['region']];
			}
		}
		return $tablets;
	}
}
