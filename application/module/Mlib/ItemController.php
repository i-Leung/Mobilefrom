<?php
class Mlib_ItemController extends Project_Controller_Action_Front
{
	/**
	 * 型号详情
	 */
	public function indexAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 获取手机参数占比
	 */
	public function dataPercentAction()
	{
		$data = $this->_request->getParam('data', null);
		if ($data) {
			if ($percent = Factory::getView('mlib/item')->getModelDataPercent($data)) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($percent);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 获取手机示例图
	 */
	public function galleryAction()
	{
		$id = $this->_request->getParam('id', null);
		if ($id) {
			if ($gallery = Factory::getView('mlib/item')->getModelGallery($id)) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($gallery);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}

	/**
	 * 记录浏览量
	 */
	public function clicksAction()
	{
		if ($id = $this->_request->getParam('id', null)) {
			Factory::getLogic('system/mlib')->modifyModel($id, array('clicks' => '`clicks` + 1'));
		}
	}
}
