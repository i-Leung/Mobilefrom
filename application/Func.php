<?php
/**
 * 自动加载类文件
 * @param 类名 $classname
 */
function __autoload($classname)
{
	$file = null;
	if (stristr($classname, '_')) {
		$classname = str_replace('_', '/', $classname);
	}
	$file = $classname . '.php';
	if (!include_once($file)) {
		notfound();
	}
	return;
}

function notfound()
{
	header("HTTP/1.1 404 Not Found");  
	header("Status: 404 Not Found");
	$curl = curl_init();
	session_start();
	session_write_close();
	curl_setopt($curl, CURLOPT_URL, Factory::getUrl('default/website/notfound'));
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);
	echo $data;
	exit(0);
}

/**
 * 获取手机价格范围
 * @param 手机价格 $price
 * @return 价格范围ID
 */
function getLevel($price)
{
	global $static;
	$price = intval($price);
	if (!$price) {
		return 8;
	}
	foreach ($static['mobile'][3] as $key => $value) {
		if ($price > $value['min'] && $price <= $value['max']) {
			return $key;
		}
	}
}

/**
 * 用户友好时间表达
 * @param 原始格式时间 $datetime
 */
function timeforcustomer($datetime)
{
	if ($datetime == '0') {
		return '不设限期';
	}
	$interval = ceil((time() - $datetime) / 60);
	if ($interval <= 60) {
		return '<span style="color:#669900;">' . $interval . '分钟前</span>';
	}
	$interval = ceil((time() - $datetime) / 3600);
	if ($interval <= 24) {
		return '<span style="color:#669900;">' . $interval . '小时内</span>';
	}
	$interval = ceil((time() - $datetime) / 86400);
	if ($interval <= 7) {
		return '<span style="color:#669900;">' . $interval . '天内</span>';
	}
	return date('m月d日', $datetime);
}

/**
 * 获取真实IP
 */
function getIP()
{
	$ip = null;
	if (getenv("HTTP_CLIENT_IP")) {
		$ip = getenv("HTTP_CLIENT_IP");
	} elseif(getenv("HTTP_X_FORWARDED_FOR")) {
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	} elseif(getenv("REMOTE_ADDR")) {
		$ip = getenv("REMOTE_ADDR");
	} else {
		$ip = "Unknow";
	}
	return $ip;
}
