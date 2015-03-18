<?php
class Mobile_ListView extends Project_View_Block_Abstract
{
	private $_src = 'http://mobilefrom-upload.stor.sinaapp.com/mobile/';
	
	/**
	 * 获取我所发布手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadMyMobile($customer_id, $page, $per = 10)
	{
		return Factory::getResource('mobile')->loadMyMobile($customer_id, $page, $per);
	}
	
	/**
	 * 获取我所发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyMobileTotal($customer_id)
	{
		return Factory::getResource('mobile')->getMyMobileTotal($customer_id);
	}
	
	/**
	 * 获取我所发布过期手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadMyInactiveMobile($customer_id, $page, $per = 10)
	{
		return Factory::getResource('inactive/mobile')->loadMyInactiveMobile($customer_id, $page, $per);
	}
	
	/**
	 * 获取我所发布过期手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getMyInactiveMobileTotal($customer_id)
	{
		return Factory::getResource('inactive/mobile')->getMyInactiveMobileTotal($customer_id);
	}
	
	/**
	 * 获取用户发布手机列表
	 * @param 用户ID $customer_id
	 * @param 请求页码 $page
	 * @return false或手机列表
	 */
	public function loadCustomerMobile($customer_id, $page)
	{
		return Factory::getResource('mobile')->loadCustomerMobile($customer_id, $page);
	}
	
	/**
	 * 获取用户发布手机总量
	 * @param 用户ID $customer_id
	 * @return false或总量
	 */
	public function getCustomerMobileTotal($customer_id)
	{
		return Factory::getResource('mobile')->getCustomerMobileTotal($customer_id);
	}
	
	/**
	 * 获取用户其它发布手机信息
	 * @param 用户ID $customer_id
	 * @param 已查看手机ID $mobile_id
	 * @return false或手机列表
	 */
	public function loadCustomerOther($customer_id, $mobile_id)
	{
		return Factory::getResource('mobile')->loadCustomerOther($customer_id, $mobile_id);
	}
	
	/**
	 * 后台获取用户所有发布手机列表
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadAdminCustomerMobile($customer_id, $params, $page = 1, $per = 10)
	{
		return Factory::getResource('mobile')->loadAdminCustomerMobile($customer_id, $params, $page, $per);
	}

	/**
	 * 后台获取用户所有发布手机总量
	 * @param 用户ID $customer_id
	 * @param 筛选参数 $params
	 * @return false或总量
	 */
	public function getAdminCustomerMobileTotal($customer_id, $params)
	{
	 	return Factory::getResource('mobile')->getAdminCustomerMobileTotal($customer_id, $params);
	}

	/**
	 * 获取可见手机列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @param 展示模式 $mview
	 * @return false或手机列表
	 */
	public function loadVisibleMobile($page = 1, $per = 10)//, $mview
	{
		$cache = Factory::getLogic('cache');
		$mfilter = Factory::getSession('mfilter');
		$morder = Factory::getSession('morder');
		global $CStatic;
		/**
		switch ($mview) {
			case 'list':
				if (empty($mfilter) && $mobiles = $cache->getCache('mobile-list-' . $morder . '-' . $page)) {
					return $mobiles;
				} else {
					$mobiles = Factory::getResource('mobile')->loadVisibleMobileByList($page, $per);
					if ($mobiles) {
						foreach ($mobiles as &$mobile) {
							$mobile['price'] = $mobile['price'] ? '￥' . $mobile['price'] : '换机';
							$mobile['data'] = unserialize($mobile['data']);
							$mobile['contact'] = unserialize($mobile['contact']);
							$mobile['summary'] = $mobile['data']['remark'];
							$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
							unset($mobile['data']);
							unset($mobile['contact']);
						}
						if (empty($mfilter)) {
							$cache->createCache('mobile-list-' . $morder . '-' . $page, $mobiles);
						}
						return $mobiles;
					}
				}
				break;
			case 'grid':
		*/
				if (empty($mfilter) && $mobiles = $cache->getCache('mobile-grid-' . $morder . '-' . $page)) {
					return $mobiles;
				} else {
					$mobiles = Factory::getResource('mobile')->loadVisibleMobileByGrid($page, $per);
					if ($mobiles) {
						foreach ($mobiles as &$mobile) {
							$mobile['price'] = $mobile['price'] ? '￥' . $mobile['price'] : '换机';
							$mobile['contact'] = unserialize($mobile['contact']);
							$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
						}
						if (empty($mfilter)) {
							$cache->createCache('mobile-grid-' . $morder . '-' . $page, $mobiles);
						}
						return $mobiles;
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
	public function getVisibleMobileTotal()
	{
		return Factory::getResource('mobile')->getVisibleMobileTotal();
	}
	
	/**
	 * 获取最新手机列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或手机列表
	 */
	public function loadNewMobile($page = 1, $per = 8)
	{
		$cache = Factory::getLogic('cache');
		if ($mobiles = $cache->getCache('mobile-new')) {
			return $mobiles;
		} else {
			if ($mobiles = Factory::getResource('mobile')->loadNewMobile($page, $per)) {
				global $CStatic;
				foreach ($mobiles as &$mobile) {
					$mobile['contact'] = unserialize($mobile['contact']);
					$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
				}
				$cache->createCache('mobile-new', $mobiles);
				return $mobiles;
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
		if (isset($filter[3])) {//价格范围
			$result[3] = $MStatic['newly'][$filter[3]];
		}
		if (isset($filter[2])) {//手机品牌
			$brands = Factory::getView('system/mlib')->loadBrandList();
			$result[2] = $brands[$filter[2]];
		}
		if (isset($filter[8])) {//手机型号
			$brands = Factory::getView('system/mlib')->loadModelList();
			$result[8] = $brands[$filter[8]];
		}
		if (isset($filter[6])) {//交易地区
			global $CStatic;
			$result[6] = $CStatic['region'][$filter[6]];
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
	public function loadSearchMobile($q, $page = 1, $per = 20)//, $mview
	{
		global $CStatic;
		/**
		switch ($mview) {
			case 'list':
				$mobiles = Factory::getResource('mobile')->loadSearchMobileByList($q, $page, $per);
				if ($mobiles) {
					global $static;
					foreach ($mobiles as &$mobile) {
						$mobile['price'] = $mobile['price'] ? '￥' . $mobile['price'] : '换机';
						$mobile['data'] = unserialize($mobile['data']);
						$mobile['contact'] = unserialize($mobile['contact']);
						$mobile['summary'] = $mobile['data']['remark'];
						$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
						unset($mobile['data']);
						unset($mobile['contact']);
					}
				}
				return $mobiles;
				break;
			case 'grid':
		*/
				$mobiles = Factory::getResource('mobile')->loadSearchMobileByGrid($q, $page, $per);
				if ($mobiles) {
					foreach ($mobiles as &$mobile) {
						$mobile['price'] = $mobile['price'] ? '￥' . $mobile['price'] : '换机';
						$mobile['contact'] = unserialize($mobile['contact']);
						$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
					}
				}
				return $mobiles;
		/**
				break;
		}
		*/
	}
	
	/**
	 * 获取搜索手机总量
	 * @param 搜索内容 $q
	 * @return false或总量
	 */
	public function getSearchMobileTotal($q)
	{
		return Factory::getResource('mobile')->getSearchMobileTotal($q);
	}
	
	/**
	 * 根据提供的ID集合获取对应闲置手机列表
	 * @param ID集合数组 $ids
	 */
	public function loadMobileByIds($ids)
	{
		if ($ids) {
			$ids = implode(',', $ids);
			return Factory::getResource('mobile')->loadMobileByIds($ids);
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
	public function loadRecommendMobile($id, $model, $brand, $price)
	{
		$level = getLevel($price);
		return Factory::getResource('mobile')->loadRecommendMobile($id, $model, $brand, $level);
	}

	/**
	 * 加载有效筛选条件
	 */
	public function loadAvailableFilterOption()
	{
		$cache = Factory::getLogic('cache');
		if ($options = $cache->getCache('mobile-options')) {
			return $options;
		} else {
			$options = array();
			$result = Factory::getResource('relation/mobile/filter')->loadAvailableFilterOption();
			foreach ($result as $item) {
				$options[$item['filter_id']][$item['value_id']] = $item['value_id'];
			}
			$cache->createCache('mobile-options', $options);
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
		return Factory::getResource('relation/mobile/filter')->loadTopBrandList($num);
	}

	/**
	 * 总手机信息量
	 */
	public function getAllTotal()
	{
		return Factory::getResource('mobile')->getAllTotal();
	}

	/**
	 * 可见手机总量
	 */
	public function getAllVisibleTotal()
	{
		return Factory::getResource('mobile')->getAllVisibleTotal();
	}

	/**
	 * 获取同型号手机列表
	 * @param 手机型号ID $id
	 */
	public function loadModelMobileList($id)
	{
		$mobiles = Factory::getResource('mobile')->loadModelMobileList($id);
		if ($mobiles) {
			global $CStatic;
			foreach ($mobiles as &$mobile) {
				$mobile['price'] = $mobile['price'] ? '￥' . $mobile['price'] : '换机';
				$mobile['contact'] = unserialize($mobile['contact']);
				$mobile['region'] = $CStatic['region'][$mobile['contact']['region']];
			}
		}
		return $mobiles;
	}
}
