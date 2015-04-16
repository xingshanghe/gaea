<?php
/** 
* Session类 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年3月25日下午5:24:18
* @source Session.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace Gaea;

class Session
{
    private $_prefix='';
    
    private $_handler = 'files';//[files|memcached]
    
    /**
     * 构造函数
     * @param unknown $option
     */
    public function __construct($option = array())
    {
        $this->_prefix = Core::config('system','prefix');
        
        if (is_array($option)&&(!empty($option))){//session 配置，在session_start之前调用
            if (isset($option['prefix'])){
                $this->_set_prefix($option['prefix']);
            }
            $this->_set_option($option);
        }
        $this->start();
    }
    
    /**
     * 更改session设置
     * @param unknown $option
     */
    private function _set_option($option)
    {
        if (isset($option['name']))   session_name($option['name']);
        if (isset($option['path']))   session_save_path($option['path']);
        if (isset($option['domain'])) ini_set('session.cookie_domain', $option['domain']);
            
        if (isset($option['expire'])){
           ini_set('session.gc_maxlifetime', $option['expire']);
                ini_set('session.cookie_lefetime', $option['expire']);
        }
            
        if(isset($option['use_trans_sid']))   ini_set('session.use_trans_sid', $option['use_trans_sid']?1:0);
        if(isset($option['use_cookies']))     ini_set('session.use_cookies', $option['use_cookies']?1:0);
        if(isset($option['cache_limiter']))   session_cache_limiter($option['cache_limiter']);
        if(isset($option['cache_expire']))    session_cache_expire($option['cache_expire']);
        
        if (isset($option['handler'])&&('files' !== $option['handler'])){
            if (class_exists('Gaea\Session\Handler\\'.ucfirst($option['handler']))){
                $class_name = 'Gaea\Session\Handler\\'.ucfirst($option['handler']);
                
                $this->_handler = new $class_name(Core::config('session',$this->_handler));
                
                session_set_save_handler(
                    array(&$this->_handler,'open'),
                    array(&$this->_handler,'close'),
                    array(&$this->_handler,'read'),
                    array(&$this->_handler,'write'),
                    array(&$this->_handler,'destroy'),
                    array(&$this->_handler,'gc')                    
                );
                
            }
        }
        
    }
    
    private function _set_prefix($prefix)
    {
        $this->_prefix = $prefix;
    }
    /**
     * 获取前缀
     * @return Ambigous <string, unknown>
     */
    public function get_prefix()
    {
        return $this->_prefix;
    }
    
    /**
     * 启动session
     */
    public function start()
    {
        if (!session_id())
        session_start();
    }
    
    
    public function pause()
    {
        session_write_close();
    }
    
    public function destroy()
    {
        $_SESSION = array();
        session_unset();
        session_destroy();
    }
    
    public function regenerate()
    {
        session_regenerate_id();
    }
    
    public function is_set($name)
    {
        return $this->_prefix?isset($_SESSION[$this->_prefix.$name]):isset($_SESSION[$name]);
    }
    
    public function set($name,$value=''){
        if (strpos($name, '.')){
            list($name1,$name2)     =   explode('.', $name);
            if ($this->_prefix){
                $_SESSION[$this->_prefix.$name1][$name2] = $value;
            }else{
                $_SESSION[$name1][$name2] = $value;
            }
        }else{
            if ($this->_prefix){
                $_SESSION[$this->_prefix.$name] = $value;
            }else{
                $_SESSION[$name] = $value;
            }
        }
    }
    
    public function get($name = ''){
        if (''===$name){
            return $_SESSION;
        }else{
            if (strpos($name, '.')){
                list($name1,$name2)     =   explode('.', $name);
                return $this->_prefix ?
                    (isset($_SESSION[$this->_prefix.$name1][$name2])?$_SESSION[$this->_prefix.$name1][$name2]:null)
                :
                    (isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null);                  
            }else{
                return $this->_prefix ?
                    (isset($_SESSION[$this->_prefix.$name])?$_SESSION[$this->_prefix.$name]:null)
                :
                    (isset($_SESSION[$name])?$_SESSION[$name]:null);
            }
        }
    }
    
    public function delete($name = ''){
        if ('' === $name){
            $this->destroy();
        }else{
            if (strpos($name, '.')){
                list($name1,$name2) = explode('.', $name);
                if ($this->_prefix){
                    unset($_SESSION[$this->_prefix.$name1][$name2]);
                }else{
                    unset($_SESSION[$name1][$name2]);
                }
            }else{
                if ($this->_prefix){
                    unset($_SESSION[$this->_prefix][$name]);
                }else{
                    unset($_SESSION[$name]);
                }
            }
        }
    }
    
    
}