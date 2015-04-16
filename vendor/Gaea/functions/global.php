<?php/** * 全局函数* * @author XingShanghe<xingshanghe@gmail.com>* @date 2015-3-16 下午4:44:03 * @source global.func.php* @version 2.0.0 * @copyright  Copyright 2015 sobey.com */ /** * 浏览器友好的变量输出 *  * 修改了thinkphp的dump函数 *  * @param mixed $var 变量 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串 * @param string $label 标签 默认为空 * @param boolean $strict 是否严谨 默认为true * @return void|string */function dump($var, $echo=true, $label=null, $strict=true) {    $label = ($label === null) ? '' : rtrim($label) . ' ';    if (!$strict) {        if (ini_get('html_errors')) {            $output = print_r($var, true);            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';        } else {            $output = $label . print_r($var, true);        }    } else {        ob_start();        var_dump($var);        $output = ob_get_clean();        if (!extension_loaded('xdebug')) {            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';        }    }    //增加追踪代码    $debug_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,1);    $contextinfo = array_shift($debug_info);    $output = $contextinfo['file'].'('.$contextinfo['line'].')'.$output;        if ($echo) {        echo($output);        return null;    }else        return $output;}/** * 获取客户端IP地址 *  * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） * @return mixed */function get_client_ip($type = 0,$adv=false) {    $type       =  $type ? 1 : 0;    static $ip  =   NULL;    if ($ip !== NULL) return $ip[$type];    if($adv){        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);            $pos    =   array_search('unknown',$arr);            if(false !== $pos) unset($arr[$pos]);            $ip     =   trim($arr[0]);        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {            $ip     =   $_SERVER['HTTP_CLIENT_IP'];        }elseif (isset($_SERVER['REMOTE_ADDR'])) {            $ip     =   $_SERVER['REMOTE_ADDR'];        }    }elseif (isset($_SERVER['REMOTE_ADDR'])) {        $ip     =   $_SERVER['REMOTE_ADDR'];    }    // IP地址合法验证    $long = sprintf("%u",ip2long($ip));    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);    return $ip[$type];}