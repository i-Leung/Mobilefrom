<?php
class Logic_Cache 
{
	/**
	 * 缓存目录
	 */
	private $_path = './var/cache/';

	/**
	 * 检测缓存文件是否存在
	 * @param 文件名 $filename
	 */
	public function checkCache($filename)
	{
		if ($filename && file_exists($this->_path . $filename)) {
			return true;
		}
		return false;
	}

	/**
	 * 获取缓存内容
	 * @param 缓存文件名 $filename
	 */
	public function getCache($filename)
	{
		if (!$this->checkCache($filename)) return false;
		$content = file_get_contents($this->_path . $filename);
		return unserialize($content);
	}

	/**
	 * 生成缓存文件
	 * @param 文件名 $filename
	 * @param 缓存内容 $content
	 */
	public function createCache($filename, $content)
	{
		/**
		if ($filename && $content) {
			try {
				file_put_contents($this->_path . $filename, serialize($content));
				return true;
			} catch (Exception $e) {
				Factory::setMsg('Create Cache File Failed!:' . $e->getMessage());
			}
		}
		return false;
		*/
		return true;
	}

	/**
	 * 删除缓存文件
	 * @param 缓存文件名 $filename
	 * @param 是否批量删除 $batch
	 */
	public function clearCache($filename, $batch = false)
	{
		/**
		if (!$filename) return false;
		try{
			if ($batch) {
				$dir = opendir($this->_path);
				while (($file = readdir($dir)) !== false) {
					if (strstr($file, $filename)) {
						unlink($this->_path . $file);
					}
				}
			} else {
				if ($this->checkCache($filename)) {
					unlink($this->_path . $filename);
				}
			}
			return true;
		} catch (Exception $e) {
			Factory::setMsg('Clear Cache File Failed!:' . $e->getMessage());
			return false;
		}
		*/
		return true;
	}

	/**
	 * 删除所有缓存文件
	 */
	public function clearAllCache()
	{
		/**
		try{
			$dir = opendir($this->_path);
			while (($file = readdir($dir)) !== false) {
				if (!in_array($file, array('.', '..'))) {
					unlink($this->_path . $file);
				}
			}
			return true;
		} catch (Exception $e) {
			Factory::setMsg('Clear Cache File Failed!:' . $e->getMessage());
			return false;
		}
		*/
		return true;
	}
}
