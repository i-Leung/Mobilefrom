<?php
class Customer_PurchaseController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$action = $this->_request->getActionName();
		$isLoged = Factory::isLoged();
		if (!$isLoged) {
			$this->redirect(Factory::getBaseUrl());
		} else {
			if (in_array($this->_request->getActionName(), array('publish-post', 'status', 'refresh'))) {
				Factory::getLogic('cache')->clearCache('purchase', true);
			}
		}
	}
	
	/**
	 * 新版发布求购
	 */
	public function publishAction()
	{
		if (Factory::getSession('customer/id')) {
			if (Factory::getSession('customer/group') == '2') {
				$this->redirect(Factory::getUrl('customer'));
			} else {
				$this->loadLayout()->renderLayout();
			}
		} else {
			$this->redirect(Factory::getBaseUrl());
		}
	}
	
	/**
	 * 新版求购信息处理
	 */
	public function publishPostAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			$id = $this->_request->getParam('id');
			$purchase = $this->_request->getParam('purchase');
			$contact = $this->_request->getParam('contact');
			//基本信息
			$title = $purchase['title'];
			$brand = $purchase['brand'];
			$amount = $purchase['amount'];
			$price = $purchase['price'];
			$remark = $purchase['remark'];
			$created_at = time();
			//构造过滤器
			$filter = array(
					1 => $purchase['brand'],
					2 => $contact['region'],
					3 => Factory::getSession('customer/group')
			);
			//联系信息
			$contact = serialize($contact);
			//整理后手机信息
			$params = array(
					'title' => $title,
					'brand' => $brand,
					'amount' => $amount,
					'price' => $price,
					'remark' => $remark,
					'contact' => $contact,
					'created_at' => $created_at
			);
			$purchase_id = null;
			if ($id) {
				$pLogic = Factory::getLogic('purchase');
				if ($pLogic->isActivePurchase($id)) {
					if ($pLogic->modify($id, $params, $filter)) {
						$purchase_id = $id;
					}
				} else {
					if ($pLogic->modifyInactive($id, $params, $filter)) {
						$purchase_id = $id;
					}
				}
			} else {
				$purchase_id = Factory::getLogic('purchase')->create($params, $filter);
				Factory::getLogic('customer')->modify(
					$customer_id, 
					array('contact' => $contact)
				);
			}
			$msg = Factory::getMsg();
			if (empty($msg) && $purchase_id) {
				echo json_encode($purchase_id);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 新版发布成功
	 */
	public function successAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * ajax获取求购信息
	 */
	public function getAction()
	{
		$id = $this->_request->getParam('id');
		$model = Factory::getLogic('purchase');
		$customer_id = $model->getPublisher($id);
		if ($customer_id == Factory::getSession('customer/id')) {
			if ($purchase = $model->getInfo($id)) {
				$purchase['price'] = explode('-', $purchase['price']);
				$purchase['min'] = $purchase['price'][0];
				$purchase['max'] = $purchase['price'][1];
				unset($purchase['price']);
				echo json_encode($purchase);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 用户有效求购列表
	 */
	public function activeAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 用户过期求购列表
	 */
	public function inactiveAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 改变求购信息状态
	 */
	public function statusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$purchase = Factory::getLogic('purchase');
		if ($id && $status && $purchase->getPublisher($id) == Factory::getSession('customer/id')) {
			$purchase->changeStatus($id, $status);
			switch ($status) {
				case '1':
					$purchase->createOrder($id);
					break;
				case '2':
				case '3':
					$purchase->removeOrder($id);
					break;
			}
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 刷新求购信息
	 */
	public function refreshAction()
	{
		$id = $this->_request->getParam('id');
		$purchase = Factory::getLogic('purchase');
		if ($id && $purchase->getPublisher($id) == Factory::getSession('customer/id')) {
			$purchase->modify($id, array('created_at' => time()));
			$purchase->removeOrder($id);
			$purchase->createOrder($id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
