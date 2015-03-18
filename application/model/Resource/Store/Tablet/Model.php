<?php
class Resource_Store_Tablet_Model extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_tablet_model';
	}

	/**
	 * 店铺列表页获取店铺在售型号
	 * @param 店铺ID $id
	 * @param 显示数量 $amount
	 */
	public function getSellingTabletModelList($id, $amount = 6)
	{
		$sql = 'select a.id, b.label as brand, c.label as model, c.thumb 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				where a.store_id = :id and a.status = 1 
				limit ' . $amount;
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 店铺平板页获取在售型号
	 * @param 店铺ID $id
	 * @param 过滤器 $filter
	 * @param 排序器 $sort
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadSellingTabletModelList($id, $filter, $sort, $page = 1, $per = 9)
	{
		$where = '';
		$params = array(':id' => $id);
		if (isset($filter['brand'])) {
			$where = ' and a.brand_id = :brand ';
			$params[':brand'] = $filter['brand'];
		}
		if (isset($filter['price'])) {
			$where = ' and (d.price >= :min and d.price <= :max) ';
			$price = explode(':', $filter['price']);
			$params[':min'] = $price[0];
			$params[':max'] = $price[1];
		}
		switch ($sort) {
			case 'price':
				$sort = ' min asc ';
				break;
			case 'clicks':
				$sort = ' c.clicks asc ';
				break;
			default:
				$sort = 'a.recommend desc, a.hot desc, c.released_at desc ';
				break;
		}
		$sql = 'select a.id, b.label as brand, c.label as model, c.thumb, max(d.price) as max, min(d.price) as min 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.store_id = :id ' . $where . ' and a.status = 1 
				group by a.id 
				order by ' . $sort . ' 
				limit ' . $per . ' offset ' . ($page - 1) * $per;
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getSellingTabletModelTotal($id)
	{
		$sql = 'select count(id) 
				from store_tablet_model 
				where store_id = :id and status = 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺在售品牌
	 * @param 店铺ID $id
	 */
	public function loadSellingTabletBrandList($id)
	{
		$sql = 'select distinct a.brand_id as id, b.label 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				where a.store_id = :id and a.status = 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取我的店铺销售型号
	 * @param 店铺ID $id
	 * @param 请求页码 $page
	 * @param 单页数量 $per
	 */
	public function loadMyTabletModelList($id)
	{
		$sql = 'select a.brand_id, b.label as brand, a.id as store_model_id, c.label as model, a.status as model_status, d.id as store_tablet_id, d.version, d.color, d.storage, d.isnew, d.newly, d.remark, d.price, d.status as tablet_status, a.hot, a.recommend 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.store_id = :id and a.status <> 0 and d.status <> 0 
				order by a.brand_id, a.model_id, a.id, a.status, d.status, d.isnew desc, d.price desc';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺在售型号总数
	 * @param 店铺ID $id
	 */
	public function getMyTabletModelTotal($id)
	{
		$sql = 'select count(id) 
				from store_tablet_model 
				where store_id = :id';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取指定销售型号信息
	 * @param 型号ID $id
	 */
	public function getTabletModelInfo($id)
	{
		$sql = 'select a.id as store_model_id, b.label as brand, c.label as model, a.gallery, a.remark as mremark, d.version, d.color, d.storage, d.isnew, d.newly, d.remark, d.price 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.id = :id 
				order by d.isnew, d.price';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 坚持平板型号出售信息是否存在
	 * @param 品牌ID $brand_id
	 * @param 型号ID $model_id
	 * @param 店铺ID $store_id
	 */
	public function checkTabletModelExist($brand_id, $model_id, $store_id)
	{
		$sql = 'select id 
				from store_tablet_model 
				where brand_id = :brand and model_id = :model and store_id = :store 
				limit 1';
		$select = $this->select($sql, array(':brand' => $brand_id, ':model' => $model_id, ':store' => $store_id));
		return $this->fetchOne($select);
	}

	/**
	 * 判断指定平板型号出售信息所属
	 * @param 型号ID $store_model_id
	 * @param 店铺ID $store_id
	 */
	public function isMyTabletModel($store_model_id, $store_id)
	{
		$sql = 'select id 
				from store_tablet_model 
				where id = :store_model_id and store_id = :store_id 
				limit 1';
		$select = $this->select($sql, array(':store_model_id' => $store_model_id, ':store_id' => $store_id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺全新平板型号销售信息
	 * @param 型号ID $model_id
	 */
	public function loadModelStoreNewSales($model_id)
	{
		$sql = 'select * from (
					select a.id as store_id, a.customer_id, a.name, b.id as store_model_id, c.version, c.isnew, c.newly, c.color, c.storage, c.price 
					from store as a 
					left join store_tablet_model as b on b.store_id = a.id 
					left join store_tablet_item as c on c.store_model_id = b.id 
					where b.model_id = :model and c.isnew = 1 and a.status = 1 and b.status = 1 and c.status = 1 
					order by a.id asc, c.price asc
				) as tmp 
				group by store_id';
		$select = $this->select($sql, array(':model' => $model_id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺二手平板型号销售信息
	 * @param 型号ID $model_id
	 */
	public function loadModelStoreUsedSales($model_id)
	{
		$sql = 'select * from (
					select a.id as store_id, a.customer_id, a.name, b.id as store_model_id, c.version, c.isnew, c.newly, c.color, c.storage, c.price 
					from store as a 
					left join store_tablet_model as b on b.store_id = a.id 
					left join store_tablet_item as c on c.store_model_id = b.id 
					where b.model_id = :model and c.isnew = 0 and a.status = 1 and b.status = 1 and c.status = 1 
					order by a.id asc, c.price asc
				) as tmp 
				group by store_id';
		$select = $this->select($sql, array(':model' => $model_id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺出售平板型号信息
	 * @param 店铺平板型号ID $id
	 */
	public function getStoreTabletModelInfo($id)
	{
		$sql = 'select a.id, b.label as brand, c.label as model, c.thumb, c.params, c.gallery as ogallery, c.released_at, a.remark, a.gallery as sgallery 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				where a.id = :id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchRow($select);
	}

	/**
	 * 获取我的店铺销售型号
	 * @param 店铺ID $id
	 */
	public function loadStoreTabletModelList($id)
	{
		$sql = 'select a.brand_id, b.label as brand, a.id as store_model_id, c.label as model, d.id as store_tablet_id, d.version, d.color, d.storage, d.isnew, d.newly, d.remark, d.price 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.store_id = :id and a.status = 1 and d.status = 1 
				order by a.brand_id, a.model_id, a.id, d.isnew desc, d.price desc';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取店铺平板型号对应资料库型号ID
	 * @param 店铺型号ID $id
	 */
	public function getTlibModelId($id)
	{
		$sql = 'select model_id from store_tablet_model where id = :id limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺热销商品数量
	 * @param 店铺ID $store
	 */
	public function getStoreHotAmount($store)
	{
		$sql = 'select count(id) from store_tablet_model where store_id = :store and hot = 1';
		$select = $this->select($sql, array(':store' => $store));
		return $this->fetchOne($select);
	}

	/**
	 * 获取热销列表
	 * @param 店铺ID $id
	 */
	public function loadTabletHotList($id)
	{
		$sql = 'select b.label as brand, a.id, c.label as model, c.thumb, min(d.price) as price 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.store_id = :id and a.hot = 1 and a.status = 1 and d.status = 1 
				group by a.id 
				order by a.id 
				limit 5';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 获取推荐列表
	 * @param 店铺ID $id
	 */
	public function loadTabletRecommendList($id)
	{
		$sql = 'select b.label as brand, a.id, c.label as model, c.thumb, min(d.price) as price 
				from store_tablet_model as a 
				left join tlib_brand as b on b.id = a.brand_id 
				left join tlib_model as c on c.id = a.model_id 
				left join store_tablet_item as d on d.store_model_id = a.id 
				where a.store_id = :id and a.recommend = 1 and a.status = 1 and d.status = 1 
				group by a.id 
				order by a.id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 店铺是否有在售平板信息
	 * @param 店铺ID $id
	 */
	public function hasSellingTablet($id)
	{
		$sql = 'select a.id 
				from store_tablet_model as a 
				left join store_tablet_item as b on b.store_model_id = a.id 
				where a.store_id = :id and a.status = 1 and b.status = 1 
				limit 1';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchOne($select);
	}
}
