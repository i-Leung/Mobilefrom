<?php
class Resource_System_Mlib_Model extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'mlib_model';
	}

	/**
	 * 获取表中最后一个品牌对应的型号ID
	 * @param 品牌ID $bid
	 */
	public function getLastId($bid)
	{
		$sql = 'select `id` from `mlib_model` 
				where bid = :bid 
				order by id desc limit 1';
		$select = $this->select($sql, array(':bid' => $bid));
		return $this->fetchOne($select);
	}

	/**
	 * 默认排序获取手机型号列表
	 * @param 筛选器 $filter
	 * @param 排序器 $order
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadModelList($filter, $order, $page = 1, $per = 16)//, $available = '-1'
	{
		$where = '';
		$params = array();
		//过滤
		if (isset($filter['brand'])) {
			$where .= 'a.bid = :brand and ';
			$params[':brand'] = $filter['brand'];
		}
		if (isset($filter['released_at'])) {
			$where .= 'a.released_at = :released_at and ';
			$params[':released_at'] = $filter['released_at'];
		}
		if (isset($filter['price'])) {
			$where .= ' a.id in (
							select b.model_id from store_mobile_item as a 
							left join store_mobile_model as b on b.id = a.store_model_id 
							where a.price <= :smax and a.price >= :smin and a.status = 1 
							union
							select b.value_id from mobile as a 
							left join relation_mobile_filter_value as b on b.mobile_id = a.id 
							where a.price <= :max and a.price >= :min
						) and ';
			$params[':min'] = $params[':smin'] = $filter['price']['min'];
			$params[':max'] = $params[':smax'] = $filter['price']['max'];
		}
		if (isset($filter['os'])) {
			$where .= ' a.id in (
							select model_id from mlib_model_attribute_index 
							where attribute_id = 6 and value_id = :os 
						) and ';
			$params[':os'] = $filter['os'];
		}
		//排序
		switch ($order) {
			case 'clicks':
				$order = 'a.clicks desc,';
				break;
			case 'sales':
				$order = 'a.offers desc,';
				break;
			default:
				$order = '';
				break;
		}
		/**
		//是否有售
		switch ($available) {
			case '1':
				$where .= ' a.offers > 0 and ';
				break;
			default:
				$where .= '';
				break;
		}
		*/
		$sql = 'select a.id, a.label as model, b.label as brand, a.thumb, a.clicks, a.offers 
				from `mlib_model` as a 
				left join `mlib_brand` as b on b.id = a.bid 
				where ' . $where . ' a.offers > 0 and a.thumb <> "" and a.status = 1 
				order by ' . $order . ' a.released_at desc, b.id asc, a.id asc 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取手机型号总量
	 * @param 筛选器 $filter
	 */
	public function getModelTotal($filter)
	{
		$where = '';
		$params = array();
		if ($filter) {
		}
		$sql = 'select count(id) from `mlib_model` 
				where released_at <> 0 and status = 1';
		$select = $this->select($sql, $params);
		return $this->fetchOne($select);
	}

	/**
	 * 获取手机型号概述
	 * @param 型号ID $id
	 */
	public function getModelSummary($id)
	{
		$sql = 'select a.id, b.label as brand, a.label as model, a.thumb, a.params, a.released_at 
				from `mlib_model` as a 
				left join `mlib_brand` as b on b.id = a.bid 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取手机相关图片集
	 * @param 型号ID $id
	 */
	public function getModelGallery($id)
	{
		$sql = 'select gallery 
				from `mlib_model` 
				where id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取手机重要参数
	 * @param 型号ID $id
	 */
	public function getModelData($id)
	{
		$sql = 'select params from `mlib_model` where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取手机品牌型号列表
	 * @param 品牌ID $bid
	 */
	public function loadModelListById($bid)
	{
		$sql = 'select `id`, `label` 
				from `mlib_model` 
				where bid = :bid and status = 1 
				order by label asc';
		$select = $this->select($sql, array(':bid' => $bid));
		return $this->fetchAll($select);
	}

	/**
	 * 获取手机型号报价列表
	 * @param 型号ID $data
	 * @param 是否新机 $isnew 
	 */
	public function loadModelQuote($data, $isnew = '1')
	{
		switch ($isnew) {
			case '1':
				$sql = 'select b.model_id as id, min(a.price) as price from store_mobile_item as a 
						left join store_mobile_model as b force index (model) on b.id = a.store_model_id 
						where b.model_id in (' . $data . ') and a.isnew = 1 and a.status = 1 
						group by b.model_id';
				break;
			case '0':
				$sql = 'select id, min(price) as price from (
							select b.model_id as id, min(a.price) as price from store_mobile_item as a 
							left join store_mobile_model as b force index (model) on b.id = a.store_model_id 
							where b.model_id in (' . $data . ') and a.isnew = 0 and a.status = 1 
							group by b.model_id 
							union 
							select b.value_id as id, min(a.price) as price from mobile as a 
							left join relation_mobile_filter_value as b on b.mobile_id = a.id 
							where b.filter_id = 8 and value_id in (' . $data . ') 
							group by b.value_id 
						) as tmp 
						group by id';
				break;
		}
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}

	/**
	 * 获取指定手机型号报价信息
	 * @param 型号ID $id
	 * @param 是否新机 $isnew 
	 */
	public function getModelQuote($id, $isnew = '1')
	{
		switch ($isnew) {
			case '1':
				$sql = 'select min(a.price) as price from store_mobile_item as a 
						left join store_mobile_model as b force index (model) on b.id = a.store_model_id 
						where b.model_id = :model and a.isnew = 1 and a.status = 1 
						group by b.model_id';
				$select = $this->select($sql, array(':model' => $id));
				return $this->fetchOne($select);
				break;
			case '0':
				$sql = 'select min(price) as price from (
							select b.model_id as id, min(a.price) as price from store_mobile_item as a 
							left join store_mobile_model as b force index (model) on b.id = a.store_model_id 
							where b.model_id = :model and a.isnew = 0 and a.status = 1 
							group by b.model_id 
							union 
							select b.value_id as id, min(a.price) as price from mobile as a 
							left join relation_mobile_filter_value as b on b.mobile_id = a.id 
							where b.filter_id = 8 and value_id = :value 
							group by b.value_id 
						) as tmp 
						group by id';
				$select = $this->select($sql, array(':model' => $id, ':value' => $id));
				return $this->fetchOne($select);
				break;
		}
	}

	/**
	 * 获取同价位推荐手机列表型号
	 * @param 主题型号ID $id
	 * @param 价格下限 $min
	 * @param 价格上限 $max
	 * @param 展示数量 $amount
	 */
	public function loadRecommendModelPriceList($id, $min, $max, $amount)
	{
		$params = array();
		$params[':id'] = $id;
		$params[':min'] = $params[':smin'] = $min;
		$params[':max'] = $params[':smax'] = $max;
		$where .= ' a.id in (
						select b.model_id from store_mobile_item as a 
						left join store_mobile_model as b on b.id = a.store_model_id 
						where a.price <= :smax and a.price >= :smin and a.status = 1 
						union
						select b.value_id from mobile as a 
						left join relation_mobile_filter_value as b on b.mobile_id = a.id 
						where a.price <= :max and a.price >= :min
					) and ';
		$sql = 'select a.id, b.label as brand, a.label as model, a.thumb 
				from mlib_model as a 
				left join mlib_brand as b on b.id = a.bid 
				where ' . $where . ' a.id <> :id and a.offers > 0 and a.released_at <> 0 
				limit ' . $amount;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取随机推荐手机列表型号
	 * @param 主题型号ID $id
	 * @param 展示数量 $amount
	 */
	public function loadRecommendModelRandList($id, $amount)
	{
		$sql = 'select a.id, b.label as brand, a.label as model, a.thumb 
				from mlib_model as a 
				left join mlib_brand as b on b.id = a.bid 
				where a.id <> :id and a.offers > 0 and a.released_at <> 0 
				order by rand() 
				limit ' . $amount;
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取手机型号名称
	 * @param 手机型号ID $id
	 */
	public function getModelName($id)
	{
		$sql = 'select b.label as brand, a.label as model 
				from mlib_model as a 
				left join mlib_brand as b on b.id = a.bid 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取店内搜索结果
	 * @param 店铺ID $store
	 * @param 搜索内容 $q
	 */
	public function loadInsideRecords($store, $q)
	{
		$sql = 'select b.label as brand, a.label as model, a.thumb, d.store_model_id as id, max(d.price) as `max`, min(d.price) as `min` 
				from mlib_model as a 
				left join mlib_brand as b on b.id = a.bid 
				left join store_mobile_model as c on c.model_id = a.id 
				left join store_mobile_item as d on d.store_model_id = c.id 
				where c.store_id = :store and a.label like :q and c.status = 1 and d.status = 1 
				group by d.store_model_id 
				order by d.store_model_id desc';
		$select = $this->select($sql, array(':q' => '%' . $q . '%', ':store' => $store));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店内搜索结果总数
	 * @param 店铺ID $store
	 * @param 搜索内容 $q
	 */
	public function getInsideRecordsTotal($store, $q)
	{
		$sql = 'select count(distinct a.id) 
				from mlib_model as a 
				left join store_mobile_model as c on c.model_id = a.id 
				left join store_mobile_item as d on d.store_model_id = c.id 
				where c.store_id = :store and a.label like :q and c.status = 1 and d.status = 1';
		$select = $this->select($sql, array(':q' => '%' . $q . '%', ':store' => $store));
		return $this->fetchOne($select);
	}

	/**
	 * 获取站内搜索结果
	 * @param 搜索内容 $q
	 */
	public function loadOutsideRecords($q)
	{
		$sql = 'select a.id, b.label as brand, a.label as model, a.thumb, a.offers, a.clicks 
				from mlib_model as a 
				left join mlib_brand as b on b.id = a.bid 
				where a.offers > 0 and a.thumb <> 0 and a.label like :q 
				order by a.released_at desc';
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchAll($select);
	}

	/**
	 * 获取站内搜索结果总数
	 * @param 搜索内容 $q
	 */
	public function getOutsideRecordsTotal($q)
	{
		$sql = 'select count(id) 
				from mlib_model 
				where offers > 0 and thumb <> 0 and label like :q';
		$select = $this->select($sql, array(':q' => '%' . $q . '%'));
		return $this->fetchOne($select);
	}

	/***************************************************后台操作***************************************************/
	/**
	 * 获取后台手机品牌型号列表
	 * @param 品牌ID $bid
	 * @param 排序方式 $sort
	 */
	public function loadAdminModelList($bid, $sort = 'id')
	{
		switch ($sort) {
			case 'id':
				$sort = ' id desc ';
				break;
			case 'label':
				$sort = ' label asc ';
				break;
		}
		$sql = 'select id, label, sort, thumb, status from `mlib_model` where bid = :bid and status = 1 order by ' . $sort;
		$select = $this->select($sql, array(':bid' => $bid));
		return $this->fetchAll($select);
	}

	/**
	 * 后台获取手机型号信息
	 * @param 型号ID $id
	 */
	public function getAdminModel($id)
	{
		$sql = 'select a.id, b.label as brand, a.label, a.thumb, a.gallery, a.params, a.released_at 
				from `mlib_model` as a 
				left join `mlib_brand` as b on b.id = a.bid 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}
}
