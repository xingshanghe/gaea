<?php
/** 
* 引导文件
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午3:10:54
* @source bootstrap.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
use Gaea\Core;
/////////////
//核心类初始化
/////////////
Core::init();

$_CFG_SYSTEM = Core::config('system');//获取系统配置
$_CFG_SMARTY = Core::config('smarty');//获取smarty配置
///////////////
//扩展Flight框架
///////////////
//修改模版引擎为smarty
Flight::register('view','Smarty',array(),function ($smarty) use ($_CFG_SMARTY,$_CFG_SYSTEM){
    $smarty->cache_lifetime     =   $_CFG_SMARTY['cache_time'];
    $smarty->template_dir       =   SRC_PATH . sprintf($_CFG_SMARTY['template_dir'] ,$_CFG_SYSTEM['theme']);
    $smarty->compile_dir        =   TEMP_PATH . $_CFG_SMARTY['compile_dir'];
    $smarty->cache_dir          =   TEMP_PATH . $_CFG_SMARTY['cache_dir'];
});
//覆盖render函数，支持smarty
Flight::map('render', function($template, $data = array() ){
    Flight::view()->assign($data);
    Flight::view()->display($template);
});
///////////////
//应用部分开始
///////////////

$_CFG_ROUTE     =   Core::config('route');
Flight::path(SRC_PATH.'/modules');//注册项目下modules模块目录


//自定义路由
//'/test'  =>  array('\sso\controller\Test','index'),
if (!empty($_CFG_ROUTE)){
    foreach ($_CFG_ROUTE as $key => $value ){
        Flight::route( $key ,function () use ($value) {
            if (isset($value[2])&&(!empty($value[2]))){
                //类的静态方法
                unset($value[2]);
                call_user_func_array($value, func_get_args());
            }else{
                //类的公开非静态方法
                $callback = array(new $value[0](),$value[1]);
                call_user_func_array( $callback , func_get_args());
            }
        });
    }
}
/**
 * 配置通用路由
 * 基于传递参数的方式的考虑没有采用反射实现。
 * 支持以“/模块/控制器/方法/参数”形势的路由访问
 */
Flight::route('/@module/@controller/@action/*',function (){
    $params =   func_get_args();
    $module =   array_shift($params);
    $controller  =   array_shift($params);
    $action =   array_shift($params);
    $route_obj = array_shift($params);

    $params = explode('/', $route_obj->splat);unset($route_obj);
    if (!is_callable(array('\\'.$module.'\\controller\\'.$controller,$action))){
        throw new Exception('The "\\'.$module.'\\controller\\'.$controller.'::'.$action.'()" is not callable.');
    }
    $class_name = '\\'.$module.'\\controller\\'.$controller;
    $callback = array(new $class_name(),$action);
    call_user_func_array( $callback , $params);
},true);
/**
 * 整体通配路由
 */
Flight::route('/*',function (){
    Flight::notFound();
});