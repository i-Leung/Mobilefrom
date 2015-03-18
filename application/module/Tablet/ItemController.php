<?php
class Tablet_ItemController extends Project_Controller_Action_Front
{
	/**
	 * 手机详情
	 */
	public function indexAction()
	{
		$id = $this->_request->getParam('id');
		if ($id && Factory::getLogic('tablet')->isActiveTablet($id)) {
			$this->loadLayout();
			$this->renderLayout();
		} else {
			notfound();
		}
	}
	
	/**
	 * 记录点击量
	 */
	public function clickAction()
	{
		$id = $this->_request->getParam('id');
		
		//记录浏览历史
		if ($thistory = Factory::getCookie('thistory')) {//判断是否有浏览记录
			$thistory = unserialize($thistory);
			if (!in_array($id, $thistory)) {
				$thistory = $this->buildLimitArray($thistory, $id);				
			}
		} else {
			$thistory = array($id);
		}
		Factory::setCookie('thistory', serialize($thistory));

		//记录点击量
		if ($id && !in_array(Factory::getCookie('customer_id'), array(1, 2, 10))) {
			Factory::getLogic('tablet')->markClick($id);
			$model = Factory::getView('tablet/item')->getTabletModelId($id);
			if (strlen($model) > 2) {
				Factory::getLogic('system/tlib')->modifyModel($model, array('clicks' => '`clicks` + 1'));
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
	 * 获取手机咨询
	 */
	public function consultAction()
	{
		$id = $this->_request->getParam('id');
		$page = $this->_request->getParam('p');
		if ($id && $page) {
			$consults = Factory::getView('tablet/item')->loadTabletConsult($id, $page);
			$msg = Factory::getMsg();
			if (empty($msg) && $consults){
				$consults['asked_at'] = date('Y-m-d H:i:s', $consults['asked_at']);
				$consults['answered_at'] = date('Y-m-d H:i:s', $consults['answered_at']);
				echo json_encode($consults);
				exit(0);
			}
		}
		echo json_encode(0);
	}

	/**
	 * 构建限制容量数组，前出后进
	 * @param 目标数组 $array
	 * @param 后进元素 $last
	 * @param 数组大小 $num
	 */
	public function buildLimitArray($array, $last, $num = 3)
	{
		if (count($array) >= $num) {
			array_shift($array);
		}
		array_push($array, $last);
		return $array;
	}

	/**
	 * 获取品牌下型号集合
	 */
	public function modelListAction()
	{
		$bid = $this->_request->getParam('bid');
		if ($bid) {
			if ($models = Factory::getView('system/tlib')->loadModelListById($bid)) {
				$msg = Factory::getMsg();
				if (empty($msg)) {
					echo json_encode($models);
					exit(0);
				}
			}
		}
		echo json_encode(0);
	}
}
