<?php
/**
 *	@name:	common.inc.php
 *	@todo: 	初始化程序
 *	@author: firmy
 *
 */
 define("IN_SYS", true);
 define("ROOT_DIR",str_replace("\\","/",dirname(__FILE__))."/");
 define("DEBUG",true);

 include ROOT_DIR.'inc/func.Global.php';
 include ROOT_DIR.'conf/sys.conf.php';
 include ROOT_DIR.'conf/cache.conf.php';
 include ROOT_DIR.'conf/comm.conf.php';
 //require ROOT_DIR.'inc/smarty/libs/Smarty.class.php';
 //调试
 if(DEBUG) {
 	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
 		$hdr_ip = stripslashes($_SERVER['HTTP_CLIENT_IP']);
 	} else {
 		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
 			$hdr_ip = stripslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
 		} else { 
 			$hdr_ip = stripslashes($_SERVER['REMOTE_ADDR']); 
 		}
 	}
 	if(in_array($hdr_ip,Core::$vars['ip_acl'])){ //指定ip可以调试
 		ini_set('display_errors',true);
 		ini_set("error_reporting",E_ALL ^ E_NOTICE);
 	}else{//非列表内容不给调试
 		//ini_set('display_errors',false);
 	}
 } else {
 	ini_set('display_errors',false);
 }

 foreach(Core::$configs['define'] as $k=>$v) {
    define(strtoupper($k), $v);
 }
Core::$configs['define']=null;	//free
Core::$vars['amp'] = '&';
load_app();

