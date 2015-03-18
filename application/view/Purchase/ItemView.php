<?php
class Purchase_ItemView extends Project_View_Block_Abstract
{
	/**
	 * 求购详细页显示信息
	 * @param 求购信息ID $id
	 * @return false或求购信息详细
	 */
	public function showInfo($id)
	{
		$info = Factory::getResource('purchase')->showInfo($id);
		$brands = Factory::getView('system/mlib')->loadBrandList();
		if (strlen($info['brand']) == 1) {
			$info['brand'] = $brands['0' . $info['brand']];
		} else {
			$info['brand'] = $brands[$info['brand']];
		}
		return $info;
	}
	
	/**
	 * 获取求购概述信息
	 * @param 求购信息ID $id
	 * @return false或求购概述详细
	 */
	public function getSummarize($id)
	{
		return Factory::getResource('purchase')->getSummarize($id);
	}
	
	/**
	 * 求购详细页显示联系信息
	 * @param 求购信息ID $id
	 * @return false或求购联系信息
	 */
	public function showContact($id)
	{
		$info = Factory::getResource('purchase')->showContact($id);
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
	 * 获取求购咨询列表
	 * @param 求购信息ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 * @return false或咨询列表
	 */
	public function loadPurchaseConsult($id, $page = 1, $per = 10)
	{
		return Factory::getResource('msg/consult')->loadTopicConsult(2, $id, $page, $per);
	}
	
	/**
	 * 获取求购咨询总量
	 * @param 求购信息ID $id
	 * @return false或咨询总量
	 */
	public function getPurchaseConsultTotal($id)
	{
		return Factory::getResource('msg/consult')->getTopicConsultTotal(2, $id);
	}
	
	/**
	 * 获取求购刷新信息
	 * @param 求购信息ID $id
	 */
	public function getLastRefreshedAt($id)
	{
		return Factory::getResource('purchase')->getLastRefreshedAt($id);
	}
}
