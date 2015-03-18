<?php
class Store_ItemController extends Project_Controller_Action_Front
{
	/**
	 * 店铺ID
	 */
	public $store_id = null;

	public function init()
	{
		parent::init();
		$this->store_id = $this->_request->getParam('store', null);
		if (!$this->store_id) {
			$this->redirect(Factory::getUrl('store/list', array('cfrom' => 'store')));
		}
	}

	/**
	 * 店铺首页
	 */
	public function indexAction()
	{
		$this->loadLayout('store')->renderLayout();
	}

	/**
	 * ajax获取店铺在售型号
	 */
	public function ajaxMobileListAction()
	{
		if ($this->store_id) {
			if ($result = Factory::getView('store/item')->getSellingMobileModelList($this->store_id, 6)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 在售型号列表
	 */
	public function mobileListAction()
	{
		if ($sort = $this->_request->getParam('sort', null)) {
			Factory::setSession('store-sort-' . $this->store_id, $sort);
		}
		if ($filter = $this->_request->getParam('filter', null)) {
			foreach ($filter as $key => $value) {
				if ($value == 'all') {
					Factory::unsetSession('store-filter-' . $this->store_id . '/' . $key);
				} else {
					Factory::setSession('store-filter-' . $this->store_id . '/' . $key, $value);
				}
			}
		}
		$this->loadLayout('store')->renderLayout();
	}

	/**
	 * 在售型号详情
	 */
	public function mobileInfoAction()
	{
		$this->loadLayout('store')->renderLayout();
	}

	/**
	 * 报价单
	 */
	public function mobileSheetAction()
	{
		$this->loadLayout('store')->renderLayout();
	}
	
	/**
	 * ajax获取店铺在售型号
	 */
	public function ajaxTabletListAction()
	{
		if ($this->store_id) {
			if ($result = Factory::getView('store/item')->getSellingTabletModelList($this->store_id, 6)) {
				echo json_encode($result);
				exit(0);
			}
		}
		echo json_encode(0);
	}
	
	/**
	 * 在售型号列表
	 */
	public function tabletListAction()
	{
		if ($sort = $this->_request->getParam('sort', null)) {
			Factory::setSession('store-tsort-' . $this->store_id, $sort);
		}
		if ($filter = $this->_request->getParam('filter', null)) {
			foreach ($filter as $key => $value) {
				if ($value == 'all') {
					Factory::unsetSession('store-tfilter-' . $this->store_id . '/' . $key);
				} else {
					Factory::setSession('store-tfilter-' . $this->store_id . '/' . $key, $value);
				}
			}
		}
		$this->loadLayout('store')->renderLayout();
	}
	
	/**
	 * 在售型号详情
	 */
	public function tabletInfoAction()
	{
		$this->loadLayout('store')->renderLayout();
	}
	
	/**
	 * 关于本店
	 */
	public function aboutAction()
	{
		$this->loadLayout('store')->renderLayout();
	}

	/**
	 * 店铺地图
	 */
	public function mapAction()
	{
		$this->loadLayout()->renderLayout();
	}

	/**
	 * 店铺服务
	 */
	public function serviceAction()
	{
		$this->loadLayout('store')->renderLayout();
	}
	
	/**
	 * 店铺评价
	 */
	public function commentAction()
	{
		$this->loadLayout('store')->renderLayout();
	}

	/**
	 * 记录店铺浏览量
	 */
	public function clicksAction()
	{
		$store = $this->_request->getParam('store', null);
		$url = $this->_request->getParam('url', null);
		$customer = Factory::getSession('customer/id');
		$view = Factory::getView('store/item');
		if ($customer && $view->isMyStore($store, $customer)) {
			exit(0);
		}
		$sLogic = Factory::getLogic('store');
		$sLogic->modifyStore($store, array('clicks' => '`clicks` + 1'));
		$sLogic->markVisitor(
			array(
				'store_id' => $store,
				'ip' => getIp(),
				'url' => $url,
				'date' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
			)
		);
		if ($model = $this->_request->getParam('model', null)) {
			$model_id = $view->getMlibModelId($model);
			Factory::getLogic('system/mlib')->modifyModel($model_id, array('clicks' => '`clicks` + 1'));
		}
	}

	/**
	 * 店铺留言
	 */
	public function feedbackAction()
	{
		$this->loadLayout('store')->renderLayout();
	}
}
