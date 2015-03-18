<?php
class Monitor_System_TlibController extends Project_Controller_Action_Admin
{
	/*********************************************属性操作*********************************************/
	/**
	 * 属性列表
	 */
	public function attributeListAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 属性信息
	 */
	public function attributeAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 添加属性
	 */
	public function addAttributeAction()
	{
		$label = $this->_request->getParam('label');
		$type = $this->_request->getParam('type');	
		if ($label && $type) {
			Factory::getResource('system/tlib/attribute')->create(
				array('label' => $label, 'type' => $type)
			);
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/attribute-list'));
	}

	/**
	 * 添加属性值
	 */
	public function addAttributeValueAction()
	{
		$value = $this->_request->getParam('value');
		if ($value) {
			Factory::getResource('system/tlib/attribute/value')->create($value);
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/attribute', array('id' => $value['attribute_id'])));
	}

	/**
	 * 改变属性状态
	 */
	public function changeAttributeStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/tlib')->changeAttributeStatus($id, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/*********************************************平板操作*********************************************/
	/**
	 * 品牌列表
	 */
	public function brandListAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 添加品牌
	 */
	public function addBrandAction()
	{
		$brand = $this->_request->getParam('brand');
		if ($brand) {
			Factory::getLogic('system/tlib')->createBrand(array('label' => $brand));
			Factory::getLogic('cache')->clearCache('brands');
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/brand-list'));
	}

	/**
	 * 修改品牌
	 */
	public function modifyBrandAction()
	{
		$id = $this->_request->getParam('id');
		$brand = array(
			'label' => $this->_request->getParam('label'),
			'sort' => $this->_request->getParam('sort')
		);
		if ($id && $brand) {
			Factory::getLogic('system/tlib')->modifyBrand($id, $brand);
			Factory::getLogic('cache')->clearCache('brands');
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/brand-list'));
	}

	/**
	 * 改变平板品牌状态
	 */
	public function statusBrandAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			Factory::getLogic('system/tlib')->statusBrand($id, $status);
			Factory::getLogic('cache')->clearCache('brands');
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 品牌型号列表
	 */
	public function modelListAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 添加型号
	 */
	public function addModelAction()
	{
		$bid = $this->_request->getParam('bid');
		$model = $this->_request->getParam('model');
		if ($bid && $model) {
			Factory::getLogic('system/tlib')->createModel(array('bid' => $bid, 'label' => $model));
			Factory::getLogic('cache')->clearCache('models-' . $bid);
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/model-list', array('bid' => $bid)));
	}

	/**
	 * 修改型号
	 */
	public function modifyModelAction()
	{
		$bid = $this->_request->getParam('bid');
		$id = $this->_request->getParam('id');
		$model = array(
			'label' => $this->_request->getParam('label'),
			'sort' => $this->_request->getParam('sort')
		);
		if ($id && $model) {
			Factory::getLogic('system/tlib')->modifyModel($id, $model);
			Factory::getLogic('cache')->clearCache('models-' . $bid);
		}
		$this->redirect(Factory::getUrl('monitor/system_tlib/model-list', array('bid' => $bid)));
	}

	/**
	 * 改变平板型号状态
	 */
	public function statusModelAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			Factory::getLogic('system/tlib')->statusModel($id, $status);
			Factory::getLogic('cache')->clearCache('models', true);
			$msg = Factory::getMsg();
			if (empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 平板型号详细页面
	 */
	public function modelAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			$this->loadLayout()->renderLayout();
		} else {
			$this->redirect(Factory::getUrl('monitor/system_tlib/brand-list'));
		}
	}

	/**
	 * 保存平板型号基本信息
	 */
	public function saveModelBaseInfoAction()
	{
		$id = $this->_request->getParam('id', null);
		$model = $this->_request->getParam('model', null);
		if ($id && $model) {
			$result = Factory::getResource('system/tlib/model')->modify(
				array('id = ?' => $id), $model
			);
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
	 * 保存平板型号参数信息
	 */
	public function saveModelParamsAction()
	{
		$id = $this->_request->getParam('id', null);
		$params = $this->_request->getParam('params', null);
		if ($id && $params) {
			$mLogic = Factory::getLogic('system/tlib');
			$result = $mLogic->modifyModel(
				$id, array('params' => serialize($params))
			);
			if ($result) {
				$mLogic->removeModelAttributeIndex($id);
				$index = array(
					'attribute_id' => array('1', '2', '3', '4', '5', '6', '9', '10', '11'),
					'value_id' => array(
						$params['screen_resolution'], 
						$params['screen_size'], 
						$params['front_camera'], 
						$params['back_camera'], 
						$params['cell_capacity'], 
						$params['os'], 
						$params['core_number'], 
						$params['ram'], 
						$params['expansion']
					),
					'model_id' => array($id, $id, $id, $id, $id, $id, $id, $id, $id)
				);
				$mLogic->createModelAttributeIndex($index);
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
	 * 设置平板默认封面
	 */
	public function saveModelImgAction()
	{
		$id = $this->_request->getParam('id', null);
		$thumb = $this->_request->getParam('thumb', null);
		$gallery = $this->_request->getParam('gallery', null);
		if ($id && $thumb && $gallery) {
			$result = Factory::getLogic('system/tlib')->modifyModel(
				$id, array('thumb' => $thumb, 'gallery' => $gallery)
			);
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
}
