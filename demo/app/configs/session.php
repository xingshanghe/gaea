<?php
/** 
* session 配置文件
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date 2015年3月26日下午3:15:05
* @source session.php 
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 
return array(
    'max_lifetime'  =>  1800,
    'handler'       =>  'files',//[files|memcached]
    'option'        =>  array(
                    'memcached' =>array(
                        'servers'=>array(
                            //array('域名','端口','权重')
                            )//
                        ),
                    ),//
);