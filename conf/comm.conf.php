<?php
if(!defined('IN_SYS')) {
    header("HTTP/1.1 404 Not Found");
    die;
}

 //自定义日志配置
 Core::$vars['ip_acl'] = array("112","34234");
