<?php
class Logic_Purchase
{
	/**
	 * 判断信息是否可用
	 * @param 求购信息ID $id
	 * @return boolean
	 */
	public function isActivePurchase($id)
	{
		return Factory::getResource('purchase')->isActivePurchase($id);
	}

	/**
	 * 发布求购信息
	 * @param 求购信息 $params
	 * @param 过滤器信息 $filter
	 */
	public function create($params, $filter)
	{
		$purchase = Factory::getResource('purchase');
		$purchase_id = null;
		if ($purchase->create($params)) {
			$purchase_id = $purchase->getNewId();
			$fparams = array();
			foreach ($filter as $key => $value) {
				$fparams['filter_id'][] = $key;
				$fparams['value_id'][] = $value;
				$fparams['purchase_id'][] = $purchase_id;
			}
			Factory::getResource('relation/purchase/filter')->create($fparams, 'multiple');
			Factory::getResource('relation/customer/purchase')->create(
					array(
							'purchase_id' => $purchase_id,
							'customer_id' => Factory::getSession('customer/id')
					)
			);
			$this->createOrder($purchase_id);
			return $purchase_id;
		}
		return false;
	}
	
	/**
	 * 修改求购信息
	 * @param 求购信息ID $id
	 * @param 求购信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modify($id, $params, $filter = null)
	{
		if (Factory::getResource('purchase')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('relation/purchase/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'purchase_id = ?' => $id,
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
	 * 修改非可用求购信息并置为可用
	 * @param 求购信息ID $id
	 * @param 求购信息 $params
	 * @param 过滤器信息 $filter
	 * @return boolean
	 */
	public function modifyInactive($id, $params, $filter = null)
	{
		if (Factory::getResource('inactive/purchase')->modify(array('id = ?' => $id), $params)) {
			if ($filter) {
				$fparams = array();
				$fmodel = Factory::getResource('inactive/purchase/filter');
				foreach ($filter as $key => $value) {
					$fmodel->modify(
						array(
							'purchase_id = ?' => $id,
							'filter_id = ?' => $key
						),
						array('value_id' => $value)
					);
				}
				$this->movePurchaseInactiveToActive($id);
				$this->removeOrder($id);
				$this->createOrder($id);
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 获取求购信息发布者ID
	 * @param 求购信息ID $id
	 * @return false或发布者ID
	 */
	public function getPublisher($id)
	{
		return Factory::getResource('relation/customer/purchase')->getPublisher($id);
	}
	
	/**
	 * 获取求购信息详细
	 * @param 求购信息ID $id
	 * @return false或求购信息详细
	 */
	public function getInfo($id)
	{
		$info = Factory::getResource('purchase')->getInfo($id);
		if ($info) {
			$info['contact'] = unserialize($info['contact']);
		}
		return $info;
	}
	
	/**
	 * 获取求购概述信息
	 * @param 求购信息ID $id
	 * @return false或求购概述详细
	 */
	public function getSummarize($id)
	{
		return Factory::getResource('purchase')->getSummarize($id);
	}
	
	/**
	 * 改变求购信息状态
	 * @param 求购信息ID $id
	 * @param 状态 $status
	 */
	public function changeStatus($id, $status)
	{
		switch ($status) {
			case '1':
				return $this->movePurchaseInactiveToActive($id);
				break;
			case '2':
			case '3':
				return $this->movePurchaseActiveToInactive($id, $status);
				break;
		}
	}

	/**
	 * 求购信息移到非可用库
	 * @param 求购信息ID $id
	 * @param 状态 $status
	 */
	public function movePurchaseActiveToInactive($id, $status)
	{
		if ($this->isActivePurchase($id)) {
			//获取求购记录条
			$p_resource = Factory::getResource('purchase');
			$purchase = $p_resource->getActivePurchaseRecord($id);
			$c_resource = Factory::getResource('relation/customer/purchase');
			$customer = $c_resource->getActivePurchasePublisherRecord($id);
			$f_resource = Factory::getResource('relation/purchase/filter');
			$filters = $f_resource->getActivePurchaseFilterRecord($id);
			//转移到非可用库
			$purchase['status'] = $status;
			Factory::getResource('inactive/purchase')->create($purchase);
			Factory::getResource('inactive/purchase/customer')->create($customer);
			$in_filter = Factory::getResource('inactive/purchase/filter');
			foreach ($filters as $filter) {
				$in_filter->create($filter);	
			}
			//移除可用库数据
			$p_resource->remove(array('id = ?' => $id));
			$c_resource->remove(array('purchase_id = ?' => $id));
			$f_resource->remove(array('purchase_id = ?' => $id));
		} else {
			Factory::getResource('inactive/purchase')->modify(
				array('id = ?' => $id), 
				array('status' => $status)
			);
		}
	}

	/**
	 * 求购信息移到可用库
	 * @param 求购信息ID $id
	 * @param 状态 $status
	 */
	public function movePurchaseInactiveToActive($id)
	{
		//获取非可用求购记录条
		$p_resource = Factory::getResource('inactive/purchase');
		$in_purchase = $p_resource->getInactivePurchaseRecord($id);
		$c_resource = Factory::getResource('inactive/purchase/customer');
		$in_customer = $c_resource->getInactivePurchasePublisherRecord($id);
		$f_resource = Factory::getResource('inactive/purchase/filter');
		$in_filters = $f_resource->getInactivePurchaseFilterRecord($id);
		//转移到可用库
		$in_purchase['created_at'] = time();
		unset($in_purchase['status']);
		Factory::getResource('purchase')->create($in_purchase);
		Factory::getResource('relation/customer/purchase')->create($in_customer);
		$filter = Factory::getResource('relation/purchase/filter');
		foreach ($in_filters as $in_filter) {
			$filter->create($in_filter);	
		}
		//移除非可用库数据
		$p_resource->remove(array('id = ?' => $id));
		$c_resource->remove(array('purchase_id = ?' => $id));
		$f_resource->remove(array('purchase_id = ?' => $id));
	}
	
	/**
	 * 创建排序记录
	 * @param 求购信息ID $id
	 * @return boolean
	 */
	public function createOrder($id)
	{
		return Factory::getResource('relation/purchase/order')->create(
				array('purchase_id' => $id)
		);
	}
	
	/**
	 * 移除排序记录
	 * @param 求购信息ID $id
	 * @return boolean
	 */
	public function removeOrder($id)
	{
		return Factory::getResource('relation/purchase/order')->remove(
				array('purchase_id = ?' => $id)
		);
	}
	
	/**
	 * 记录手机点阅量
	 * @param 手机ID $id
	 */
	public function markClick($id)
	{
		return Factory::getResource('purchase')->modify(
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
		$contact = $this->showContact($id);
		if (is_numeric($contact['contact']['qq'])) {
			$contact['username'] = $contact['contact']['username'];
			$contact['email'] = $contact['contact']['qq'] . '@qq.com';
		}
		return $contact;
	}
}
