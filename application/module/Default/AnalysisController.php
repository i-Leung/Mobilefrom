<?php
class Default_AnalysisController extends Project_Controller_Action_Front
{
	/**
	 * 页面导向统计
	 */
	public function pfromAction()
	{
		$module = $this->_request->getParam('module');
		$controller = $this->_request->getParam('controller');
		$action = $this->_request->getParam('action');
		$cfrom = $this->_request->getParam('cfrom');
		$date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$result = Factory::getLogic('analysis/pfrom')->markPageFrom($module, $controller, $action, $cfrom, $date);
		echo $result ? json_encode(1) : json_encode(0);
	}

	/**
	 * 统计使用QQ联系数量
	 */
	public function qqAction()
	{
		$uin = $this->_request->getParam('uin');
		$detail = $this->_request->getParam('detail');
		$redirect = 'http://wpa.qq.com/msgrd?v=3&uin=' . $uin . '&site=www.mobilefrom.com&menu=yes';
		$date = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		if ($detail == 'store') {
			$customer = Factory::getSession('customer/id');
			$store = $this->_request->getParam('store');
			if (!($customer && Factory::getView('store/item')->isMyStore($store, $customer))) {
				Factory::getLogic('store')->markVisitor(
					array(
						'store_id' => $store,
						'ip' => getIp(),
						'url' => 'store/markqq',
						'consult' => '1',
						'date' => $date
					)
				);
			}
		} else {
			Factory::getLogic('analysis/pfrom')->markPageFrom('customer', 'contact', 'qq', $detail, $date);
		}
		$this->redirect($redirect);
	}
}
