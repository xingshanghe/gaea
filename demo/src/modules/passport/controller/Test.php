<?php
/** 
* Test控制器
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午3:16:09
* @source Test.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace passport\controller;

use Flight;
use Gaea\Core;

class Test
{
    public function index($name)
    {
        
        //获取常用对象
//         $session = Core::session();
//         $db = Core::db();
//         $memcached = Core::memcached();
//         $logger= Core::logger();
        
//         dump($session);
//         dump($db);
//         dump($memcached);
//         dump($logger);
        
        
        $data = array('name'=>$name?$name:'gaea');
        Flight::render('passport/test/index.tpl',$data);
    }
}