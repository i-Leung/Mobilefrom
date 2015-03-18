<?php
//定义应用代码路径
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

//引入文件导入路径
set_include_path(
	implode(
		PATH_SEPARATOR, array(
			realpath(APPLICATION_PATH . '/module'),
			realpath(APPLICATION_PATH . '/model'),
			realpath(APPLICATION_PATH . '/view'),
		    realpath(APPLICATION_PATH . '/../library'),
		    get_include_path(),
		)
	)
);

error_reporting(E_ERROR | E_WARNING | E_PARSE);

//静态参数
require 'application/Static.php';

//工厂模式
require 'application/Factory.php';

//公共方法
require 'application/Func.php';

//应用驱动
require 'library/Project/Application.php';
$app = new Project_Application();
$app->run();
