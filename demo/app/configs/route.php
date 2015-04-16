<?php
/** 
* 单点登录 － 路由配置文件
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月5日上午9:33:40
* @source route.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
//当php >=5.3时，类的公开的非静态的方法必须在类实例化后方可被调用，否则会提示Strict性错误（为了兼容先前及以后的版本，还是用对象方法传入）。
return array(
    '/hello(/@name)'             =>  array('passport\controller\Test','index'),
);