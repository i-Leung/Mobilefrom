<?php
class Resource_Analysis_Pfrom extends Project_Model_DB_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'analysis_page_from';
	}

	/**
	 * 检测页面导向记录是否存在 
	 * @param 模块 $module
	 * @param 控制器 $controller
	 * @param 动作 $action
	 * @param 导向来源 $from
	 * @param 记录日期 $date
	 */
	public function hasPageFromRecord($module, $controller, $action, $from, $date)
	{
		$sql = 'select id from `analysis_page_from` 
				where `module` = :module and `controller` = :controller 
				and `action` = :action and `from` = :from and `date` = :date 
				limit 1';
		$select = $this->select(
					$sql, 
					array(
						':module' => $module,
						':controller' => $controller,
						':action' => $action,
						':from' => $from,
						':date' => $date
					)
				);
		return $this->fetchOne($select);
	}

	/**
	 * 整站页面导向统计
	 * @param 限制条件 $where
	 * @param 参数 $params
	 */
	public function sitePageFrom($where, $params)
	{
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select module, `from`, sum(amount) as amount 
				from `analysis_page_from`' . $where . ' 
				group by module, `from`'; 
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 模块页面导向统计
	 * @param 限制条件 $where
	 * @param 参数 $params
	 */
	public function modulePageFrom($where, $params)
	{
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select controller, action, `from`, sum(amount) as amount  
				from `analysis_page_from`' . $where . ' 
				group by controller, action, `from`'; 
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}

	/**
	 * 动作页面导向统计
	 * @param 限制条件 $where
	 * @param 参数 $params
	 */
	public function actionPageFrom($where, $params)
	{
		$where = $where ? ' where ' . implode(' and ', $where) : '';
		$sql = 'select `from`, sum(amount) as amount 
				from `analysis_page_from`' . $where . ' 
				group by `from` 
				order by amount desc';
		$select = $this->select($sql, $params);
		return $this->fetchAll($select);
	}
}
