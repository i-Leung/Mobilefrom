<?php
class Customer_FavorView extends Project_View_Block_Abstract
{
	/**
	 * 用户关注列表
	 * @param 信息类型 $type
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadFavorType($type, $page = 1, $per = 10)
	{
		$customer_id = Factory::getSession('customer/id');
		return Factory::getResource('relation/customer/favor')->loadFavorType($customer_id, $type, $page, $per);
	}
	
	/**
	 * 用户关注总数
	 * @param 信息类型 $type
	 */
	public function getFavorTypeTotal($type)
	{
		$customer_id = Factory::getSession('customer/id');
		return Factory::getResource('relation/customer/favor')->getFavorTypeTotal($customer_id, $type);
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
	 * 是否已收藏
	 * @param 用户ID $customer_id
	 * @param 类型ID $type
	 * @param 主题ID $id
	 */
	public function hasFavor($customer_id, $type, $id)
	{
		return Factory::getResource('relation/customer/favor')->hasFavor($customer_id, $type, $id);
	}
}
