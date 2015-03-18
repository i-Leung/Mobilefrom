<?php
class Logic_Tablet
{
	/**
	 * 判断信息是否可用
	 * @param 平板信息ID $id
	 * @return boolean
	 */
	public function isActiveTablet($id)
	{
		return Factory::getResource('tablet')->isActiveTablet($id);
	}

	/**
	 * 发布平板信息
	 * @param 平板信息 $params
	 * @param 过滤器信息 $filter
	 */
	public function create($params, $filter)
	{
		$tablet = Factory::getResource('tablet');
		$tablet_id = null;
		if ($tablet->create($params)) {
			$tablet_id = $tablet->getNewId();
			$fparams = array();
			foreach ($filter as $key => $value) {
				$fparams['filter_id'][] = $key;
				$fparams['value_id'][] = $value;
				$fparams['tablet_id'][] = $tablet_id;
			}
			Factory::getResource('relation/tablet/filter')->create($fparams, 'multiple');
			Factory::getResource('relation/customer/tablet')->create(
				array(
					'tablet_id' => $tablet_id,
					'customer_id' => Factory::getSession('customer/id')
				)
			);
			$this->createOrder($tablet_id);
			return $tablet_id;
		}
		return false;
	}
	
	/**
	 * 修改平板信息
	 * @param 平板信息ID $id
	 * @param 平板信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modify($id, $params, $filter = null)
	{
		if (Factory::getResource('tablet')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('relation/tablet/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'tablet_id = ?' => $id,
							'filter_id = ?' => $key
						),
						array('value_id' => $value)
					);
				}
				$this->removeOrder($id);
				$this->createOrder($id);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 修改非可用平板信息并置为可用
	 * @param 平板信息ID $id
	 * @param 平板信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modifyInactive($id, $params, $filter = null)
	{
		if (Factory::getResource('inactive/tablet')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('inactive/tablet/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'tablet_id = ?' => $id,
							'filter_id = ?' => $key
						),
						array('value_id' => $value)
					);
				}
				$this->moveTabletInactiveToActive($id);
				$this->removeOrder($id);
				$this->createOrder($id);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 获取平板信息发布者ID
	 * @param 平板信息ID $id
	 * @return false或发布者ID
	 */
	public function getPublisher($id)
	{
		return Factory::getResource('relation/customer/tablet')->getPublisher($id);
	}
	
	/**
	 * 获取平板信息详细
	 * @param 平板信息ID $id
	 * @return false或平板信息详细
	 */
	public function getInfo($id)
	{
		$info = Factory::getResource('tablet')->getInfo($id);
		if ($info) {
			if ($info['gallery']) {
				$info['gallery'] = substr($info['gallery'], 0, -1);
				$info['gallery'] = explode(';', $info['gallery']);
			} else {
				$info['gallery'] = array();
			}
			$info['data'] = unserialize($info['data']);
			$info['contact'] = unserialize($info['contact']);
		}
		return $info;
	}
	
	/**
	 * 获取平板概述信息
	 * @param 平板信息ID $id
	 * @return false或平板概述详细
	 */
	public function getSummarize($id)
	{
		return Factory::getResource('tablet')->getSummarize($id);
	}
	
	/**
	 * 改变平板信息状态
	 * @param 平板信息ID $id
	 * @param 状态 $status
	 */
	public function changeStatus($id, $status)
	{
		switch ($status) {
			case '1':
				return $this->moveTabletInactiveToActive($id);
				break;
			case '2':
			case '3':
				return $this->moveTabletActiveToInactive($id, $status);
				break;
		}
	}

	/**
	 * 平板信息移到非可用库
	 * @param 平板信息ID $id
	 * @param 状态 $status
	 */
	public function moveTabletActiveToInactive($id, $status)
	{
		if ($this->isActiveTablet($id)) {
			//平板资料库更新
			if ($model = Factory::getView('tablet/item')->getTabletModelId($id)) {
				Factory::getLogic('system/mlib')->modifyModel($model, array('offers' => '`offers` - 1'));
			}
			//获取平板记录条
			$t_resource = Factory::getResource('tablet');
			$tablet = $t_resource->getActiveTabletRecord($id);
			$p_resource = Factory::getResource('relation/customer/tablet');
			$publisher = $p_resource->getActiveTabletPublisherRecord($id);
			$f_resource = Factory::getResource('relation/tablet/filter');
			$filters = $f_resource->getActiveTabletFilterRecord($id);
			//转移到非可用库
			$tablet['status'] = $status;
			Factory::getResource('inactive/tablet')->create($tablet);
			Factory::getResource('inactive/tablet/customer')->create($publisher);
			$in_filter = Factory::getResource('inactive/tablet/filter');
			foreach ($filters as $filter) {
				$in_filter->create($filter);	
			}
			//移除可用库数据
			$t_resource->remove(array('id = ?' => $id));
			$p_resource->remove(array('tablet_id = ?' => $id));
			$f_resource->remove(array('tablet_id = ?' => $id));
		} else {
			Factory::getResource('inactive/tablet')->modify(
				array('id = ?' => $id), 
				array('status' => $status)
			);
		}
	}

	/**
	 * 平板信息移到可用库
	 * @param 平板信息ID $id
	 * @param 状态 $status
	 */
	public function moveTabletInactiveToActive($id)
	{
		//获取非可用平板记录条
		$t_resource = Factory::getResource('inactive/tablet');
		$in_tablet = $t_resource->getInactiveTabletRecord($id);
		$p_resource = Factory::getResource('inactive/tablet/customer');
		$in_publisher = $p_resource->getInactiveTabletPublisherRecord($id);
		$f_resource = Factory::getResource('inactive/tablet/filter');
		$in_filters = $f_resource->getInactiveTabletFilterRecord($id);
		//转移到可用库
		$in_tablet['created_at'] = time();
		unset($in_tablet['status']);
		Factory::getResource('tablet')->create($in_tablet);
		Factory::getResource('relation/customer/tablet')->create($in_publisher);
		$filter = Factory::getResource('relation/tablet/filter');
		foreach ($in_filters as $in_filter) {
			$filter->create($in_filter);	
		}
		//移除非可用库数据
		$t_resource->remove(array('id = ?' => $id));
		$p_resource->remove(array('tablet_id = ?' => $id));
		$f_resource->remove(array('tablet_id = ?' => $id));
		//平板资料库更新
		if ($model = Factory::getView('tablet/item')->getTabletModelId($id)) {
			Factory::getLogic('system/mlib')->modifyModel($model, array('offers' => '`offers` + 1'));
		}
	}
	
	/**
	 * 创建排序记录
	 * @param 平板信息ID $id
	 * @return boolean
	 */
	public function createOrder($id)
	{
		return Factory::getResource('relation/tablet/order')->create(
				array('tablet_id' => $id)
		);
	}
	
	/**
	 * 移除排序记录
	 * @param 平板信息ID $id
	 * @return boolean
	 */
	public function removeOrder($id)
	{
		return Factory::getResource('relation/tablet/order')->remove(
				array('tablet_id = ?' => $id)
		);
	}
	
	/**
	 * ajax加载对比列表窗口
	 * @param 对比平板ID字符串 $compare
	 */
	public function loadCompareOutline($compare)
	{
		return Factory::getResource('tablet')->loadCompareOutline($compare);
	}
	
	/**
	 * ajax加载对比列表
	 * @param 对比平板ID字符串 $compare
	 */
	public function showCompare($compare)
	{
		return Factory::getResource('tablet')->showCompare($compare);
	}
	
	/**
	 * 记录平板点阅量
	 * @param 平板ID $id
	 */
	public function markClick($id)
	{
		return Factory::getResource('tablet')->modify(
			array('id = ?' => $id),
			array('clicks' => '`clicks` + 1')
		);
	}

	/**
	 * 获取信息发布者联系邮箱
	 * @param 信息ID $id
	 */
	public function getPublisherInfo($id)
	{
		$contact = Factory::getView('tablet/item')->showContact($id);
		if (is_numeric($contact['contact']['qq'])) {
			$contact['username'] = $contact['contact']['username'];
			$contact['email'] = $contact['contact']['qq'] . '@qq.com';
		}
		return $contact;
	}
}
