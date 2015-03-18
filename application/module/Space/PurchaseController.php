<?php
class Space_PurchaseController extends Project_Controller_Action_Front
{
	public function listAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * 详细页TA还发布了内容
	 */
	public function otherAction()
	{
		$customer_id = $this->_request->getParam('cid', null);
		$purchase_id = $this->_request->getParam('pid', null);
		if (!is_null($customer_id) && !is_null($purchase_id)) {
			$purchase = Factory::getView('purchase/list')->loadCustomerOther($customer_id, $purchase_id);
			$msg = Factory::getMsg();
			if (empty($msg) && $purchase) {
				echo json_encode($purchase);
				exit(0);
			}
		}
		echo json_encode(0);
	}
}
