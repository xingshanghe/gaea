<?php
/** 
* Session Memcached处理类
* 
* 
* 注：此类还未经测试，请谨慎使用
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年3月26日上午10:45:45
* @source Memcached.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
namespace Gaea\Session\Handler;
class Memcached
{
    private $_max_lifetime;
    protected $_handler = null;
    
    public function __construct($option){
        $this->_max_lifetime = ini_get('session.gc_maxlifetime');
        //对session单独指定session服务器
        $servers = isset($option['servers'])?$option['servers']:array();
        $options = isset($option['options'])?$option['options']:array();
        $options['_prefix'] = '';
        $this->_handler = Core::memcached('session',$servers,$options);
    }
    /**
     * 打开Session
     * @access public
     * @param string $save_path
     * @param mixed $session_name
     * @return boolean
     */
    public function open($save_path,$session_name){
        return true;
    }
    /**
     * 关闭session
     * @return boolean
     */
    public function close(){
        $this->gc($this->_max_lifetime);
        $this->_handler->quit();
        $this->_handler = null;
        return true;
    }
    /**
     * 读取session
     * @param unknown $session_id
     */
    public function read($session_id){
        return $this->_handler->get($session_id);
    }
    /**
     * 写入session
     * @param unknown $session_id
     * @param unknown $session_data
     */
    public function write($session_id,$session_data){
        return $this->_handler->set($session_id,$session_data,$this->_max_lifetime);
    }
    
    /**
     * 删除session
     * @param unknown $session_id
     */
    public function destroy($session_id){
        return $this->_handler->delete($session_id);
    }
    /**
     * session垃圾回收
     * @param int $session_max_lifetime
     * @return boolean
     */
    public function gc($session_max_lifetime){
        return true;
    }
}