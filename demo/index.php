<?php
/** 
* 入口
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午3:09:29
* @source index.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
define('GAEA', TRUE);

/////////////////////
//常量定义
////////////////////
define('ROOT_PATH', __DIR__); //根目录
define('VENDOR_PATH', dirname(ROOT_PATH).'/vendor');//vendor目录
define('APP_PATH', ROOT_PATH.'/app');//app目录
define('SRC_PATH', ROOT_PATH.'/src');//src目录
define('TEMP_PATH', ROOT_PATH.'/temp');//temp目录
define('WEB_PATH', ROOT_PATH.'/web');//web目录


require VENDOR_PATH.'/autoload.php';//自动加载文件(库，函数)
require APP_PATH.'/bootstrap.php';//引导文件

Flight::start();//程序启动
