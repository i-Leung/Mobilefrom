<?php
class Customer_StoreController extends Project_Controller_Action_Front
{
	public function init()
	{
		parent::init();
		$action = $this->_request->getActionName();
		$isLoged = Factory::isLoged();
		if (!$isLoged || Factory::getSession('customer/group') != '2') {
			$this->redirect(Factory::getBaseUrl());
		}
	}

	/******************************************店铺管理******************************************/
	public function indexAction()
	{}

	/**
	 * 上传营业执照
	 */
	public function licenseAction()
	{
		$ext = pathinfo($_FILES["upload"]["name"], PATHINFO_EXTENSION);
		if (in_array($ext, array('jpg', 'JPG'))) {
			try {
				$customer_id = Factory::getSession('customer/id');
				$filename = time() . rand(1000, 9999) . '.' . $ext;
				$odir = 'upload/store/' . $customer_id . '/';
				$tdir = 'upload/store/thumb/' . $customer_id . '/';
				//判断文件夹是否存在
				if (!is_dir($odir)) {//保存原图
					mkdir($odir, 0777);
				}
				$tmp = file_get_contents($_FILES['upload']['tmp_name']);
				$origin = fopen($odir . $filename, "w+");
				$resultOrigin = fwrite($origin, $tmp);
				fclose($origin);
				if (!is_dir($tdir)) {//保存缩略图
					mkdir($tdir, 0777);
				}
				$this->_resize($_FILES['upload']['tmp_name'], $tdir . $filename);
				//输出图片文件<img>标签
				echo json_encode('/' . $tdir . $filename);
				exit(0);
			} catch (Exception $e) {
				Factory::setMsg($e->getMessage());
			}
		}
		echo json_encode(0);
	}

	/**
	 * 调整图片尺寸
	 */
	private function _resize($src, $dest)
	{
		$org = imagecreatefromjpeg($src);
		list($width, $height) = getimagesize($src);
		if ($width >= $height) {
			$newheight = 200;
			$newwidth = ($width / $height) * $newheight;
		} else {
			$newwidth = 200;
			$newheight = ($height / $width) * $newwidth;
		}
		$tmp = imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp, $org, 0, 0, 0, 0, $newwidth, $newheight, $width, $height); 
		imagejpeg($tmp, $dest, 100);
		imagedestroy($org);
		imagedestroy($tmp); // NOTE: PHP will clean up the temp file it created when the request
	}

	/**
	 * 申请开店
	 */
	public function applyAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 获取店铺申请资料
	 */
	public function getApplyAction()
	{
		if ($customer = Factory::getSession('customer/id')) {
			if ($info = Factory::getView('customer/store')->getMyStoreInfo($customer)) {
				echo json_encode($info);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 申请资料提交
	 */
	public function applySubmitAction()
	{
		$store = $this->_request->getParam('store', null);
		if ($store) {
			$store['customer_id'] = Factory::getSession('customer/id');
			$store['gallery'] = str_replace(Factory::getBaseUrl(), '', $store['gallery']);
			if ($id = Factory::getView('customer/store')->getStoreId($store['customer_id'])) {
				$result = Factory::getLogic('store')->modifyStore($id, $store);
			} else {
				$store['created_at'] = time();
				$result = Factory::getLogic('store')->createStore($store);
			}
			if ($result) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 店铺资料
	 */
	public function infoAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 店铺设置
	 */
	public function settingAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 获取店铺设置
	 */
	public function getSettingAction()
	{
		$view = Factory::getView('customer/store');
		$id = $view->getStoreId(Factory::getSession('customer/id'));
		if ($id && $setting = $view->getStoreSetting($id)) {
			echo json_encode($setting);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 提交店铺设置
	 */
	public function settingSubmitAction()
	{
		$setting = $this->_request->getParam('setting');
		if ($setting) {
			$id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
			$setting['working'] = serialize($setting['working']);
			$setting['tel'] = array();
			foreach ($setting['telname'] as $k => $v) {
				$setting['tel'][$k] = array(
					'name' => $v,
					'num' => $setting['telnum'][$k]
				);
			}
			$setting['tel'] = serialize($setting['tel']);
			unset($setting['telname']);
			unset($setting['telnum']);
			$setting['qq'] = array();
			foreach ($setting['qqname'] as $k => $v) {
				$setting['qq'][$k] = array(
					'name' => $v,
					'num' => $setting['qqnum'][$k]
				);
			}
			$setting['qq'] = serialize($setting['qq']);
			unset($setting['qqname']);
			unset($setting['qqnum']);
			if ($result = Factory::getLogic('store')->modifyStoreSetting($id, $setting)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 店铺统计
	 */
	public function analysisAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}
	
	/******************************************店铺服务管理******************************************/
	/**
	 * 服务列表
	 */
	public function serviceAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 提交服务信息
	 */
	public function submitServiceAction()
	{
		$id = $this->_request->getParam('id', null);
		$introduction = $this->_request->getParam('introduction', null);
		$customer = Factory::getSession('customer/id');
		if ($customer && $store = Factory::getView('customer/store')->getStoreId($customer)) {
			$logic = Factory::getLogic('store');
			if ($logic->removeStoreService($store)) {
				if ($id) {
					$data = array('service_id' => $id, 'introduction' => $introduction);
					$num = count($id);
					for ($i = 1; $i <= $num; $i++) {
						$data['store_id'][] = $store;
					}
					$logic->createStoreService($data);
				}
				$id = $id ? serialize($id) : '';
				$logic->modifyStore($store, array('service' => $id));
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取我的服务列表
	 */
	public function loadMyServiceAction()
	{
		$customer = Factory::getSession('customer/id');
		if ($customer && $store = Factory::getView('customer/store')->getStoreId($customer)) {
			$services = Factory::getView('customer/store')->loadMyServiceList($store);
			if ($services) {
				echo json_encode($services);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 我提供的服务
	 */
	public function myserviceAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 申请服务
	 */
	public function applyServiceAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 提交服务申请
	 */
	public function submitServiceApplyAction()
	{
		$customer = Factory::getSession('customer');
		if ($customer && $customer['group'] == '2') {
			$id = $this->_request->getParam('id', null);
			$service = $this->_request->getParam('service', null);
			$contact = $this->_request->getParam('contact', null);
			$service['region'] = $contact['region'];
			$service['contact'] = serialize($contact);
			$service['created_at'] = time();
			$result = false;
			$logic = Factory::getLogic('customer');
			if ($id) {
				if ($logic->isMyService($id, $customer['id'])) {
					$result = $logic->modifyService($id, $service);
				}
			} else {
				$service['customer_id'] = $customer['id'];
				$result = $logic->createService($service);
			}
			if ($result) {
				$logic->modify(
					$customer['id'], 
					array('contact' => $service['contact'])
				);
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 删除服务记录
	 */
	public function removeServiceAction()
	{
		$customer = Factory::getSession('customer');
		if ($customer && $customer['group'] == '2') {
			$id = $this->_request->getParam('id', null);
			$logic = Factory::getLogic('customer');
			$result = false;
			if ($logic->isMyService($id, $customer['id'])) {
				$result = $logic->removeService($id);
			}
			if ($result) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取服务内容
	 */
	public function getServiceAction()
	{
		$sid = $this->_request->getParam('sid', null);
		$customer = $this->_request->getParam('customer', null);
		if ($sid && $customer) {
			$result = Factory::getView('customer/info')->getService($sid, $customer);
			if ($result) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($result);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/******************************************店铺活动管理******************************************/
	/**
	 * 店铺活动列表
	 */
	public function activityListAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 店铺活动申请
	 */
	public function activityApplyAction()
	{
		$id = $this->_request->getParam('id', null);
		$store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if (Factory::getView('customer/store')->canEditActivity($store, $id)) {
			$this->redirect(Factory::getUrl('customer/store/activity-list'));
			exit(0);
		}
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 提交活动资料
	 */
	public function submitActivityAction()
	{
		$result = 0;
		$id = $this->_request->getParam('id', null);
		$data = $this->_request->getParam('activity', null);
		if ($data && $store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'))) {
			$logic = Factory::getLogic('store');
			$base = Factory::getBaseUrl();
			$data['thumb'] = str_replace($base, '', $data['thumb']);
			$data['gallery'] = str_replace($base, '', $data['gallery']);
			if ($id) {
				if (isset($data['limit'])) {
					$data['limit'] = $data['limit'] == '-1' ? '0' : 60 * 60 * 24 * $data['limit'] + time();
					$data['status'] = 1;
				}
				$result = $logic->modifyActivity($id, $store, $data);
			} else {
				$data['limit'] = $data['limit'] == '-1' ? '0' : 60 * 60 * 24 * $data['limit'] + time();
				$data['store_id'] = $store;
				$result = $logic->createActivity($data);
			}
			if ($result) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取指定活动资料
	 */
	public function getActivityAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			if ($info = Factory::getView('customer/store')->getMyActivityInfo($id)) {
				echo json_encode($info);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 设置活动过期
	 */
	public function setActivityExpiredAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id && $store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'))) {
			if (Factory::getLogic('store')->modifyActivity($id, $store, array('status' => '0'))) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/******************************************店铺手机管理******************************************/
	/**
	 * 发布手机
	 */
	public function mobilePublishAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 提交手机发布数据
	 */
	public function mobileSubmitAction()
	{
		$model = $this->_request->getParam('model', null);
		$mobile = $this->_request->getParam('mobile', null);
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($model && $mobile && $store_id) {
			$logic = Factory::getLogic('store/mobile');
			$model['gallery'] = str_replace(Factory::getBaseUrl(), '', $model['gallery']);
			if (isset($model['store_model_id'])) {
				if ($smodel_id = Factory::getView('store/item')->isMyMobileModel($model['store_model_id'], $store_id)) {
					$logic->removeMobileItemBySMid($smodel_id);
					unset($model['store_model_id']);
					$model['status'] = '1';
					$logic->modifyMobileModel($smodel_id, $model);
				}
			} else {
				$model['store_id'] = $store_id;
				$smodel_id = $logic->createMobileModel($model);
				//手机资料库更新
				Factory::getLogic('system/mlib')->modifyModel($model['model_id'], array('offers' => '`offers` + 1'));
			}
			if ($smodel_id) {
				$total = count($mobile['version']);
				for ($i = 1; $i <= $total; $i++) {
					$mobile['store_model_id'][] = $smodel_id;
				}
				if ($logic->createMobileItem($mobile)) {
					$msg = Factory::getMsg();
					if (empty($msg)) {
						echo json_encode(1);
						exit(0);
					}
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 手机列表
	 */
	public function mobileListAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}

	/**
	 * 获取手机型号出售信息
	 */
	public function mobileInfoAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id && $model = Factory::getView('store/item')->getMobileModelInfo($id)) {
			echo json_encode($model);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 检测手机型号出售信息是否存在
	 */
	public function checkMobileModelExistAction()
	{
		$brand_id = $this->_request->getParam('brand_id', null);
		$model_id = $this->_request->getParam('model_id', null);
		$store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($brand_id && $model_id && $store && $smid = Factory::getView('store/item')->checkMobileModelExist($brand_id, $model_id, $store)) {
			echo json_encode($smid);
			exit(0);
		}
		echo json_encode(0);
	}

	/**
	 * 改变手机型号出售信息状态
	 */
	public function changeMobileModelStatusAction()
	{
		$id = $this->_request->getParam('id', null);
		$status = $this->_request->getParam('status', '1');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyMobileModel($id, $store_id)) {
			if (Factory::getLogic('store/mobile')->modifyMobileModel($id, array('status' => $status))) {
				//手机资料库更新
				$model_id = $sview->getMlibModelId($id);
				switch ($status) {
					case '1':
						Factory::getLogic('system/mlib')->modifyModel($model_id, array('offers' => '`offers` + 1'));
						break;
					case '0':
					case '2':
						Factory::getLogic('system/mlib')->modifyModel($model_id, array('offers' => '`offers` - 1'));
						break;
				}
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 改变手机型号热销状态
	 */
	public function changeMobileModelHotAction()
	{
		$id = $this->_request->getParam('id', null);
		$type = $this->_request->getParam('type', '0');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyMobileModel($id, $store_id)) {
			if ($result = Factory::getLogic('store/mobile')->modifyMobileModelHot($id, $store_id, $type)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 改变手机型号推荐状态
	 */
	public function changeMobileModelRecommendAction()
	{
		$id = $this->_request->getParam('id', null);
		$type = $this->_request->getParam('type', '0');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyMobileModel($id, $store_id)) {
			if (Factory::getLogic('store/mobile')->modifyMobileModelRecommend($id, $store_id, $type)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 修改手机出售信息
	 */
	public function saveMobileItemAction()
	{
		$id = $this->_request->getParam('id', null);
		$mobile = $this->_request->getParam('mobile', null);
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($id && $mobile && Factory::getView('store/item')->isMyMobileItem($id, $store_id)) {
			if (Factory::getLogic('store/mobile')->modifyMobileItem($id, $mobile)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 改变手机出售信息状态
	 * @param 手机出售信息ID $id
	 */
	public function changeMobileItemStatusAction()
	{
		$id = $this->_request->getParam('id', null);
		$status = $this->_request->getParam('status', '1');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($id && Factory::getView('store/item')->isMyMobileItem($id, $store_id)) {
			if (Factory::getLogic('store/mobile')->modifyMobileItem($id, array('status' => $status))) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/******************************************店铺平板管理******************************************/
	/**
	 * 发布平板
	 */
	public function tabletPublishAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}
	
	/**
	 * 提交平板发布数据
	 */
	public function tabletSubmitAction()
	{
		$model = $this->_request->getParam('model', null);
		$tablet = $this->_request->getParam('tablet', null);
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($model && $tablet && $store_id) {
			$logic = Factory::getLogic('store/tablet');
			$model['gallery'] = str_replace(Factory::getBaseUrl(), '', $model['gallery']);
			if (isset($model['store_model_id'])) {
				if ($smodel_id = Factory::getView('store/item')->isMyTabletModel($model['store_model_id'], $store_id)) {
					$logic->removeTabletItemBySMid($smodel_id);
					unset($model['store_model_id']);
					$model['status'] = '1';
					$logic->modifyTabletModel($smodel_id, $model);
				}
			} else {
				$model['store_id'] = $store_id;
				$smodel_id = $logic->createTabletModel($model);
				//手机资料库更新
				Factory::getLogic('system/tlib')->modifyModel($model['model_id'], array('offers' => '`offers` + 1'));
			}
			if ($smodel_id) {
				$total = count($tablet['version']);
				for ($i = 1; $i <= $total; $i++) {
					$tablet['store_model_id'][] = $smodel_id;
				}
				if ($logic->createTabletItem($tablet)) {
					$msg = Factory::getMsg();
					if (empty($msg)) {
						echo json_encode(1);
						exit(0);
					}
				}
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 平板列表
	 */
	public function tabletListAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}
	
	/**
	 * 获取平板型号出售信息
	 */
	public function tabletInfoAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id && $model = Factory::getView('store/item')->getTabletModelInfo($id)) {
			echo json_encode($model);
			exit(0);
		}
		echo json_encode(0);
	}
	
	/**
	 * 检测平板型号出售信息是否存在
	 */
	public function checkTabletModelExistAction()
	{
		$brand_id = $this->_request->getParam('brand_id', null);
		$model_id = $this->_request->getParam('model_id', null);
		$store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($brand_id && $model_id && $store && $smid = Factory::getView('store/item')->checkTabletModelExist($brand_id, $model_id, $store)) {
			echo json_encode($smid);
			exit(0);
		}
		echo json_encode(0);
	}
	
	/**
	 * 改变平板型号出售信息状态
	 */
	public function changeTabletModelStatusAction()
	{
		$id = $this->_request->getParam('id', null);
		$status = $this->_request->getParam('status', '1');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyTabletModel($id, $store_id)) {
			if (Factory::getLogic('store/tablet')->modifyTabletModel($id, array('status' => $status))) {
				//手机资料库更新
				$model_id = $sview->getTlibModelId($id);
				switch ($status) {
					case '1':
						Factory::getLogic('system/tlib')->modifyModel($model_id, array('offers' => '`offers` + 1'));
						break;
					case '0':
					case '2':
						Factory::getLogic('system/tlib')->modifyModel($model_id, array('offers' => '`offers` - 1'));
						break;
				}
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 改变平板型号热销状态
	 */
	public function changeTabletModelHotAction()
	{
		$id = $this->_request->getParam('id', null);
		$type = $this->_request->getParam('type', '0');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyTabletModel($id, $store_id)) {
			if ($result = Factory::getLogic('store/tablet')->modifyTabletModelHot($id, $store_id, $type)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 改变平板型号推荐状态
	 */
	public function changeTabletModelRecommendAction()
	{
		$id = $this->_request->getParam('id', null);
		$type = $this->_request->getParam('type', '0');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		$sview = Factory::getView('store/item');
		if ($id && $sview->isMyTabletModel($id, $store_id)) {
			if (Factory::getLogic('store/tablet')->modifyTabletModelRecommend($id, $store_id, $type)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 修改平板出售信息
	 */
	public function saveTabletItemAction()
	{
		$id = $this->_request->getParam('id', null);
		$tablet = $this->_request->getParam('tablet', null);
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($id && $tablet && Factory::getView('store/item')->isMyTabletItem($id, $store_id)) {
			if (Factory::getLogic('store/tablet')->modifyTabletItem($id, $tablet)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 改变平板出售信息状态
	 * @param 手机出售信息ID $id
	 */
	public function changeTabletItemStatusAction()
	{
		$id = $this->_request->getParam('id', null);
		$status = $this->_request->getParam('status', '1');
		$store_id = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'));
		if ($id && Factory::getView('store/item')->isMyTabletItem($id, $store_id)) {
			if (Factory::getLogic('store/tablet')->modifyTabletItem($id, array('status' => $status))) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/******************************************店铺配件管理******************************************/
	/**
	 * 发布配件
	 */
	public function accessoryPublishAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}
	
	/**
	 * 配件列表
	 */
	public function accessoryListAction()
	{
		$this->loadLayout('customer')->renderLayout();
	}
	
	/**
	 * 获取配件属性
	 */
	public function loadAccessoryAttrAction()
	{
		$kind = $this->_request->getParam('kind', null);
		$result = 0;
		$view = Factory::getView('accessory/item');
		switch ($kind) {
			case '1':
				$result = $view->loadBatteryAttr();
				break;
			case '2':
				$result = $view->loadBluetoothAttr('wearing');
				break;
			case '3':
				$result = $view->loadChargerlineAttr();
				break;
			case '4':
				$result = $view->loadEarphoneAttr();
				break;
			case '5':
				$result = $view->loadFoilAttr();
				break;
			case '6':
				$result = $view->loadMemoryAttr();
				break;
			case '7':
				$result = $view->loadCoverAttr();
				break;
		}
		echo json_encode($result);
		exit(0);
	}
	
	/**
	 * 提交配件质量
	 */
	public function submitAccessoryAction()
	{
		$kind = $this->_request->getParam('kind', null);
		$title = $this->_request->getParam('title', null);
		$price = $this->_request->getParam('price', null);
		$description = $this->_request->getParam('description', null);
		$thumb = $this->_request->getParam('thumb', null);
		$gallery = $this->_request->getParam('gallery', null);
		$data = $this->_request->getParam('data', null);
		if ($kind && $data && $store = Factory::getView('customer/store')->getStoreId(Factory::getSession('customer/id'))) {
			$item = array(
					'store_id' => $store,
					'price' => $price,
					'title' => $title,
					'description' => $description,
					'thumb' => $thumb,
					'gallery' => $gallery,
					'data' => serialize($data)
			);
			Factory::getLogic('accessory')->createAccessory($kind, $item, $data);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
