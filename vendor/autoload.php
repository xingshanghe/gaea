<?php
/** 
* 加载
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午3:07:23
* @source autoload.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

if (!defined('GAEA')) die('Access Denied.');

require __DIR__.'/flight/Flight.php'; //引入Flight框架
Flight::path(__DIR__);//支持vendor下psr标准的会自动加载

require __DIR__.'/smarty/Smarty.class.php';//引入模版引擎Smarty类
require __DIR__.'/medoo/medoo.php';//引入medoo类
