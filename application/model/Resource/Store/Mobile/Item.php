<?php
class Resource_Store_Mobile_Item extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'store_mobile_item';
	}

	/**
	 * 判断指定手机出售信息所属
	 * @param 型号ID $store_model_id
	 * @param 店铺ID $store_id
	 */
	public function isMyMobileItem($id, $store_id)
	{
		$sql = 'select a.id 
				from store_mobile_item as a 
				left join store_mobile_model as b on b.id = a.store_model_id 	
				where a.id = :id and b.store_id = :store_id 
				limit 1';
		$select = $this->select($sql, array(':id' => $id, ':store_id' => $store_id));
		return $this->fetchOne($select);
	}

	/**
	 * 获取店铺出售手机型号下手机列表
	 * @param 店铺手机型号ID $id
	 */
	public function loadStoreModelMobileList($id)
	{
		$sql = 'select version, color, storage, newly, remark, price 
				from store_mobile_item 
				where store_model_id = :id and status = 1 
				order by isnew desc, price desc';
		$select = $this->select($sql, array(':id' => $id));
		return $this->fetchAll($select);
	}

	/**
	 * 加载指定型号商家报价
	 * @param 店铺手机型号ID $id
	 */
	public function loadModelStoreQuoteList($ids)
	{
		$sql = 'select store_model_id as model, min(price) as `min`, isnew 
				from store_mobile_item 
				where store_model_id in (' . $ids . ') and isnew = 1 and status = 1 
				group by model 
				union 
				select store_model_id as model, min(price) as `min`, isnew 
				from store_mobile_item 
				where store_model_id in (' . $ids . ') and isnew = 0 and status = 1 
				group by model';
		$select = $this->select($sql);
		return $this->fetchAll($select);
	}
}
