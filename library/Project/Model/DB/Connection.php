<?php
/**
 * 
 * 数据库基础链接类
 * @author 斌
 *
 */
class Project_Model_DB_Connection
{
	/**
	 * 静态变量
	 * 存放实例对象
	 */
	static private $_instance = NULL;
	
	/**
	 * 数据库链接参数
	 */
	private $_host = SAE_MYSQL_HOST_M;
	private $_port = SAE_MYSQL_PORT;
	private $_username = SAE_MYSQL_USER;
	private $_password = SAE_MYSQL_PASS;
	private $_dbname = SAE_MYSQL_DB;
	
	/**
	 * 数据库链接对象
	 */
	public $connection = NULL;
	
	/**
	 * 私有化构造函数
	 * @todo 放在外部程序通过new重新实例化对象
	 */
	private function __construct(){
		$this->connection = $this->_connect();
	}
	
	/**
	 * 私有化克隆函数
	 * @todo 放在外部程序通过new重新实例化对象
	 */
	private function __clone(){}
	
	/**
	 * 链接数据库
	 */
	private function _connect()
	{
		$connection = null;
		try {
			$connection = new PDO(
					'mysql:host=' . $this->_host . ';port=' . $this->_port . ';dbname=' . $this->_dbname, 
					$this->_username, 
					$this->_password
				);
			$connection->query('SET NAMES utf8');
			return $connection;
		} catch (PDOException $e) {
			Factory::setMsg('database connection fail : ' . $e->getMessage());
			exit(0);
		}
	}
	
	static public function getInstance()
	{
		if (is_null(self::$_instance) || !isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
