<?php
class Tablet_ItemView extends Project_View_Block_Abstract
{
	private $_src = 'http://mobilefrom-upload.stor.sinaapp.com/tablet/';
	
	/**
	 * 获取图片上传url
	 */
	public function getUploadImgUrl()
	{
		return Factory::getUrl('plugin/swfupload/upload-tablet-img');
	}
	
	/**
	 * 手机详细页显示信息
	 * @param 手机信息ID $id
	 * @return false或手机信息详细
	 */
	public function showInfo($id)
	{
		$info = Factory::getResource('tablet')->showInfo($id);
		if ($info) {
			if ($info['gallery']) {
				$info['gallery'] = substr($info['gallery'], 0, -1);
				$info['gallery'] = explode(';', $info['gallery']);
			} else {
				$info['gallery'] = array();
			}
			$info['data'] = unserialize($info['data']);
			if ($info['data']['brand'] != '15') {
				$models = Factory::getView('system/tlib')->loadModelListById($info['data']['brand']);
				$info['model'] = $info['data']['model'];
				$info['data']['model'] = $models[$info['data']['model']];
			}
			$brands = Factory::getView('system/tlib')->loadBrandList();
			$info['brand'] = $info['data']['brand'];
			$info['data']['brand'] = $brands[$info['data']['brand']];
		}
		return $info;
	}
	
	/**
	 * 获取手机概述信息
	 * @param 手机信息ID $id
	 * @return false或手机概述详细
	 */
	public function getSummarize($id)
	{
		return Factory::getResource('tablet')->getSummarize($id);
	}
	
	/**
	 * 手机详细页显示联系信息
	 * @param 手机信息ID $id
	 * @return false或手机联系信息
	 */
	public function showContact($id)
	{
		$info = Factory::getResource('tablet')->showContact($id);
		if ($info) {
			$info['contact'] = unserialize($info['contact']);
			//获取手机归属地
			if (is_numeric($info['contact']['tel']) && (strlen($info['contact']['tel']) == 11)) {
				$url = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile=' . $info['contact']['tel'];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
				$xml = simplexml_load_string(curl_exec($ch));
				curl_close($ch);				
				$info['contact']['ownership'] = trim($xml->city);
			}
		}
		return $info;
	}
	
	/**
	 * 获取手机咨询列表
	 * @param 手机信息ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或咨询列表
	 */
	public function loadTabletConsult($id, $page = 1, $per = 10)
	{
		return Factory::getResource('msg/consult')->loadTopicConsult(1, $id, $page, $per);
	}
	
	/**
	 * 获取手机咨询总量
	 * @param 手机信息ID $id
	 * @return false或咨询总量
	 */
	public function getTabletConsultTotal($id)
	{
		return Factory::getResource('msg/consult')->getTopicConsultTotal(1, $id);
	}
	
	/**
	 * 获取手机刷新信息
	 * @param 手机信息ID $id
	 */
	public function getLastRefreshedAt($id)
	{
		return Factory::getResource('tablet')->getLastRefreshedAt($id);
	}
	
	/**
	 * 获取缩略图
	 * @param 图片路径 $pic
	 * @return string
	 */
	public function getThumbPicture($pic)
	{
		return $this->_src . 'thumb/' . $pic;
	}
	
	/**
	 * 获取大图
	 * @param 图片路径 $pic
	 * @return string
	 */
	public function getOriginalPicture($pic)
	{
		return $this->_src . $pic;
	}

	/**
	 * 获取闲置手机型号ID
	 * @param 手机ID $id
	 */
	public function getTabletModelId($id)
	{
		return Factory::getResource('relation/tablet/filter')->getTabletModelId($id);
	}
}
