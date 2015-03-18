<?php
class Project_Model_DB_Abstract
{
	/**
	 * 数据库链接资源对象
	 */
	protected $_db = NULL;
	
	/**
	 * @var 对象数据表
	 */
	protected $_table = NULL;
	
	/**
	 * @var 最新插入数据ID
	 */
	protected $_newId = NULL;
	
	/**
	 * 实例化同时获取数据库链接资源对象
	 */
	public function __construct()
	{
		$this->_db = Project_Model_DB_Connection::getInstance()->connection;
	}
	
	/**
	 * 插入数据
	 * @param 需要插入的数据 $data
	 * @param 插入动作类型：single(单条插入)\multiple(多条插入)
	 * @return boolean
	 */
	public function create($data, $type = 'single')
	{
		//验证输入数据完整性
		if (!$this->_table || !$data || !is_array($data)) {
			return false;
		}
		$columns = array();
		$values = array();
		$sql = null;
		//获取列名
		$columns = array_keys($data);
		$columns = '(`' . implode('`,`', $columns) . '`)';
		//获取值
		switch ($type) {
			case 'single':
				$values = array_values($data);
				foreach ($values as &$value) {
					$value = $this->_db->quote($value);
				}
				$values = '(' . implode(',', $values) . ')';
				break;
			case 'multiple':
				//array('column1' => array('val11', 'val12', 'val13'), 'column2' => array('val21', 'val22', 'val23'))
				$tmp = array_values($data);
				foreach ($tmp as $i => $iv) {
					foreach ($iv as $j => $jv) {
						$values[$j][$i] = $this->_db->quote($jv);
					}
				}
				foreach ($values as &$value) {
					$value = '(' . implode(',', $value) . ')';
				}
				$values = implode(',', $values);
				break;
		}
		//构建sql
		$sql = 'insert into `' . $this->_table . '` ' . $columns . ' values ' . $values;
		//插入操作
		try {
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_db->beginTransaction();
			$this->_db->exec($sql);
			$this->setNewId($this->_db->lastInsertId());//储存最新生成记录ID
			$this->_db->commit();
			return true;
		} catch (PDOException $e) {
			$this->_db->rollBack();
			Factory::setMsg($sql . ':' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 更新数据
	 * @param 筛选条件 $condition
	 * @param 需要更新的数据 $data
	 * @return boolean
	 */
	public function modify($condition, $data)
	{
		if (!$this->_table || !$data || !is_array($data) || !$condition || !is_array($condition)) {
			return false;
		}
		//修改数据
		$set = array();
		foreach ($data as $key => $value) {
			if (substr_count($value, '`') > 1) {
				$set[] = '`' . $key . '` = ' . $value;
			} else {
				$set[] = '`' . $key . '` = ' . $this->_db->quote($value);
			}
		}
		$set = implode(',', $set);
		//修改条件
		$where = array();
		foreach ($condition as $key => $value) {
			$where[] = str_ireplace('?', $value, $key);
		}
		$where = implode(' and ', $where);
		$sql = 'update `' . $this->_table . '` set ' . $set . ' where ' . $where;
		try {
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_db->beginTransaction();
			$this->_db->exec($sql);
			$this->_db->commit();
			return true;
		} catch (PDOException $e) {
			$this->_db->rollBack();
			Factory::setMsg($sql . ':' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 删除数据
	 * @param 筛选条件 $condition
	 * @return boolean
	 */
	public function remove($condition)
	{
		if (!$this->_table || !$condition || !is_array($condition)) {
			return false;
		}
		//修改条件
		$where = array();
		foreach ($condition as $key => $value) {
			$where[] = str_ireplace('?', $value, $key);
		}
		$where = implode(' and ', $where);
		$sql = 'delete from `' . $this->_table . '` where ' . $where;
		try {
			$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_db->beginTransaction();
			$this->_db->exec($sql);
			$this->_db->commit();
			return true;
		} catch (PDOException $e) {
			$this->_db->rollBack();
			Factory::setMsg($sql . ':' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 储存最新生成记录ID
	 */
	public function setNewId($id)
	{
		$this->_newId = $id;
		return $this;
	}
	
	/**
	 * 获取最新生成记录自增ID
	 */
	public function getNewId()
	{
		return $this->_newId;
	}
	
	/**
	 * 加载查询语句
	 * @param 所执行的sql语句 $sql
	 * @param 参数 $params
	 */
	public function select($sql, $params = NULL)
	{
		try {
			$fetch = $this->_db->prepare($sql);
			if (is_array($params)) {
				foreach ($params as $key => $value) {
					$fetch->bindValue($key, $value);
				}
			}
			$fetch->execute();
			return $fetch;
		} catch (Exception $e) {
			Factory::setMsg($sql . ':' . $e->getMessage());
			return false;
		}
	}
	
	/**
	 * 返回一条记录
	 * @param PDOStatement对象 $select
	 */
	public function fetchRow(PDOStatement $select)
	{
		return $select->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * 返回匹配记录集
	 * @param PDOStatement对象 $select
	 */
	public function fetchAll(PDOStatement $select)
	{
		return $select->fetchAll(PDO::FETCH_ASSOC);
	}
	
	/**
	 * 返回第一个列值
	 * @param PDOStatement对象 $select
	 */
	public function fetchOne(PDOStatement $select)
	{
		return $select->fetchColumn(0);
	}
}
