<?php
class Plugin_SwfuploadController extends Project_Controller_Action_Front
{

	public function init()
	{
		$this->_session_run = 0;
		parent::init();
	}
	
	/**
	 * swfupload插件图片缩略图显示动作
	 */
	public function thumbnailAction()
	{
		// This script accepts an ID and looks in the user's session for stored thumbnail data.
		// It then streams the data to the browser as an image
		
		// Work around the Flash Player Cookie Bug
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		}
		
		//开启session
		session_start();
		
		$image_id = isset($_GET["id"]) ? $_GET["id"] : false;
		
		if ($image_id === false) {
			header("HTTP/1.1 500 Internal Server Error");
			echo "No ID";
			exit(0);
		}
		
		if (!is_array($_SESSION["file_info"]) || !isset($_SESSION["file_info"][$image_id])) {
			header("HTTP/1.1 404 Not found");
			exit(0);
		}
		exit(0);
	}
	
	/**
	 * swfupload插件图片上传动作
	 */
	public function uploadImgAction()
	{
		/* Note: This thumbnail creation script requires the GD PHP Extension.
		 If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
		*/
		
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$customer_id = Factory::getSession('customer/id');
		$pdir = 'upload/mobile/';
		$odir = $customer_id . '/';//原图路径
		$tdir = 'thumb/' . $customer_id . '/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImg($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $odir . $filename . '.' . $ext;	// Return the file id to the script
	}
	
	/**
	 * swfupload插件图片上传动作
	 */
	public function uploadTabletImgAction()
	{
		/* Note: This thumbnail creation script requires the GD PHP Extension.
		 If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
		*/
		
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$customer_id = Factory::getSession('customer/id');
		$pdir = 'upload/tablet/';
		$odir = $customer_id . '/';//原图路径
		$tdir = 'thumb/' . $customer_id . '/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImg($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $odir . $filename . '.' . $ext;	// Return the file id to the script
	}
	
	/**
	 * swfupload插件图片上传举报图片
	 */
	public function uploadReportAction()
	{
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		if (isset($_POST["PHPSESSID"])) {
			session_id($_POST["PHPSESSID"]);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$pdir = 'upload/report/';
		$odir = '';
		$tdir = 'thumb/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImg($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $filename . '.' . $ext;	// Return the file id to the script
	}
	
	/**
	 * swfupload插件图片上传手机资料库推荐图片
	 */
	public function mlibMajorImgAction()
	{
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		$id = $this->_request->getParam('id', null);
		if (isset($_POST["PHPSESSID"]) && $id) {
			session_id($_POST["PHPSESSID"]);
		} else {
			echo 'ERROR:illegal request!';
			exit(0);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$pdir = 'upload/mlib/';
		$odir = $id . '/';
		$tdir = 'thumb/' . $id . '/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImg($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $odir . $filename . '.' . $ext;	// Return the file id to the script
	}
	
	/**
	 * swfupload插件图片上传平板资料库推荐图片
	 */
	public function tlibMajorImgAction()
	{
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		$id = $this->_request->getParam('id', null);
		if (isset($_POST["PHPSESSID"]) && $id) {
			session_id($_POST["PHPSESSID"]);
		} else {
			echo 'ERROR:illegal request!';
			exit(0);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$pdir = 'upload/tlib/';
		$odir = $id . '/';
		$tdir = 'thumb/' . $id . '/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImgTlib($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $odir . $filename . '.' . $ext;	// Return the file id to the script
	}
	
	/**
	 * swfupload插件图片上传店铺图片
	 */
	public function uploadStoreImgAction()
	{
		// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
		$id = $this->_request->getParam('id', null);
		if (isset($_POST["PHPSESSID"]) && $id) {
			session_id($_POST["PHPSESSID"]);
		} else {
			echo 'ERROR:illegal request!';
			exit(0);
		}
		
		//开启session
		session_start();
		
		// Check the upload
		if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
			echo "ERROR:invalid upload";
			exit(0);
		}
		
		//Get image info and upload path
		$filename = date('Ymd-his') . '-' . rand(0, 10000);
		$ext = pathinfo($_FILES["Filedata"]["name"], PATHINFO_EXTENSION);
		$pdir = 'upload/store/';
		$odir = $id . '/';
		$tdir = 'thumb/' . $id . '/';//缩略图路径
		//上传图片统一处理
		$this->dealWithImg($pdir, $odir, $tdir, $filename, $ext, $_FILES["Filedata"]["tmp_name"]);
		echo "FILEID:" . $odir . $filename . '.' . $ext;	// Return the file id to the script
	}

	/**
	 * 上传图片一致处理
	 * @param 父目录 $pdir
	 * @param 原图目录 $odir
	 * @param 缩略图目录 $tdir
	 * @param 图片名 $filename
	 * @param 图片后缀 $ext
	 * @param 上传临时文件 $tmp
	 */
	public function dealWithImg($pdir, $odir, $tdir, $filename, $ext, $tmp)
	{
		$attr = array(
			'expires' => 'access plus 1 day'
		);
		$s = new SaeStorage();
		
		/**
		//判断文件夹是否存在
		if (!is_dir($pdir . $odir)) {
			mkdir($pdir . $odir, 0777);
		}
		if (!is_dir($pdir . $tdir)) {
			mkdir($pdir . $tdir, 0777);
		}
		*/
		// Get the image and create a thumbnail
		switch ($ext) {
			case 'jpg':
			case 'JPG':
				$img = imagecreatefromjpeg($tmp);
				break;
			case 'png':
			case 'PNG':
				$img = imagecreatefrompng($tmp);
				break;
			case 'gif':
			case 'GIF':
				$img = imagecreatefromgif($tmp);
				break;
		}
		
		if (!$img) {
			$tmp = file_get_contents($tmp);
			$s->upload(
				'upload', 
				$pdir . $tdir . $filename . '.' . $ext, 
				$tmp
			);
			if ($s->errno() != '0') {
				echo "生成缩略图失败";
				exit(0);
			}
			$s->write(
				'upload', 
				$pdir . $odir . $filename . '.' . $ext, 
				$tmp
			);
			if ($s->errno() != '0') {
				echo "图片上传失败";
				exit(0);
			}
			/**
			$tmp = file_get_contents($tmp);
 			$thumb = fopen($pdir . $tdir . $filename . '.' . $ext, "w+");
 			$resultThumb = fwrite($thumb, $tmp);
 			fclose($thumb);
 			if (!$resultThumb) {
 				Factory::setMsg('create thumbnail failed!');
				echo '生成缩略图失败';
 				exit(0);
 			}
 			$origin = fopen($pdir . $odir . $filename . '.' . $ext, "w+");
 			$resultOrigin = fwrite($origin, $tmp);
 			fclose($origin);
			if (!$resultOrigin) {
 				Factory::setMsg('create origninal picture failed!');
				echo '生成原图失败';
 				exit(0);
 			}
			*/
		} else {
			$width = imageSX($img);
			$height = imageSY($img);
		
			if (!$width || !$height) {
				echo "无法获取图片尺寸";
				exit(0);
			}
			//新版裁切原图中间正方形部分
			$new_width = $new_height = $src_x = $src_y = 0;
			$target_width = $target_height = 320;
			if ($width >= $height) {
				$new_width = $new_height = $height;
				$src_x = ($width - $height) / 2;
				$src_y = 0;
			} else {
				$new_width = $new_height = $width;
				$src_x = 0;
				$src_y = ($height - $width) / 2;
			}
			$new_img = ImageCreateTrueColor(320, 320);
			if (!@imagecopyresampled($new_img, $img, 0, 0, $src_x, $src_y, $target_width, $target_height, $new_width, $new_height)) {
				echo "调整图片尺寸失败";
				exit(0);
			}
			
		
			//Use a output buffering to load the image into a variable
			switch ($ext) {
				case 'jpg':
				case 'JPG':
					//缩略图
					ob_start();
					header ("content-type: image/jpeg");
					imagejpeg($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/jpeg");
					imagejpeg($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
				case 'png':
				case 'PNG':
					//缩略图
					ob_start();
					header ("content-type: image/png");
					imagepng($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/png");
					imagepng($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
				case 'gif':
				case 'GIF':
					//缩略图
					ob_start();
					header ("content-type: image/gif");
					imagegif($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/gif");
					imagegif($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
			}
		
			//Move the upload Imgs
			$s->write(
				'upload', 
				$pdir . $tdir . $filename . '.' . $ext, 
				$imgthumb, 
				-1,
				$attr
			);
			if ($s->errno() != '0') {
				echo "生成缩略图失败";
				exit(0);
			}
			$s->upload(
				'upload', 
				$pdir . $odir . $filename . '.' . $ext, 
				$imgorigin
			);
			if ($s->errno() != '0') {
				echo "图片上传失败";
				exit(0);
			}
			/**
 			$thumb = fopen($pdir . $tdir . $filename . '.' . $ext, "w+");
 			$resultThumb = fwrite($thumb, $imgthumb);
 			fclose($thumb);
 			if (!$resultThumb) {
 				Factory::setMsg('create thumbnail failed!');
				echo '生成缩略图失败';
 				exit(0);
 			}
 			$origin = fopen($pdir . $odir . $filename . '.' . $ext, "w+");
 			$resultOrigin = fwrite($origin, $imgorigin);
 			fclose($origin);
 			if (!$resultOrigin) {
 				Factory::setMsg('create origninal picture failed!');
				echo '生成原图失败';
 				exit(0);
 			}
			*/
		}
	}

	/**
	 * 上传图片一致处理
	 * @param 父目录 $pdir
	 * @param 原图目录 $odir
	 * @param 缩略图目录 $tdir
	 * @param 图片名 $filename
	 * @param 图片后缀 $ext
	 * @param 上传临时文件 $tmp
	 */
	public function dealWithImgTlib($pdir, $odir, $tdir, $filename, $ext, $tmp)
	{
		//判断文件夹是否存在
		if (!is_dir($pdir . $odir)) {
			mkdir($pdir . $odir, 0777);
		}
		if (!is_dir($pdir . $tdir)) {
			mkdir($pdir . $tdir, 0777);
		}
		// Get the image and create a thumbnail
		switch ($ext) {
			case 'jpg':
			case 'JPG':
				$img = imagecreatefromjpeg($tmp);
				break;
			case 'png':
			case 'PNG':
				$img = imagecreatefrompng($tmp);
				break;
			case 'gif':
			case 'GIF':
				$img = imagecreatefromgif($tmp);
				break;
		}
		if (!$img) {
			$tmp = file_get_contents($tmp);
			$s->upload(
				'upload', 
				$pdir . $tdir . $filename . '.' . $ext, 
				$tmp
			);
			if ($s->errno() != '0') {
				echo "生成缩略图失败";
				exit(0);
			}
			$s->write(
				'upload', 
				$pdir . $odir . $filename . '.' . $ext, 
				$tmp
			);
			if ($s->errno() != '0') {
				echo "图片上传失败";
				exit(0);
			}
			/**
			$tmp = file_get_contents($tmp);
 			$thumb = fopen($pdir . $tdir . $filename . '.' . $ext, "w+");
 			$resultThumb = fwrite($thumb, $tmp);
 			fclose($thumb);
 			if (!$resultThumb) {
 				Factory::setMsg('create thumbnail failed!');
				echo '生成缩略图失败';
 				exit(0);
 			}
 			$origin = fopen($pdir . $odir . $filename . '.' . $ext, "w+");
 			$resultOrigin = fwrite($origin, $tmp);
 			fclose($origin);
			if (!$resultOrigin) {
 				Factory::setMsg('create origninal picture failed!');
				echo '生成原图失败';
 				exit(0);
 			}
			*/
		} else {
			//新版裁切原图中间正方形部分
			$new_img = ImageCreateTrueColor(300, 226);
			if (!@imagecopyresampled($new_img, $img, 0, 0, 0, 0, 300, 226, 800, 600)) {
				echo "调整图片尺寸失败";
				exit(0);
			}
			
		
			//Use a output buffering to load the image into a variable
			switch ($ext) {
				case 'jpg':
				case 'JPG':
					//缩略图
					ob_start();
					header ("content-type: image/jpeg");
					imagejpeg($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/jpeg");
					imagejpeg($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
				case 'png':
				case 'PNG':
					//缩略图
					ob_start();
					header ("content-type: image/png");
					imagepng($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/png");
					imagepng($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
				case 'gif':
				case 'GIF':
					//缩略图
					ob_start();
					header ("content-type: image/gif");
					imagegif($new_img);
					$imgthumb = ob_get_contents();
					ob_end_clean();
					//原图
					ob_start();
					header ("content-type: image/gif");
					imagegif($img);
					$imgorigin = ob_get_contents();
					ob_end_clean();
					break;
			}
		
			//Move the upload Imgs
			$s->write(
				'upload', 
				$pdir . $tdir . $filename . '.' . $ext, 
				$imgthumb, 
				-1,
				$attr
			);
			if ($s->errno() != '0') {
				echo "生成缩略图失败";
				exit(0);
			}
			$s->upload(
				'upload', 
				$pdir . $odir . $filename . '.' . $ext, 
				$imgorigin
			);
			if ($s->errno() != '0') {
				echo "图片上传失败";
				exit(0);
			}
			/**
 			$thumb = fopen($pdir . $tdir . $filename . '.' . $ext, "w+");
 			$resultThumb = fwrite($thumb, $imgthumb);
 			fclose($thumb);
 			if (!$resultThumb) {
 				Factory::setMsg('create thumbnail failed!');
				echo '生成缩略图失败';
 				exit(0);
 			}
 			$origin = fopen($pdir . $odir . $filename . '.' . $ext, "w+");
 			$resultOrigin = fwrite($origin, $imgorigin);
 			fclose($origin);
 			if (!$resultOrigin) {
 				Factory::setMsg('create origninal picture failed!');
				echo '生成原图失败';
 				exit(0);
 			}
			*/
		}
	}
}
