<?php
class Monitor_System_MlibController extends Project_Controller_Action_Admin
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
			Factory::getResource('system/mlib/attribute')->create(
				array('label' => $label, 'type' => $type)
			);
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/attribute-list'));
	}

	/**
	 * 添加属性值
	 */
	public function addAttributeValueAction()
	{
		$value = $this->_request->getParam('value');
		if ($value) {
			Factory::getResource('system/mlib/attribute/value')->create($value);
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/attribute', array('id' => $value['attribute_id'])));
	}

	/**
	 * 添加属性操作
	public function ajaxAddAttributeAction()
	{
		$attribute = $this->_request->getParam('attribute');	
		$values = $this->_request->getParam('values');	
		if ($attribute) {
			$logic = Factory::getLogic('system/mlib');
			$id = $logic->addAttribute($attribute);
			if ($id) {
				$vresult = true;
				if ($values) {
					$vresult = $this->addAttributeValue($logic, $id, $values);
				}
				$msg = Factory::getMsg();
				if (empty($msg) && $vresult) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
	 */

	/**
	 * 修改属性操作
	public function ajaxModifyAttributeAction()
	{
		$id = $this->_request->getParam('id');
		$attribute = $this->_request->getParam('attribute');	
		$values = $this->_request->getParam('values');	
		if ($id && $attribute) {
			$logic = Factory::getLogic('system/mlib');
			$aresult = $logic->modifyAttribute($id, $attribute);
			if ($aresult) {
				$vresult = true;
				if ($values) {
					$vresult = $this->addAttributeValue($logic, $id, $values);
				}
				$msg = Factory::getMsg();
				if (empty($msg) && $vresult) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
	 */

	/**
	 * 改变属性状态
	 */
	public function changeAttributeStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/mlib')->changeAttributeStatus($id, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/*********************************************属性值操作*********************************************/
	/**
	 * 添加属性值
	 * @param 逻辑模型对象 $logic
	 * @param 属性ID $id
	 * @param 属性数据 $data
	public function addAttributeValue($logic, $id, $values)
	{
		$vdata = array();
		foreach ($values['label'] as $key => $value) {
			$vdata['attribute_id'][] = $id;
			$vdata['label'][] = $value;
			$vdata['sort'][] = $values['sort'][$key];
		}
		if ($vdata) {
			return $logic->addAttributeValue($vdata);	
		}
		return false;
	}
	 */

	/**
	 * 修改属性值
	public function ajaxModifyAttributeValueAction()
	{
		$id = $this->_request->getParam('id');
		$value = $this->_request->getParam('value');
		if ($id && $value) {
			$result = Factory::getLogic('system/mlib')->modifyAttributeValue($id, $value);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	 */

	/**
	 * 修改属性值
	public function ajaxRemoveAttributeValueAction()
	{
		$id = $this->_request->getParam('id');
		if ($id) {
			$result = Factory::getLogic('system/mlib')->removeAttributeValue($id);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	 */

	/*********************************************属性组操作*********************************************/
	/**
	 * 属性组列表
	public function attributeGroupListAction()
	{
		$this->loadLayout()->renderLayout();
	}
	 */

	/**
	 * 属性组信息
	public function attributeGroupAction()
	{
		$this->loadLayout()->renderLayout();
	}
	 */

	/**
	 * 添加属性组
	public function addAttributeGroupAction()
	{
		$this->loadLayout()->renderLayout();
	}
	 */

	/**
	 * 添加属性组操作
	public function ajaxAddAttributeGroupAction()
	{
		$group = $this->_request->getParam('group');
		if ($group) {
			$logic = Factory::getLogic('system/mlib');
			if ($group_id = $logic->addAttributeGroup($group)) {
				$result = true;
				if ($attributes = $this->_request->getParam('attributes')) {
					$result = $logic->addGroupAttribute($group_id, $attributes);
				}	
				$msg = Factory::getMsg();
				if (empty($msg) && $result) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
	 */

	/**
	 * 修改属性组操作
	public function ajaxModifyAttributeGroupAction()
	{
		$id = $this->_request->getParam('id');
		$group = $this->_request->getParam('group');
		if ($id && $group) {
			$logic = Factory::getLogic('system/mlib');
			if ($logic->modifyAttributeGroup($id, $group)) {
				$result = true;
				$before = $logic->loadGroupAttributeList($id);
				$after = $this->_request->getParam('attributes', array());
				$diff = $logic->getDiffGroupAttribute($before, $after);
				if ($diff['add']) {
					$result = $result && $logic->addGroupAttribute($id, $diff['add']);
				}
				if ($diff['delete']) {
					$result = $result && $logic->removeGroupAttribute($id, $diff['delete']);
				}
				$msg = Factory::getMsg();
				if (empty($msg) && $result) {
					echo json_encode(1);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
	 */

	/**
	 * 改变属性组状态
	public function changeAttributeGroupStatusAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			$result = Factory::getLogic('system/mlib')->changeAttributeGroupStatus($id, $status);
			$msg = Factory::getMsg();
			if ($result && empty($msg)) {
				echo json_encode(1);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	 */

	/*********************************************手机操作*********************************************/
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
			Factory::getLogic('system/mlib')->createBrand(array('label' => $brand));
			Factory::getLogic('cache')->clearCache('brands');
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/brand-list'));
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
			Factory::getLogic('system/mlib')->modifyBrand($id, $brand);
			Factory::getLogic('cache')->clearCache('brands');
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/brand-list'));
	}

	/**
	 * 改变手机品牌状态
	 */
	public function statusBrandAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			Factory::getLogic('system/mlib')->statusBrand($id, $status);
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
			Factory::getLogic('system/mlib')->createModel(array('bid' => $bid, 'label' => $model));
			Factory::getLogic('cache')->clearCache('models-' . $bid);
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/model-list', array('bid' => $bid)));
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
			Factory::getLogic('system/mlib')->modifyModel($id, $model);
			Factory::getLogic('cache')->clearCache('models-' . $bid);
		}
		$this->redirect(Factory::getUrl('monitor/system_mlib/model-list', array('bid' => $bid)));
	}

	/**
	 * 改变手机型号状态
	 */
	public function statusModelAction()
	{
		$id = $this->_request->getParam('id');
		$status = $this->_request->getParam('status');
		if ($id) {
			Factory::getLogic('system/mlib')->statusModel($id, $status);
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
	 * 手机型号详细页面
	 */
	public function modelAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			$this->loadLayout()->renderLayout();
		} else {
			$this->redirect(Factory::getUrl('monitor/system_mlib/brand-list'));
		}
	}

	/**
	 * 保存手机型号基本信息
	 */
	public function saveModelBaseInfoAction()
	{
		$id = $this->_request->getParam('id', null);
		$model = $this->_request->getParam('model', null);
		if ($id && $model) {
			$result = Factory::getResource('system/mlib/model')->modify(
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
	 * 保存手机型号参数信息
	 */
	public function saveModelParamsAction()
	{
		$id = $this->_request->getParam('id', null);
		$params = $this->_request->getParam('params', null);
		if ($id && $params) {
			$mLogic = Factory::getLogic('system/mlib');
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
	 * 设置手机默认封面
	 */
	public function saveModelImgAction()
	{
		$id = $this->_request->getParam('id', null);
		$thumb = $this->_request->getParam('thumb', null);
		$gallery = $this->_request->getParam('gallery', null);
		if ($id && $thumb && $gallery) {
			$result = Factory::getLogic('system/mlib')->modifyModel(
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
