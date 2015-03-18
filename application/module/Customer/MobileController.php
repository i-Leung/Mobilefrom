<?php
class Customer_MobileController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$isLoged = Factory::isLoged();
		if (!$isLoged) {
			$this->redirect(Factory::getBaseUrl());
		} else {
			if (in_array($this->_request->getActionName(), array('publish-post', 'status', 'refresh'))) {
				Factory::getLogic('cache')->clearCache('mobile', true);
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
	 * 新版手机发布
	 */
	public function publishAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			if (Factory::getSession('customer/group') == '2') {
				$this->redirect(Factory::getUrl('customer'));
			} else {
				$total = Factory::getView('mobile/list')->getMyMobileTotal($customer_id);
				if ($customer_id != '2' && $total >= 2) {
					header("Content-type: text/html; charset=utf-8");
					echo '个人用户手机转让信息最多显示2个，请先将无效信息置为过期或删除！即将为您跳转到用户手机发布列表页面...';
					echo '<script type="text/javascript">
							function reload()
							{
								window.location.href = "/customer/mobile/active";
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
	 * 新版手机信息处理
	 */
	public function publishPostAction()
	{
		if ($customer_id = Factory::getSession('customer/id')) {
			$id = $this->_request->getParam('id');
			$mobile = $this->_request->getParam('mobile');
			$contact = $this->_request->getParam('contact');
			//基本信息
			$title = $mobile['title'];
			$thumb = $mobile['thumb'];
			$gallery = $mobile['gallery'];
			$price = $mobile['price'] ? $mobile['price'] : 0;
			$created_at = time();
			//构造过滤器
			$filter = array(
					1 => getLevel($price),//可换机、可购买有bug
					2 => $mobile['brand'],
					3 => $mobile['newly'],
					//4 => $mobile['source'],
					//5 => $mobile['os'],
					6 => $contact['region'],
					7 => Factory::getSession('customer/group')
			);
			if ($mobile['model']) {
				$filter[8] = $mobile['model'];
			}
			//详细信息
			$data = array(
					'cost' => $mobile['cost'],
					'exchange' => $mobile['exchange'],
					'brand' => $mobile['brand'],
					'model' => $mobile['model'],
					'newly' => $mobile['newly'],
					'screen' => $mobile['screen'],
					'pbody' => $mobile['pbody'],
					//'source' => $mobile['source'],
					//'os' => $mobile['os'],
					'attachment' => $mobile['attachment'],
					'repair' => $mobile['repair'],
					'trouble' => $mobile['trouble'],
					'warranty' => $mobile['warranty'],
					'remark' => $mobile['remark']
			);
			$data = serialize($data);
			//联系信息
			$contact = serialize($contact);
			//整理后手机信息
			$params = array(
						'title' => $title,
						'thumb' => $thumb,
						'price' => $price,
						'gallery' => $gallery,
						'data' => $data,
						'contact' => $contact,
						'created_at' => $created_at
					);
			$mobile_id = null;
			if ($id) {
				$mLogic = Factory::getLogic('mobile');
				if ($mLogic->isActiveMobile($id)) {
					if ($mLogic->modify($id, $params, $filter)) {
						$mobile_id = $id;
					}
				} else {
					if ($mLogic->modifyInactive($id, $params, $filter)) {
						$mobile_id = $id;
						//手机资料库更新
						Factory::getLogic('system/mlib')->modifyModel($mobile['model'], array('offers' => '`offers` + 1'));
					}
				}
			} else {
				$mobile_id = Factory::getLogic('mobile')->create($params, $filter);
				//把最后使用联系方式更新到用户资料
				Factory::getLogic('customer')->modify(
					$customer_id, 
					array('contact' => $contact)
				);
				//手机资料库更新
				if (strlen($mobile['model']) > 2) {
					Factory::getLogic('system/mlib')->modifyModel($mobile['model'], array('offers' => '`offers` + 1'));
				}
			}
			$msg = Factory::getMsg();
			if (empty($msg) && $mobile_id) {
				echo json_encode($mobile_id);
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
	 * ajax获取手机信息
	 */
	public function getAction()
	{
		$id = $this->_request->getParam('id');
		$model = Factory::getLogic('mobile');
		$customer_id = $model->getPublisher($id);
		if ($customer_id == Factory::getSession('customer/id')) {
			if ($mobile = $model->getInfo($id)) {
				echo json_encode($mobile);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 用户有效手机列表
	 */
	public function activeAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 用户过期手机列表
	 */
	public function inactiveAction()
	{
		$this->loadLayout('customer');
		$this->renderLayout();
	}
	
	/**
	 * 改变手机信息状态
	 */
	public function statusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		$mobile = Factory::getLogic('mobile');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $mobile->getPublisher($id) == $customer_id && $status) {
			if ($status == '1' && $customer_id != '2') {
				$total = Factory::getView('mobile/list')->getMyMobileTotal($customer_id);
				if ($total && $total >= 2) {
					echo json_encode(2);
					exit(0);
				}
			}
			$mobile->changeStatus($id, $status);
			switch ($status) {
				case '1':
					$mobile->createOrder($id);
					break;
				case '2':
				case '3':
					$mobile->removeOrder($id);
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
	 * 刷新手机信息
	 */
	public function refreshAction()
	{
		$id = $this->_request->getParam('id');
		$mobile = Factory::getLogic('mobile');
		$customer_id = Factory::getSession('customer/id');
		if ($id && $mobile->getPublisher($id) == $customer_id) {
			if ($customer_id != '2') {
				$total = Factory::getView('mobile/list')->getMyMobileTotal($customer_id);
				if ($total && $total >= 2) {
					echo json_encode(2);
					exit(0);
				}
			}
			$mobile->modify($id, array('created_at' => time()));
			$mobile->removeOrder($id);
			$mobile->createOrder($id);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
