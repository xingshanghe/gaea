<?php
/** 
* 核心类
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午3:17:08
* @source Core.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace Gaea;

use Flight;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\ErrorHandler;

class Core
{
    private static $_instances = array();
    
    /**
     * 初始化，加载一些必要相关项
     */
    public static function init()
    {
        self::load_func('global');//加载global库函数
        self::error_handler();    //异常捕捉
    }
    
    /**
     * 加载配置文件
     * @param string $file      配置文件名称
     * @param string $key       获取的配置信息的键值
     * @param string $default   默认配置，当获取配置信息失败时返回该值
     * @param string $reload    强制重新加载
     * @throws \Exception
     * @return Ambigous <>|string|array|false
     */
    public static function config($file ,$key = '', $default = false, $reload = false)
    {
        static $_configs = array();
        if (!$reload && isset($_configs[$file])) {
            if (empty($key)) {
                return $_configs[$file];
            } elseif(isset($_configs[$file][$key])) {
                return $_configs[$file][$key];
            }else {
                return $default;
            }
        }
    
        $file_path = APP_PATH.'/configs/'.$file.'.php';
    
        if (file_exists($file_path)) {
            $_configs[$file] = include $file_path;
        }else {
            throw new \Exception('The Config file of '.$file.' not found.');
        }
    
        if (empty($key)){
            return $_configs[$file];
        }elseif(isset($_configs[$file][$key])){
            return $_configs[$file][$key];
        }else{
            return $default;
        }
    }
    
    /**
     * 加载函数文件
     * @param   string $func    函数文件名
     * @param   string $path    函数文件路径
     * @return  <boolean>
     */
    public static function load_func($func,$path = '')
    {
        static $_funcs = array();
    
        if (empty($path))
            $path = VENDOR_PATH .'/Gaea/functions';
    
        $file = $path.'/'.$func.'.php';
        $key = md5($file);
        if (isset($_funcs[$key])) return $_funcs[$key];
        if (file_exists($file)){
            include $file;
            $_funcs[$key] = true;
        }else{
            $_funcs[$key] = false;
        }
        return $_funcs[$key];
    }
    
    /**
     * 日志记录函数
     * @param string $module    日志操作模块
     * @param string $level     日志操作登记包含
     * @param string $message   日志信息
     * @param array $context    日志上下文描述
     * @return void
     */
    public static function log($module,$level,$message, array $context = array())
    {
        $param_arr =  func_get_args();
    
        $module =   array_shift($param_arr);
        $level  =   strtolower(array_shift($param_arr));
    
        $logger = self::logger('logger',array('module' => $module));
    
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && self::config('system','debug')){
            $debug_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);
            $contextinfo = array_shift($debug_info);
            isset($param_arr[1])?null:$param_arr[1]=array();
            $param_arr[1] = array_merge($param_arr[1],$contextinfo);
            unset($contextinfo);
        }
    
        call_user_func_array(array($logger,$level), $param_arr);
    }
    
    public static function session($key = 'default', $option = array())
    {
        static $_sessions = array();
        if (!empty($key)){
            if (isset($_sessions[$key])&& ($_sessions[$key] instanceof Session)){
                return $_sessions[$key];
            }else{
                return $_sessions[$key] = new Session($option);
            }
        }else{
            return $_sessions;
        }
    }
    
    public static function cookie($key = 'default', $option = array()){}
    
    /**
     * 获取db对象实例
     * @param string $key
     * @param array $option
     * @return Ambigous <>|Gaea\Medoo
     */
    public static function db($key = 'default',$option = array())
    {
        static $_dbs = array();
        if (!empty($key)){
            if (isset($_dbs[$key])&& ($_dbs[$key] instanceof medoo)){
                return $_dbs[$key];
            }else{
                $option = array_merge($option,self::config('db','default'));
                return $_dbs[$key] = new Medoo($option);
            }
        }else{
            return $_dbs;
        }
    }
    
    /**
     * 获取memcached客户端实例
     * @param string $key   键值
     * @param array $servers    服务器群参数数组
     * @param array $options    配置参数数组
     * @return Ambigous <>|Gaea\Memcached
     */
    public static function memcached($key = 'default', $servers = array() ,$options = array())
    {
        static $_memcached = array();
        if (!empty($key)){
            if (isset($_memcached[$key]) && ($_memcached[$key] instanceof Memcached)){
                return $_memcached[$key];
            }else{
                return $_memcached[$key] = new Memcached($servers,$options);
            }
        }
        else{
            return $_memcached;
        }
    }
    
    
    /**
     * 初始化logger对象
     * @access private
     * @param string $level 日志级别
     * @return \Monolog\Logger
     */
    public static function logger( $key = 'default',$option = array() )
    {
        static $_loggers = array();
        
        isset($option['module'])?null:$option['module'] = $key;
        
        if (!empty($key)){
            if (isset($_loggers[$key]) && ($_loggers[$key] instanceof Logger)){
                return $_loggers[$key];
            }else{
                
                $logger = new Logger($option['module']);
                $level  =   strtoupper(self::config('system','log_level'));
                switch (strtoupper($level))
                {
                    case 'DEBUG':
                        $log_level = Logger::DEBUG;
                        break;
                    case 'INFO':
                        $log_level = Logger::INFO;
                        break;
                    case 'NOTICE':
                        $log_level = Logger::NOTICE;
                        break;
                    case 'WARNING':
                        $log_level = Logger::WARNING;
                        break;
                    case 'ERROR':
                        $log_level = Logger::ERROR;
                        break;
                    case 'CRITICAL':
                        $log_level = Logger::CRITICAL;
                        break;
                    case 'ALERT':
                        $log_level = Logger::ALERT;
                        break;
                    case 'EMERGENCY':
                        $log_level = Logger::EMERGENCY;
                        break;
                    default:
                        $log_level = Logger::WARNING;
                }
                $log_name = TEMP_PATH .'/logs/'.date('Ymd').'.log';
                $logger->pushHandler(new StreamHandler($log_name,$log_level));
                
                $_loggers[$key] = $logger;
                
                return $_loggers[$key];
            }
        }else{
            return $_loggers;
        }
    }
    
    /**
     * 获取error_handler
     * @return Ambigous <\Monolog\ErrorHandler, \Monolog\ErrorHandler>
     */
    public static function error_handler()
    {
        $logger = self::logger('error');
        return ErrorHandler::register($logger);
    }
}