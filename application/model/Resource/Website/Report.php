<?php
class Resource_Website_Report extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'website_report';
	}

	/**
	 * 获取举报信息列表
	 * @param 举报类别ID $cid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadReportList($cid, $page, $per)
	{
		$where = ' where a.status = 1';
		$params = array();
		if ($cid) {
			$params = array(':category_id' => $cid);
			$where = ' where a.category_id = :category_id and a.status = 1';
		}
		$sql = 'select a.id, b.username as reporter, a.title, a.thumb, a.summary, a.created_at 
			from website_report as a 
			left join customer as b on b.id = a.reporter_id 
			' . $where . '
			order by a.id desc 
			limit ' . $per . ' offset ' . ($page - 1) * $per;
		return $this->fetchAll(
				$this->select($sql, $params)
		);
	}

	/**
	 * 获取有图片的举报信息列表
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadThumbnailReportList($page = 1, $per = 5)
	{
		$sql = 'select id, title, thumb 
				from website_report 
				where thumb <> "" and status = 1 
				order by id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		return $this->fetchAll(
				$this->select($sql)
		);
	}

	/**
	 * 获取举报信息列表总数
	 * @param 举报类别ID $cid
	 */
	public function getReportTotal($cid)
	{
		$params = array();
		$sql = '';
		if ($cid) {
			$params = array(':category_id' => $cid);
			$sql = 'select count(id) as total from website_report where category_id = :category_id and status = 1';
		} else {
			$sql = 'select count(id) as total from website_report where status = 1';
		}
		return $this->fetchOne(
				$this->select($sql, $params)
		);
	}

	/**
	 * 根据QQ\电话搜索举报信息
	 * @param 搜索号码 $number
	 * @param 是否需要用户搜索 $customer
	 */
	public function loadReportListBySearch($number, $customer = 0)
	{
		$params = array(':number' => $number, ':customer' => $customer);
		$sql = 'select a.id, b.username as reporter, a.title, a.thumb, a.summary, a.created_at 
				from website_report as a 
				left join customer as b on b.id = a.reporter_id 
				left join website_report_search as c on c.rid = a.id 
				where (c.number = :number and (c.type = 2 or c.type = 3)) or (c.number = :customer and c.type = 1) and a.status = 1 
				order by a.id desc';
		return $this->fetchAll(
				$this->select($sql, $params)
		);
	}

	/**
	 * 判断用户是否被举报
	 * @param 用户ID $customer_id
	 * @param QQ $qq
	 * @param 电话 $tel
	 */
	public function isReported($customer_id, $qq, $tel)
	{
		$where = $params = array();
		if ($customer_id) {
			$where[] = 'type = :ctype and number = :customer_id';
			$params[':ctype'] = '1';
			$params[':customer_id'] = $customer_id;
		}
		if ($qq) {
			$where[] = 'type = :qtype and number = :qq';
			$params[':qtype'] = '2';
			$params[':qq'] = $qq;
		}
		if ($tel) {
			$where[] = 'type = :ttype and number = :tel';
			$params[':ttype'] = '3';
			$params[':tel'] = $tel;
		}
		$where[] = 'status = 1';
		if ($where) {
			$where = 'where (' . implode(') or (', $where) . ')';
			$sql = 'select id from website_report ' . $where;
			return $this->fetchAll(
					$this->select($sql, $params)
			);
		}
		return array();
	}

	/**
	 * 获取指定ID举报信息列表
	 * @param 举报信息ID集合 $ids
	 */
	public function loadReportListByIds($ids)
	{
		$ids = implode(',', $ids);
		$sql = 'select a.id, b.username as reporter, a.title, a.summary, a.created_at 
				from website_report as a 
				left join customer as b on b.id = a.reporter_id 
				where a.status = 1 
				order by a.id desc 
				where id in (' . $ids . ')';
		return $this->fetchAll($this->select($sql));
	}

	/**
	 * 获取用户发布举报信息列表
	 * @param 用户ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMyReportList($id, $page, $per)
	{
		$sql = 'select id, category_id, title, status 
				from website_report 
				where reporter_id = :id 
				order by id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		return $this->fetchAll(
				$this->select($sql, array(':id' => $id))
		);
	}

	/**
	 * 获取用户发布举报信息总数
	 * @param 用户ID $id
	 */
	public function getMyReportListTotal($id)
	{
		$sql = 'select count(id) from website_report where reporter_id = :id';
		return $this->fetchOne(
				$this->select($sql, array(':id' => $id))
		);
	}

	/**
	 * 获取指定举报信息资料
	 * @param 举报信息ID $id
	 */
	public function getReportDetail($id)
	{
		$sql = 'select a.id, a.reporter_id, b.username as reporter, b.group as greporter, b.registered_at as reg_reporter, b.logon_times as lg_reporter, 
				a.informer_id, c.username as informer, c.group as ginformer, c.registered_at as reg_informer, c.logon_times as lg_informer, 
				a.title, a.content, a.gallery, a.clicks, a.comments, a.reporter_point, a.informer_point, a.created_at 
				from website_report as a 
				left join customer as b on b.id = a.reporter_id 
				left join customer as c on c.id = a.informer_id 
				where a.id = :id 
				limit 1';
		return $this->fetchRow(
			$this->select($sql, array(':id' => $id))
		);
	}

	/**
	 * 获取指定举报信息进行编辑
	 * @param 信息ID $id
	 * @param 举报人ID $customer_id
	 */
	public function getReportByEdit($id, $customer_id)
	{
		$sql = 'select a.id, a.category_id, a.informer_id, b.username as informer, a.title, a.content, a.thumb, a.gallery 
				from website_report as a 
				left join customer as b on b.id = a.informer_id 
				where a.id = :id and a.reporter_id = :customer 
				limit 1';
		return $this->fetchRow(
			$this->select($sql, array(':id' => $id, ':customer' => $customer_id))
		);
	}

	/**
	 * 获取前后举报信息
	 * @param 举报信息ID $id
	 */
	public function getReportPrevNext($id)
	{
		$sql = 'select id, title from website_report where id < :prev limit 1 
				union 
				select id, title from website_report where id > :next limit 1';
		return $this->fetchAll(
			$this->select($sql, array(':prev' => $id, ':next' => $id))
		);
	}

	/**
	 * 获取举报信息标题
	 * @param 举报信息ID $id
	 */
	public function getReportTitle($id)
	{
		$sql = 'select title from website_report where id = :id limit 1';
		return $this->fetchOne(
			$this->select($sql, array(':id' => $id))
		);
	}

	/**
	 * 后台获取举报信息列表
	 * @param 信息类型 $cid
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadAdminReportList($cid = null, $page = 1, $per = 20)
	{
		$where = '';
		$params = array();
		if ($cid) {
			$where = ' where category_id = :cid ';
			$params = array(':cid' => $cid);
		}
		$sql = 'select id, title, clicks, created_at, status 
				from website_report 
				' . $where . '
				order by id desc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		return $this->fetchAll(
				$this->select($sql, $params)
		);
	}

	/**
	 * 获取后台举报信息总数
	 * @param 信息类型 $cid
	 */
	public function getAdminReportTotal($cid = null)
	{
		$where = '';
		$params = array();
		if ($cid) {
			$where = ' where category_id = :cid ';
			$params = array(':cid' => $cid);
		}
		$sql = 'select count(id) from website_report ' . $where;
		return $this->fetchOne(
				$this->select($sql, $params)
		);
	}
}
