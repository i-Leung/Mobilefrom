<?php
class Website_ReportView extends Project_View_Block_Abstract
{
	private $_src = 'http://mobilefrom-upload.stor.sinaapp.com/report/';
	
	/**
	 * 获取图片上传url
	 */
	public function getUploadImgUrl()
	{
		return Factory::getUrl('plugin/swfupload/upload-report');
	}

	/**
	 * 获取缩略图
	 * @param 图片路径 $pic
	 * @return string
	 */
	public function getThumbPicture($pic)
	{
		return $pic ? $this->_src . 'thumb/' . $pic : $this->getImg('mf.png');
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

	/*****************************************************************举报信息*************************************************************/
	/**
	 * 获取举报信息类型
	 * @param 类型ID $id
	 */
	public function getReportCategory($id = null)
	{
		$category = array(
			'1' => '炸弹佬', 
			'2' => '飞机师', 
			'3' => '虚假信息', 
			'-1' => '其它'
		);
		if ($id) {
			return $category[$id];
		}
		return $category;
	}

	/**
	 * 获取搜索号码类型
	 * @param 类型ID $id
	 */
	public function getNumberType($id = null)
	{
		$type = array(
			'1' => '用户ID', 
			'2' => 'QQ号码', 
			'3' => '联系号码'
		);
		if ($id) {
			return $type[$id];
		}
		return $type;
	}

	/**
	 * 获取举报信息列表
	 * @param 举报类别ID $cid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadReportList($cid = null, $page = 1, $per = 10)
	{
		return Factory::getResource('website/report')->loadReportList($cid, $page, $per);
	}

	/**
	 * 获取有图片的举报信息列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadThumbnailReportList($page = 1, $per = 5)
	{
		return Factory::getResource('website/report')->loadThumbnailReportList($page = 1, $per = 5);
	}

	/**
	 * 获取举报信息列表总数
	 * @param 举报类别ID $cid
	 */
	public function getReportTotal($cid)
	{
		return Factory::getResource('website/report')->getReportTotal($cid);
	}

	/**
	 * 根据QQ\电话搜索举报信息
	 * @param 搜索内容 $search
	 */
	public function loadReportListBySearch($search)
	{
		$customer = Factory::getLogic('customer')->isExist($search);
		$customer = $customer ? $customer : 0;
		return Factory::getResource('website/report')->loadReportListBySearch($search, $customer);
	}

	/**
	 * 获取举报联系信息总数
	 */
	public function getReportSearchTotal()
	{
		return Factory::getResource('website/report/search')->getReportSearchTotal();
	}

	/**
	 * 第三方搜索结果
	 * @param 搜索内容 $search
	 */
	public function loadReportSearchByThirdParty($search)
	{
		$url = 'http://www.soso.com/q?w=' . $search . '&site=shunderen.com&ofmt=xml&pg=1';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//不直接输出，返回到变量
		$xml = curl_exec($ch);
		$xml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
		curl_close($ch);				
		$result = array();
		foreach ($xml->Result_List[0] as $item) {
			$result[] = array(
				'title' => (string)$item->Ptitle,
				'url' => (string)$item->Purl,
				'content' => (string)$item->Pcontent,
				'created_at' => (string)$item->Pdate
			);
		}
		return $result;
	}

	/**
	 * 判断用户是否被举报
	 * @param 用户ID $customer_id
	 * @param QQ $qq
	 * @param 电话 $tel
	 */
	public function isReported($customer_id, $qq, $tel)
	{
		return Factory::getResource('website/report')->isReported($customer_id, $qq, $tel);
	}

	/**
	 * 获取指定ID举报信息列表
	 * @param 举报信息ID集合 $ids
	 */
	public function loadReportListByIds($ids)
	{
		return Factory::getResource('website/report')->loadReportListByIds($ids);
	}

	/**
	 * 获取用户发布举报信息列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMyReportList($page = 1, $per = 10)
	{
		return Factory::getResource('website/report')->loadMyReportList(
			Factory::getSession('customer/id'), $page, $per
		);
	}

	/**
	 * 获取用户发布举报信息总数
	 */
	public function getMyReportListTotal()
	{
		return Factory::getResource('website/report')->getMyReportListTotal(
			Factory::getSession('customer/id')
		);
	}

	/**
	 * 获取指定举报信息资料
	 * @param 举报信息ID $id
	 */
	public function getReportDetail($id)
	{
		$detail = Factory::getResource('website/report')->getReportDetail($id);
		$detail['content'] = htmlspecialchars_decode($detail['content']);
		$detail['gallery'] = $detail['gallery'] ? explode(';', substr($detail['gallery'], 0, -1)) : null;
		return $detail;
	}

	/**
	 * 获取指定举报信息进行编辑
	 * @param 信息ID $id
	 * @param 举报人ID $customer_id
	 */
	public function getReportByEdit($id, $customer_id)
	{
		if ($report = Factory::getResource('website/report')->getReportByEdit($id, $customer_id)) {
			$report['content'] = htmlspecialchars_decode($report['content']);
			$report['gallery'] = $report['gallery'] ? explode(';', substr($report['gallery'], 0, -1)) : '';
			return $report;
		}
		return false;
	}

	/**
	 * 获取指定举报信息的联系信息进行编辑
	 * @param 信息ID $id
	 */
	public function getReportContactByEdit($id)
	{
		return Factory::getResource('website/report/search')->getReportContactByEdit($id);
	}

	/**
	 * 获取前后举报信息
	 * @param 举报信息ID $id
	 */
	public function getReportPrevNext($id)
	{
		return Factory::getResource('website/report')->getReportPrevNext($id);
	}

	/**
	 * 获取举报信息标题
	 * @param 举报信息ID $id
	 */
	public function getReportTitle($id)
	{
		return Factory::getResource('website/report')->getReportTitle($id);
	}

	/**
	 * 获取被举报人联系信息
	 * @param 举报主题ID $tid
	 * @param 举报来源 $cfrom
	 */
	public function getInformerContact($tid, $cfrom)
	{
		$result = false;
		switch ($cfrom) {
			case 'mobile':
				$result = Factory::getResource('mobile')->showContact($tid);
				break;
			case 'purchase':
				$result = Factory::getResource('purchase')->showContact($tid);
				break;
		}
		if ($result) {
			$result['username'] = $result['customer_id'] == '2' ? '' : $result['username'];
			$result['contact'] = unserialize($result['contact']);
			$result['tel'] = is_numeric($result['contact']['tel']) ? $result['contact']['tel'] : '';
			$result['qq'] = is_numeric($result['contact']['qq']) ? $result['contact']['qq'] : '';
		}
		return $result;
	}

	/*****************************************************************举报信息评论*************************************************************/
	/**
	 * @param 判断对象是否发表过举报言论 
	 * @param 举报信息ID $rid
	 * @param 目标对象ID $customer_id
	 */
	public function hasTargetComment($rid, $customer_id)
	{
		return Factory::getResource('website/report/comment')->hasTargetComment($rid, $customer_id);
	}

	/**
	 * 加载举报人、被举报人评论集合
	 * @param 举报信息ID $rid
	 * @param 目标对象ID $customer_id
	 */
	public function loadTargetCommentList($rid, $customer_id)
	{
		$result = array();
		if ($comments = Factory::getResource('website/report/comment')->loadTargetCommentList($rid, $customer_id)) {
			foreach ($comments as &$comment) {
				$comment['created_at'] = timeforcustomer($comment['created_at']);
				if ($comment['pid'] == '0') {
					$result[$comment['id']] = array(
						'self' => $comment,
						'children' => array()
					);
				} else {
					$result[$comment['pid']]['children'][] = $comment;
				}
			}
			return $result;
		}
		return $result;
	}

	/**
	 * 加载举报评论列表
	 * @param 举报ID $rid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadReportCommentList($rid, $page = 1, $per = 10)
	{
		$result = Factory::getResource('website/report/comment')->loadReportCommentList($rid, $page, $per);
		if ($result) {
			foreach ($result as &$item) {
				$item['children'] = $item['children'] ? $item['children'] : '0';
				$item['created_at'] = timeforcustomer($item['created_at']);
			}
		}
		return $result;
	}

	/**
	 * 加载举报评论子评论列表
	 * @param 举报父评论ID $pid
	 */
	public function loadReportCommentChildren($pid)
	{
		$result = Factory::getResource('website/report/comment')->loadReportCommentChildren($pid);
		if ($result) {
			foreach ($result as &$item) {
				$item['created_at'] = timeforcustomer($item['created_at']);
			}
		}
		return $result;
	}

	/**
	 * 获取举报信息评论对话记录
	 * @param 对话父ID $id
	 */
	public function showCommentDialog($id)
	{
		$result = Factory::getResource('website/report/comment')->showCommentDialog($id);
		if ($result) {
			foreach ($result as &$item) {
				$item['created_at'] = timeforcustomer($item['created_at']);
			}
		}
		return $result;
	}

	/**
	 * 后台获取举报信息列表
	 * @param 信息类型 $cid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadAdminReportList($cid = null, $page = 1, $per = 20)
	{
		return Factory::getResource('website/report')->loadAdminReportList($cid, $page, $per);
	}

	/**
	 * 获取后台举报信息总数
	 * @param 信息类型 $cid
	 */
	public function getAdminReportTotal($cid = null)
	{
		return Factory::getResource('website/report')->getAdminReportTotal($cid);
	}
}
