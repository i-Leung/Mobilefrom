<?php
class Customer_TabletController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$isLoged = Factory::isLoged();
		if (!$isLoged) {
			$this->redirect(Factory::getBaseUrl());
		} else {
			if (in_array($this->_request->getActionName(), array('publish-post', 'status', 'refresh'))) {
				Factory::getLogic('cache')->clearCache('tablet', true);
			}
		}
	}

	/**
	 * @todo 无理报错，暂未找到原因
	 */
	public function indexAction()
	{
	}
	
	/**
	 * 新版平板发布
	 */
	public function publishAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			if (Factory::getSession('customer/group') == '2') {
				$this->redirect(Factory::getUrl('customer'));
			} else {
				$total = Factory::getView('tablet/list')->getMyTabletTotal($customer_id);
				if ($customer_id != '2' && $total >= 2) {
					header("Content-type: text/html; charset=utf-8");
					echo '个人用户平板转让信息最多显示2个，请先将无效信息置为过期或删除！即将为您跳转到用户平板发布列表页面...';
					echo '<script type="text/javascript">
							function reload()
							{
								window.location.href = "/customer/tablet/active";
							}
							setInterval("reload()", 5000);
						  </script>';
					exit(0);
				}
				$this->loadLayout();
				$this->renderLayout();
			}
		} else {
			$this->redirect(Factory::getBaseUrl());
		}
	}
	
	/**
	 * 新版平板信息处理
	 */
	public function publishPostAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			$id = $this->_request->getParam('id');
			$tablet = $this->_request->getParam('tablet');
			$contact = $this->_request->getParam('contact');
			//基本信息
			$title = $tablet['title'];
			$thumb = $tablet['thumb'];
			$gallery = $tablet['gallery'];
			$price = $tablet['price'] ? $tablet['price'] : 0;
			$created_at = time();
			//构造过滤器
			$filter = array(
					1 => getLevel($price),//可换机、可购买有bug
					2 => $tablet['brand'],
					3 => $tablet['newly'],
					4 => $contact['region']
			);
			if ($tablet['model']) {
				$filter[5] = $tablet['model'];
			}
			//详细信息
			$data = array(
					'cost' => $tablet['cost'],
					'exchange' => $tablet['exchange'],
					'brand' => $tablet['brand'],
					'model' => $tablet['model'],
					'newly' => $tablet['newly'],
					'screen' => $tablet['screen'],
					'pbody' => $tablet['pbody'],
					'attachment' => $tablet['attachment'],
					'repair' => $tablet['repair'],
					'trouble' => $tablet['trouble'],
					'warranty' => $tablet['warranty'],
					'remark' => $tablet['remark']
			);
			$data = serialize($data);
			//联系信息
			$contact = serialize($contact);
			//整理后平板信息
			$params = array(
						'title' => $title,
						'thumb' => $thumb,
						'price' => $price,
						'gallery' => $gallery,
						'data' => $data,
						'contact' => $contact,
						'created_at' => $created_at
					);
			$tablet_id = null;
			if ($id) {
				$mLogic = Factory::getLogic('tablet');
				if ($mLogic->isActiveTablet($id)) {
					if ($mLogic->modify($id, $params, $filter)) {
						$tablet_id = $id;
					}
				} else {
					if ($mLogic->modifyInactive($id, $params, $filter)) {
						$tablet_id = $id;
						//平板资料库更新
						Factory::getLogic('system/tlib')->modifyModel($tablet['model'], array('offers' => '`offers` + 1'));
					}
				}
			} else {
				$tablet_id = Factory::getLogic('tablet')->create($params, $filter);
				//把最后使用联系方式更新到用户资料
				Factory::getLogic('customer')->modify(
					$customer_id, 
					array('contact' => $contact)
				);
				//平板资料库更新
				if (strlen($tablet['model']) > 2) {
					Factory::getLogic('system/tlib')->modifyModel($tablet['model'], array('offers' => '`offers` + 1'));
				}
			}
			$msg = Factory::getMsg();
			if (empty($msg) && $tablet_id) {
				echo json_encode($tablet_id);
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
	 * ajax获取平板信息
	 */
	public function getAction()
	{
		$id = $this->_request->getParam('id');
		$model = Factory::getLogic('tablet');
		$customer_id = $model->getPublisher($id);
		if ($customer_id == Factory::getSession('customer/id')) {
			if ($tablet = $model->getInfo($id)) {
				echo json_encode($tablet);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 用户有效平板列表
	 */
	public function activeAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 用户过期平板列表
	 */
	public function inactiveAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 改变平板信息状态
	 */
	public function statusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$tablet = Factory::getLogic('tablet');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $tablet->getPublisher($id) == $customer_id && $status) {
			if ($status == '1' && $customer_id != '2') {
				$total = Factory::getView('tablet/list')->getMyTabletTotal($customer_id);
				if ($total && $total >= 2) {
					echo json_encode(2);
					exit(0);
				}
			}
			$tablet->changeStatus($id, $status);
			switch ($status) {
				case '1':
					$tablet->createOrder($id);
					break;
				case '2':
				case '3':
					$tablet->removeOrder($id);
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
	 * 刷新平板信息
	 */
	public function refreshAction()
	{
		$id = $this->_request->getParam('id');
		$tablet = Factory::getLogic('tablet');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $tablet->getPublisher($id) == $customer_id) {
			if ($customer_id != '2') {
				$total = Factory::getView('tablet/list')->getMyTabletTotal($customer_id);
				if ($total && $total >= 2) {
					echo json_encode(2);
					exit(0);
				}
			}
			$tablet->modify($id, array('created_at' => time()));
			$tablet->removeOrder($id);
			$tablet->createOrder($id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
