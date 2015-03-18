<?php
class Logic_Mobile
{
	/**
	 * 判断信息是否可用
	 * @param 手机信息ID $id
	 * @return boolean
	 */
	public function isActiveMobile($id)
	{
		return Factory::getResource('mobile')->isActiveMobile($id);
	}

	/**
	 * 发布手机信息
	 * @param 手机信息 $params
	 * @param 过滤器信息 $filter
	 */
	public function create($params, $filter)
	{
		$mobile = Factory::getResource('mobile');
		$mobile_id = null;
		if ($mobile->create($params)) {
			$mobile_id = $mobile->getNewId();
			$fparams = array();
			foreach ($filter as $key => $value) {
				$fparams['filter_id'][] = $key;
				$fparams['value_id'][] = $value;
				$fparams['mobile_id'][] = $mobile_id;
			}
			Factory::getResource('relation/mobile/filter')->create($fparams, 'multiple');
			Factory::getResource('relation/customer/mobile')->create(
				array(
					'mobile_id' => $mobile_id,
					'customer_id' => Factory::getSession('customer/id')
				)
			);
			$this->createOrder($mobile_id);
			return $mobile_id;
		}
		return false;
	}
	
	/**
	 * 修改手机信息
	 * @param 手机信息ID $id
	 * @param 手机信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modify($id, $params, $filter = null)
	{
		if (Factory::getResource('mobile')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('relation/mobile/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'mobile_id = ?' => $id,
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
	 * 修改非可用手机信息并置为可用
	 * @param 手机信息ID $id
	 * @param 手机信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modifyInactive($id, $params, $filter = null)
	{
		if (Factory::getResource('inactive/mobile')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('inactive/mobile/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'mobile_id = ?' => $id,
							'filter_id = ?' => $key
						),
						array('value_id' => $value)
					);
				}
				$this->moveMobileInactiveToActive($id);
				$this->removeOrder($id);
				$this->createOrder($id);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 获取手机信息发布者ID
	 * @param 手机信息ID $id
	 * @return false或发布者ID
	 */
	public function getPublisher($id)
	{
		return Factory::getResource('relation/customer/mobile')->getPublisher($id);
	}
	
	/**
	 * 获取手机信息详细
	 * @param 手机信息ID $id
	 * @return false或手机信息详细
	 */
	public function getInfo($id)
	{
		$info = Factory::getResource('mobile')->getInfo($id);
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
	 * 获取手机概述信息
	 * @param 手机信息ID $id
	 * @return false或手机概述详细
	 */
	public function getSummarize($id)
	{
		return Factory::getResource('mobile')->getSummarize($id);
	}
	
	/**
	 * 改变手机信息状态
	 * @param 手机信息ID $id
	 * @param 状态 $status
	 */
	public function changeStatus($id, $status)
	{
		switch ($status) {
			case '1':
				return $this->moveMobileInactiveToActive($id);
				break;
			case '2':
			case '3':
				return $this->moveMobileActiveToInactive($id, $status);
				break;
		}
	}

	/**
	 * 手机信息移到非可用库
	 * @param 手机信息ID $id
	 * @param 状态 $status
	 */
	public function moveMobileActiveToInactive($id, $status)
	{
		if ($this->isActiveMobile($id)) {
			//手机资料库更新
			if ($model = Factory::getView('mobile/item')->getMobileModelId($id)) {
				Factory::getLogic('system/mlib')->modifyModel($model, array('offers' => '`offers` - 1'));
			}
			//获取手机记录条
			$m_resource = Factory::getResource('mobile');
			$mobile = $m_resource->getActiveMobileRecord($id);
			$p_resource = Factory::getResource('relation/customer/mobile');
			$publisher = $p_resource->getActiveMobilePublisherRecord($id);
			$f_resource = Factory::getResource('relation/mobile/filter');
			$filters = $f_resource->getActiveMobileFilterRecord($id);
			//转移到非可用库
			$mobile['status'] = $status;
			Factory::getResource('inactive/mobile')->create($mobile);
			Factory::getResource('inactive/mobile/customer')->create($publisher);
			$in_filter = Factory::getResource('inactive/mobile/filter');
			foreach ($filters as $filter) {
				$in_filter->create($filter);	
			}
			//移除可用库数据
			$m_resource->remove(array('id = ?' => $id));
			$p_resource->remove(array('mobile_id = ?' => $id));
			$f_resource->remove(array('mobile_id = ?' => $id));
		} else {
			Factory::getResource('inactive/mobile')->modify(
				array('id = ?' => $id), 
				array('status' => $status)
			);
		}
	}

	/**
	 * 手机信息移到可用库
	 * @param 手机信息ID $id
	 * @param 状态 $status
	 */
	public function moveMobileInactiveToActive($id)
	{
		//获取非可用手机记录条
		$m_resource = Factory::getResource('inactive/mobile');
		$in_mobile = $m_resource->getInactiveMobileRecord($id);
		$p_resource = Factory::getResource('inactive/mobile/customer');
		$in_publisher = $p_resource->getInactiveMobilePublisherRecord($id);
		$f_resource = Factory::getResource('inactive/mobile/filter');
		$in_filters = $f_resource->getInactiveMobileFilterRecord($id);
		//转移到可用库
		$in_mobile['created_at'] = time();
		unset($in_mobile['status']);
		Factory::getResource('mobile')->create($in_mobile);
		Factory::getResource('relation/customer/mobile')->create($in_publisher);
		$filter = Factory::getResource('relation/mobile/filter');
		foreach ($in_filters as $in_filter) {
			$filter->create($in_filter);	
		}
		//移除非可用库数据
		$m_resource->remove(array('id = ?' => $id));
		$p_resource->remove(array('mobile_id = ?' => $id));
		$f_resource->remove(array('mobile_id = ?' => $id));
		//手机资料库更新
		if ($model = Factory::getView('mobile/item')->getMobileModelId($id)) {
			Factory::getLogic('system/mlib')->modifyModel($model, array('offers' => '`offers` + 1'));
		}
	}
	
	/**
	 * 创建排序记录
	 * @param 手机信息ID $id
	 * @return boolean
	 */
	public function createOrder($id)
	{
		return Factory::getResource('relation/mobile/order')->create(
				array('mobile_id' => $id)
		);
	}
	
	/**
	 * 移除排序记录
	 * @param 手机信息ID $id
	 * @return boolean
	 */
	public function removeOrder($id)
	{
		return Factory::getResource('relation/mobile/order')->remove(
				array('mobile_id = ?' => $id)
		);
	}
	
	/**
	 * ajax加载对比列表窗口
	 * @param 对比手机ID字符串 $compare
	 */
	public function loadCompareOutline($compare)
	{
		return Factory::getResource('mobile')->loadCompareOutline($compare);
	}
	
	/**
	 * ajax加载对比列表
	 * @param 对比手机ID字符串 $compare
	 */
	public function showCompare($compare)
	{
		return Factory::getResource('mobile')->showCompare($compare);
	}
	
	/**
	 * 记录手机点阅量
	 * @param 手机ID $id
	 */
	public function markClick($id)
	{
		return Factory::getResource('mobile')->modify(
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
		$contact = Factory::getView('mobile/item')->showContact($id);
		if (is_numeric($contact['contact']['qq'])) {
			$contact['username'] = $contact['contact']['username'];
			$contact['email'] = $contact['contact']['qq'] . '@qq.com';
		}
		return $contact;
	}
}
