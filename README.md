# gaea
a php framwork based on flightphp

-------

##框架



###基础

####环境要求
php 5.4+
####安装
+ WebServer，具体配置方法参见[Flight按照要求](http://flightphp.com/install)
+ Php-Memcached扩展，具体配置方法参见[pecl](http://php.net/manual/zh/book.memcached.php)


###结构设计
####项目目录结构

|-app/	
|	&emsp;|-configs/	配置文件夹			
|	&emsp;|-bootstrap.php	引导文件			
|-src/		代码目录	
|	&emsp;|-libs/		类库方法库		
|	&emsp;|	&emsp;|-classes/		
|	&emsp;|	&emsp;|-functions/	
|	&emsp;|-modules/	模块文件夹			
|	&emsp;|	&emsp;|-xxxmodules/		
|	&emsp;|	&emsp;|	&emsp;|-controller/		
|	&emsp;|	&emsp;|	&emsp;|-libs/		
|	&emsp;|	&emsp;|	&emsp;|-model/		
|	&emsp;|-templates/		
|	&emsp;|	&emsp;|-default/		
|-temp/		临时目录	
|	&emsp;|-caches/		
|	&emsp;|-compiled/		
|	&emsp;|-logs/		


####url访问
框架采用mvc设计模式开发，采用单一入口方式部署和访问。
访问示例

    http://yourdomain.com/module/controller/action/param1/param2
    
其中将会访问

    /src/module/controller.php下的action方法
    
    
####引导文件

/app/bootstrap.php引导文件完成了命名空间的注册，扩展模版引擎，以及路由的注册。		
其中常量定义：		
ROOT_PATH&emsp;系统根目录		
APP_PATH&emsp;app目录			
TEMP_PATH&emsp;临时目录		
SSRC_PATH	&emsp; 系统代码目录		
WEB_PATH	&emsp; 系统WEB资源目录	

----	


###系统配置

所有的配置文件均已数组形式返回。具体配置文件项在/app/configs下			
|-db.php	数据库(Mysql)配置文件	
|-memcache.php		 缓存(Memcache)配置文件	
|-route.php		路由配置文件	
|-smarty.php	模版引擎配置文件	
|-system.php	系统配置文件	


####系统配置

结构为一维数组，各项参数见注释
    
    return  array(
    	'theme'     =>      'default',	 //主题
    	'debug'     =>      true,       //是否调试信息
    	'log_level' =>      'DEBUG',    //日志打印级别EMERGENCY|ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG
    	'cache_expire'  =>  3600,       //缓存时间,单位秒
    	'prefix'  =>  'sobey_',   //存储键值前缀，包含db和cache
    );    
    


####数据库配置

结构为二维数组，默认使用default，可参考default结构配置多个数据库配置如test

    
    return  array(
        'default'   =>  array(
                'database_type'=>'mysql',
                'database_name'=>'ecshop',
                'server'=>'127.0.0.1',
                'username'=>'root',
                'password'=>'123456',
                //其他可选参数参见php手册
                //http://www.php.net/manual/en/pdo.setattribute.php
                'port'=>3306
        ),
        //test配置非系统默认
        'test'		=>	array(
        	//.........
        ),
    );

####Memcached配置

结构为二维数组，默认使用default，可参考default结构配置多个数据库配置如test
    

    return array(
    	'servers'=>array(
        	//array('域名','端口','权重')
       	 array('127.0.0.1','11211'),
    	),
    	//options为Memcached::setOptions参数
    	'options'=>array(
    		//Memcached::OPT_HASH => Memcached::HASH_MURMUR,
    	 	//Memcached::OPT_PREFIX_KEY => "widgets"
    	),
    ); 
 
 
####smarty配置

结构为二维数组，默认使用default，可参考default结构配置多个数据库配置如test
    

    return array(
    	'servers'=>array(
        	//array('域名','端口','权重')
       	 array('127.0.0.1','11211'),
    	),
    	'options'=>array(
    		//Memcached::OPT_HASH => Memcached::HASH_MURMUR,
    	 	//Memcached::OPT_PREFIX_KEY => "widgets"
    	),
    ); 
    
    
 
####模块modules

一个模块的基本结构为，请遵循以下的规则：    		
|-src/		代码目录		
|	&emsp;|-modules/	模块文件夹				
|	&emsp;|	&emsp;|-xxxmodules/        	模块根目录        
|	&emsp;|	&emsp;|	&emsp;|-api/	接口文件目录	    
|	&emsp;|	&emsp;|	&emsp;|-controller/		控制器文件夹
|	&emsp;|	&emsp;|	&emsp;|-libs/		工具类库    
|	&emsp;|	&emsp;|	&emsp;|-model/	   模型类库   

 ----	
 		
 
##项目
----

###开发技巧

####Core文件

+ Core::config($file,[$key,[$dafault = false,[$reload = false]]])	获取配置文件
+ Core::load_func($func,[$path= '']) 加载函数	
+ Core::log($module,$level,$message,[$context = array()]) 日志记录
+ Core::db([$key='default',[$option=array()]]) 获取db实例
+ Core::memcached([$key='default',[$servers=array(),[$options=array()]]]) 获取memcached实例

####项目函数库
一些项目的全局函数位于/src/libs/functions/global.php,该文件会被自动加载。
自己可新建模块的函数库并使用Core::load_func()引入使用


####模版
模版文件位于： /src/teplate/{风格}/sso(模块)下，为smarty语法文件		    