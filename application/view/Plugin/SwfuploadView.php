<?php
class Plugin_SwfuploadView extends Project_View_Block_Abstract
{
	/**
	 * 获取图片上传url
	 */
	public function getUploadImgUrl()
	{
		return Factory::getUrl('plugin/swfupload/upload-img');
	}
	
	/**
	 * 获取图片储存目录路径
	 */
	public function getUploadDir($dir)
	{
		return $dir ? $dir : date('Ymd_his') . rand(1, 100);
	}
}