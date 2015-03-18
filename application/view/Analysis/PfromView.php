<?php
class Analysis_PfromView extends Project_View_Block_Abstract
{
	/**
	 * 页面模块
	 */
	public $module = array(
		'default' => '网站相关',
		'customer' => '用户相关',
		'space' => '用户空间相关',
		'mobile' => '手机相关',
		'purchase' => '求购相关'
	);

	/**
	 * 页面来源中文标签
	 */
	public $cfrom = array(
		'breadcrumb' => '面包屑导航',
		'register' => '注册页面',
		'favor' => '用户中心关注页面',
		'uc_tab' => '用户中心撤换栏',
		'uc_paginator' => '用户中心翻页',
		'uc_consult' => '用户中心咨询页面',
		'publish' => '发布页面',
		'uc_mypublish' => '用户中心我的发布',
		'index_new' => '首页最新信息',
		'index_region' => '首页区域筛选',
		'index_brand' => '首页品牌筛选',
		'add_compare' => '添加对比项',
		'namecard' => '用户名片',
		'item_other' => 'TA还发布了',
		'list' => '列表页面',
		'manual_paginator' => '点击翻页',
		'sort_time' => '时间排序',
		'sort_price' => '价格排序',
		'sidebox' => '侧部隐藏栏',
		'attr_selector' => '属性选中列表',
		'brand' => '品牌筛选',
		'os' => '系统筛选',
		'source' => '版本筛选',
		'newly' => '新旧程度筛选',
		'price' => '价格筛选',
		'region' => '区域筛选',
		'who' => '卖家性质',
		'item_consult' => '详细页面咨询功能',
		'item' => '详细页面',
		'outside' => '外部请求',
		'nav' => '主导航',
		'history' => '浏览历史',
		'search' => '搜索结果',
		'topheader' => '顶部栏',
		'slide_header' => '顶部滑动栏',
		'pop_window' => '登陆弹窗'
	);

	/**
	 * 整站页面导向统计
	 * @param 统计范围 $range
	 */
	public function sitePageFrom($range)	
	{
		if ($range) {
			$range = $this->_pageFromRange($range);
			$records = Factory::getResource('analysis/pfrom')->sitePageFrom($range['where'], $range['params']);
			$result = array('total' => 0);
			foreach ($records as $record) {
				if ($result[$record['module']]) {
					if ($record['from'] == 'outside') {
						$result[$record['module']]['outside'] += $record['amount'];
					} else {
						$result[$record['module']]['inside'] += $record['amount'];
					}
				} else {
					$result[$record['module']] = array(
						'outside' => 0,
						'inside' => 0,
						'subtotal' => 0
					);
					if ($record['from'] == 'outside') {
						$result[$record['module']]['outside'] += $record['amount'];
					} else {
						$result[$record['module']]['inside'] += $record['amount'];
					}
				}
				$result[$record['module']]['subtotal'] += $record['amount'];
				$result['total'] += $record['amount'];
			}
			return $result;
		}
		return false;
	}

	/**
	 * 模块页面导向统计
	 * @param 模块名称 $module
	 * @param 统计范围 $range
	 */
	public function modulePageFrom($module, $range)	
	{
		if ($module && $range) {
			$range = $this->_pageFromRange($range);
			$range['where'][] = 'module = :module';
			$range['params'][':module'] = $module;
			$records = Factory::getResource('analysis/pfrom')->modulePageFrom($range['where'], $range['params']);
			$result = array('total' => 0);
			foreach ($records as $record) {
				$url = $record['controller'] . '_' . $record['action'];
				if ($result[$url]) {
					if ($record['from'] == 'outside') {
						$result[$url]['outside'] += $record['amount'];
					} else {
						$result[$url]['inside'] += $record['amount'];
					}
				} else {
					$result[$url] = array(
						'outside' => 0,
						'inside' => 0,
						'subtotal' => 0
					);
					if ($record['from'] == 'outside') {
						$result[$url]['outside'] += $record['amount'];
					} else {
						$result[$url]['inside'] += $record['amount'];
					}
				}
				$result[$url]['subtotal'] += $record['amount'];
				$result['total'] += $record['amount'];
			}
			return $result;
		}
		return false;
	}

	/**
	 * 动作页面导向统计
	 * @param 动作名称 $action
	 * @param 统计范围 $range
	 */
	public function actionPageFrom($action, $range)	
	{
		if ($action && $range) {
			$range = $this->_pageFromRange($range);
			$action = explode('_', $action);
			$range['where'][] = 'module = :module';
			$range['where'][] = 'controller = :controller';
			$range['where'][] = 'action = :action';
			$range['params'][':module'] = $action[0];
			$range['params'][':controller'] = $action[1];
			$range['params'][':action'] = $action[2];
			$records = Factory::getResource('analysis/pfrom')->actionPageFrom($range['where'], $range['params']);
			$total = 0;
			foreach ($records as $record) {
				$total += $record['amount'];
			}
			return array('total' => $total, 'records' => $records);
		}
		return false;
	}

	/**
	 * 页面导向统计时间范围计算
	 * @param 统计范围 $range
	 */
	private function _pageFromRange($range)
	{
		$now = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$day = 24 * 60 * 60;
		$where = array();
		$params = array();
		switch ($range) {
			case '1':
				$where[] = '`date` = :now';
				$params[':now'] = $now;
				break;
			case '2':
				$where[] = '`date` = :yesterday';
				$params[':yesterday'] = $now - $day;
				break;
			case '3':
				$where[] = '`date` >= :from';
				$where[] = '`date` <= :to';
				$params[':from'] = $now - 3 * $day;
				$params[':to'] = $now;
				break;
			case '4':
				$where[] = '`date` >= :from';
				$where[] = '`date` <= :to';
				$params[':from'] = $now - 7 * $day;
				$params[':to'] = $now;
				break;
			case '5':
				break;
		}
		return array('where' => $where, 'params' => $params);
	}
}
