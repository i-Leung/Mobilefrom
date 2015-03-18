<?php
class Page_PaginatorView extends Project_View_Block_Abstract
{
	/**
	 * 总页数
	 */
	private $_total;

	/**
	 * 当前请求页码
	 */
	private $_current;

	/**
	 * 每页显示数量
	 */
	private $_per;

	/**
	 * 翻页url
	 */
	private $_url;

	/**
	 * 实例化翻页对象
	 * @param 总页数 $total
	 * @param 当前页码 $current
	 * @param 每页数量 $per
	 * @param 翻页url $url
	 * @param 解析模板文件路径 $tpl
	 */
	public function init($total, $current, $per, $url, $tpl = null)
	{
		parent::__construct();
		$this->_setTotal($total);
		$this->_setCurrent($current);
		$this->_setPerPage($per);
		$this->_setUrl($url);
		$this->_setTpl($tpl);		
		return $this;
	}
	
	/**
	 * 设置翻页总页数
	 * @param 总页数 $total
	 */
	private function _setTotal($total)
	{
		$this->_total = $total;
	}

	/**
	 * 设置当前页码
	 * @param 请求页面 $current
	 */
	private function _setCurrent($current)
	{
		$this->_current = $current;
	}

	/**
	 * 设置每页显示数量
	 * @param 每页显示数量 $per
	 */
	private function _setPerPage($per)
	{
		$this->_per = $per;
	}

	/**
	 * 设置翻页url
	 * @param url $url
	 */
	private function _setUrl($url)
	{
		$this->_url = $url;
	}

	/**
	 * 设置解析模板
	 * @param 模板名 $tpl
	 */
	private function _setTpl($tpl)
	{
		switch($tpl) {
			case 'admin':
				$this->_template = 'page/paginator.phtml';
				break;
			case 'front':
				break;
			default:
				break;
		}
	}

	/**
	 * 获取总页数
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
	 * 获取当前页码
	 */
	public function getCurrent()
	{
		return $this->_current;
	}

	/**
	 * 获取每页显示数量
	 */
	public function getPerPage()
	{
		return $this->_per;
	}

	/**
	 * 获取翻页url
	 */
	public function getUrl()
	{
		return $this->_url;
	}
}
