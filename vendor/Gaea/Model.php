<?php
/** 
* 模型类
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年4月17日上午4:10:21
* @source Model.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace Gaea;


class Model
{
    private $_db;
    private $_memcached;
    
    private $_prefix;
    private $_table;
    
    private $_cached = false;    //是否使用缓存
    
    protected $_error;      //
    protected $_validate =  array();   //校验规则
    
    /**
     * @access public
     * @param array $option
     * param参数格式：
     * array(
     *  'db'        =>array(),
     *  'memcached' =>array(
     *          'servers'=>array(),
     *          'options'=>array()
     *          ),
     *  'validate'  =>array(
     *          array('验证字段1','验证规则','错误提示',['验证条件','附加规则','验证时间']),
     *          array('验证字段2','验证规则','错误提示',['验证条件','附加规则','验证时间']),
     *          ),
     *  )
     * 
     */
    public function __construct($option = array())
    {
        isset($option['validate'])?$this->set_validate($option['validate']):null;//设置校验规则
        
        isset($option['db'])?null:$option['db'] = array();
        $this->_db = $this->_init_db($option['db']);              //初始化db
        
        if (isset($option['cached'])){
            $this->set_cached($option['cached']);
        }
        if ($this->_cached){
            isset($option['memcached']['servers'])?:$option['memcached']['servers'] = array();
            isset($option['memcached']['options'])?:$option['memcached']['options'] = array();
            $this->_memcached = $this->_init_memcached($option['memcached']['servers'],$option['memcached']['options']);//初始化cache
        }
        $this->_prefix = Core::config('system','prefix');
    }
    
    /**
     * 初始化db
     * @access private
     * @return \medoo
     */
    private function _init_db($option){
        return Core::db(__CLASS__,$option);
    }
    
    /**
     * 初始化memcacheds
     * @access private
     * @return Ambigous <\sobey\Ambigous, \sobey\Memcached>
     */
    private function _init_memcached($servers,$options)
    {
        return Core::memcached(__CLASS__,$servers,$options);
    }
    
    /**
     * 数据校验函数
     * @param array $data
     * @return boolean
     */
    public function validate( $data )
    {
        return true;
    }
    
    
    //数据库级别（后期须同步更新缓存） 增
    private function _insert( $datas )
    {
        return $this->_db->insert($this->_table, $datas);
    }
    
    //数据库级别（后期须同步更新缓存） 删
    private function _delete( $where )
    {
        //TODO 缓存数据处理
        return $this->_db->delete($this->_table, $where);
    }
    
    //数据库级别（后期须同步更新缓存） 改
    private function _update($data, $where = null)
    {
        return $this->_db->update($this->_table,$data, $where);
    }
    //数据库级别（后期须同步更新缓存） 查
    //$table, $join, $columns = null, $where = null
    private function _select($join, $columns = null, $where = null)
    {
        return $this->_db->select($this->_table, $join, $columns, $where);
    }
    //数据库级别（后期须同步更新缓存） 查单条
    private function _get($join = null, $columns = null, $where = null)
    {
        
        return $this->_db->get($this->_table, $join, $columns, $where);
    }
    
    private function _count( $join, $column = null, $where = null){
        return $this->_db->count( $this->_table,$join, $column, $where);
    }
    
    /**
     * 设置model类表名
     * @access public
     * @param string $table 表名
     * @param boolean $is_truename 是否真正表名，true为真，不再叠加前缀
     */
    public function set_table( $table ,$is_truename = false){
        $this->_table = $is_truename?$table:$this->_prefix.$table;
    }
    /**
     * 获取model类表名
     * @access public
     * @return Ambigous <string, string>
     */
    public function get_table()
    {
        return $this->_table;
    }
    
    /**
     * 获取表名前缀
     * @access public
     * @return Ambigous <string, string>
     */
    public function get_prefix()
    {
        return $this->_prefix;
    }
    
    /**
     * 设置是否使用缓存并初始化memcached
     * @access public
     * @param boolean $cached
     */
    public function set_cached( $cached,$option = null)
    {
        $this->_cached = $cached;
        if ($this->_cached && $this->_memcached){
            isset($option['memcached']['servers'])?:$option['memcached']['servers'] = array();
            isset($option['memcached']['options'])?:$option['memcached']['options'] = array();
            $this->_memcached = $this->_init_memcached($option['memcached']['servers'],$option['memcached']['options']);//初始化cache
        }
    }
    
    /**
     * 获取是否使用缓存
     * @access public
     * @return boolean 
     */
    public function get_cached()
    {
        return $this->_cached;
    }
    
    /**
     * 获取异常，主要用于数据校验
     * @access public 
     * @return array 
     */
    public function get_error()
    {
        return $this->_error;
    }
    
    /**
     * 设置校验规则
     * @param array $validate
     */
    public function set_validate($validate)
    {
        return $this->_validate = $validate;
    }
    
    /**
     * 获取数据库操作类，
     * 此方法仅用调试后期废弃,非调试请勿使用
     * @return medoo
     */
    public function get_handler()
    {
        return $this->_db;
    }
    
    
    public function query($sql)
    {
        return $this->_db->query($sql);
    }
    
    
    public function __destruct(){}
    
    
    
}