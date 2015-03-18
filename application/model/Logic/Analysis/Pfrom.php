<?php 
class Logic_Analysis_Pfrom
{
	/**
	 * 页面导向资源模型实例
	 */
	private $_resource_page_from = null;

	public function __construct()
	{
		$this->_resource_page_from = Factory::getResource('analysis/pfrom');
	}

	/**
	 * 创建当天页面导向统计记录
	 * @param 模块 $module
	 * @param 控制器 $controller
	 * @param 动作 $action
	 * @param 导向来源 $from
	 * @param 记录日期 $date
	 */
	public function createPageFromRecord($module, $controller, $action, $from, $date)
	{
		return $this->_resource_page_from->create(
			array(
				'module' => $module,
				'controller' => $controller,
				'action' => $action,
				'from' => $from,
				'date' => $date
			)
		);
	}

	/**
	 * 增加导向量
	 * @param 记录ID $id
	 */
	public function modifyPageFromRecord($id)
	{
		return $this->_resource_page_from->modify(
			array('id = "?"' => $id),
			array('amount' => '`amount` + 1')
		);
	}

	/**
	 * 检测页面导向记录是否存在 
	 * @param 模块 $module
	 * @param 控制器 $controller
	 * @param 动作 $action
	 * @param 导向来源 $from
	 * @param 记录日期 $date
	 * @return $id/false
	 */
	public function hasPageFromRecord($module, $controller, $action, $from, $date)
	{
		return $this->_resource_page_from
					->hasPageFromRecord($module, $controller, $action, $from, $date);
	}

	/**
	 * 记录页面导向量
	 * @param 模块 $module
	 * @param 控制器 $controller
	 * @param 动作 $action
	 * @param 导向来源 $cfrom
	 * @param 记录日期 $date
	 */
	public function markPageFrom($module, $controller, $action, $cfrom, $date)
	{
		if ($id = $this->hasPageFromRecord($module, $controller, $action, $cfrom, $date)) {
			return $this->modifyPageFromRecord($id);
		} else {
			return $this->createPageFromRecord($module, $controller, $action, $cfrom, $date);
		}
	}
}
