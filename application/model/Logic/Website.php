<?php
class Logic_Website
{
	/*****************************************************************网站建议*************************************************************/
	/**
	 * 提交网站建议
	 * @param 联系QQ $qq
	 * @param 建议内容 $content
	 */
	public function makeSuggestion($qq, $content)
	{
		Factory::getResource('website/suggestion')->create(
			array(
				'qq' => $qq,
				'content' => $content,
				'browser' => $this->getBrowser(),
				'created_at' => date('Y-m-d')
			)
		);
	}

	/**
	 * 获取用户浏览器版本
	 */
	public function getBrowser()
	{
		$sys = $_SERVER['HTTP_USER_AGENT'];
		$exp = '';
		if (stripos($sys, "NetCaptor") > 0) {
			$exp = "NetCaptor";
		} elseif (stripos($sys, "Firefox/") > 0) {
			preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
			$exp = "Mozilla Firefox ".$b[1];
		} elseif(stripos($sys, "MAXTHON") > 0) {
			preg_match("/MAXTHON\s+([^;)]+)+/i", $sys, $b);
			preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
			$exp = $b[0]." (IE".$ie[1].")";
		} elseif(stripos($sys, "MSIE") > 0) {
			preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
			$exp = "Internet Explorer ".$ie[1];
		} elseif(stripos($sys, "Netscape") > 0) {
			$exp = "Netscape";
		} elseif(stripos($sys, "Opera") > 0) {
			$exp = "Opera";
		} elseif (stripos($sys, "Chrome") > 0) {
			$exp = "Chrome";
		} else {
			$exp = "未知浏览器";
		}
		return $exp;
	}

	/*****************************************************************举报信息*************************************************************/
	/**
	 * 创建举报信息
	 * @param 举报信息 $data
	 */
	public function createReport($data)
	{
		$resource = Factory::getResource('website/report');
		if ($resource->create($data)) {
			return $resource->getNewId();
		}
		return false;
	}

	/**
	 * 修改举报信息
	 */
	public function modifyReport($id, $params)
	{
		return Factory::getResource('website/report')->modify(
			array('id = ?' => $id), $params
		);
	}

	/**
	 * 举报搜索资料入库
	 * @param 举报搜索资料 $data
	 */
	public function createReportSearch($data)
	{
		return Factory::getResource('website/report/search')->create($data, 'multiple');
	}

	/**
	 * 移除所有相关举报搜索信息
	 * @param 举报信息ID $id
	 */
	public function removeReportSearch($id)
	{
		return Factory::getResource('website/report/search')->remove(
			array('rid = ?' => $id)
		);
	}

	/**
	 * 评论举报信息
	 * @param 评论内容 $data
	 */
	public function createReportComment($data)
	{
		$resource = Factory::getResource('website/report/comment');
		if ($resource->create($data)) {
			return $resource->getNewId();
		}
		return false;
	}

	/**
	 * 检测卖家是否被举报
	 * @param 卖家ID $customer_id
	 * @param 联系电话 $tel
	 * @param QQ $qq
	 */
	public function checkReported($customer_id, $tel, $qq)
	{
		$customer_id = is_numeric($customer_id) ? $customer_id : 0;
		$tel = is_numeric($tel) ? $tel : 0;
		$qq = is_numeric($qq) ? $qq : 0;
		return Factory::getResource('website/report/search')->checkReported($customer_id, $tel, $qq);
	}
}
