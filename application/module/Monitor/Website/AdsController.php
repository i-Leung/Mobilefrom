<?php
class Monitor_Website_AdsController extends Project_Controller_Action_Admin
{
	/**
	 * 首页大图控制
	 */
	public function indexGalleryAction()
	{
		$this->loadLayout();
		$this->renderLayout();
		echo '首页大图控制';
	}
}