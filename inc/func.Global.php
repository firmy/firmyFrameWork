<?php
if(!defined('IN_SYS')) {
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@File name:	func.Global.php
 *	@desc:	全局函数
 *
 */
if(!defined('IN_SYS')) {
	header("HTTP/1.1 404 Not Found");
	die;
}

/**
* @desc 自动加载类文件
* @return void
*/
function __autoload($class="") {
	$class_file = ROOT_DIR.'inc/class.' . $class . '.php';
	if(class_exists($class_file,false)) {
		return;
	} elseif (!is_readable($class_file)) {
		echo "unable to read class file";
	} else {
		load($class_file);
	}
}

/**
* @desc load app
* 如果app name是多个单词的则每个单词以下划线分割，程序会自动加载对应的类
* @param string $appName application name
*/
function load_app($appName='', $method = '') {
	$_GET[ACTION] = !empty($_GET[ACTION]) ? ucfirst($_GET[ACTION]) : DEFAULT_ACTION;
	$_GET[DO_METHOD] = !empty($_GET[DO_METHOD]) ? ucfirst($_GET[DO_METHOD]) : DEFAULT_DO_METHOD;
	if(strpos($_GET[ACTION], '_')!==false) {
		$_GET[ACTION] = str_replace('_',' ', $_GET[ACTION]);
		$_GET[ACTION] = str_replace(' ','', ucwords($_GET[ACTION]));
	}
	if(strpos($_GET[DO_METHOD], '_')!==false) {
		$_GET[DO_METHOD] = str_replace('_',' ', $_GET[DO_METHOD]);
		$_GET[DO_METHOD] = str_replace(' ','', ucwords($_GET[DO_METHOD]));
	}
	$appName = $appName ? $appName : $_GET[ACTION];
	$appName = ucfirst($appName);
	$method = $method ? $method : $_GET[DO_METHOD];
	$fileName = APP_DIR.'app.'.$appName.'.php';
	load($fileName);
	$object = new $appName;
	$object->$method();
}

/**
* @desc load conifgure file
*/
function load_cfg($cfgName='System') {
	load(ROOT_DIR.'config/inc.'.ucfirst($cfgName).'.php');
}

/**
* @name load
* @desc include a file
* @param string $file file name
* @return void
*/
function load($file) {
	if(file_exists($file)) {
		include_once($file);
	} else {
		throw new JException("file not exists");
	}
}

/**
* @name template
* @return string
*
*/
function template($name) {
	return sprintf("%s/tpl.%s.php", TPL_DIR, $name);
}


/**
* @name get_ip
* @desc get browser's ip
* @return string
*
*/
function get_ip() {
	if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$hdr_ip = stripslashes($_SERVER['HTTP_CLIENT_IP']);
	} else {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$hdr_ip = stripslashes($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			$hdr_ip = stripslashes($_SERVER['REMOTE_ADDR']);
		}
	}
	return $hdr_ip;
}

function j_stripcslashes (&$data='') {
	//check gpc
	if (!get_magic_quotes_gpc()) {
	return;
}
	if(is_array($data)) {
		foreach($data as &$v) {
			if(is_array($v)) return j_stripcslashes($v);
			else $v = stripcslashes($v);
		}
	} elseif($data) {
		return stripcslashes($data);
	} else {
		j_stripcslashes($_GET);
	}
}

function _debug($msg, $bool=false) {
	header($msg, false);
}

/**
 * 记录数据
 */
function logs_data($str){
	global $log_f;
	if (!is_object($log_f)) {
		$log_f=fopen(ROOT_DIR.'log/'.date('Ymd',$_SERVER['GET_TIME']).'.txt','a+');
	}
	$data="\t\n";
	$data.=is_array($str)?implode(",", $str):$str;
	fwrite($log_f,$data);
}

/**
 * 判断id是否存在，
 * 跳转到指定页面
 */
function jumpId($vid){
	$info = Videos :: Video_Info($vid, false);
	if (empty($info)){
		return false;
	}
	logs_data('转接搜索:'.$search.'--'.get_ip().':::'.$_SERVER['HTTP_USER_AGENT']);

	header("location:http://3g.56.com/index.php?action=View&vid=".$vid);
	exit;
}

 /**
 *@name notice_debug
 @desc: 提示debug信息，
 @msg:输出的内容
 $type:定位
 @param */
 function notice_debug($msg,$type=''){
	if(DEBUG){
		if(is_array($msg)){
			echo '<pre>';print_r($msg);	
		}else{
			echo '<br>',$msg;	
		}
			
	}
	if(!is_array($msg)){
		logs($msg,'data_err');
	}
 }


/**
 *@name logs
 @desc: 自定义日志收集
 @param : $msg : 日志内容
 $type: 日志类型， 详细见Core::$vars['log'] 定义
 */
function logs($msg,$type=''){
	$filename = isset(Core::$vars['log'][$type]['filename'])?Core::$vars['log'][$type]['filename'] : $_SERVER['SERVER_DOMAIN'];
	if($filename ){ // 目录还不可写 先不记录
		$msg = sprintf("%s\t%s\t%s",$_SERVER['GET_URI'],$_SERVER['SERVER_PORT'],$msg);
		Logger::log($msg,$filename);
	}	
}


/**
*获取时间 毫秒
*/
 function get_microtime() {
	list($usec, $sec) = explode(' ', microtime());
	return (( float )$usec + ( float )$sec);
 }

/**
* 计算程序执行时间，毫秒
*/
 function spent($stop_time, $start_time) {
	return round(($stop_time - $start_time) * 1000, 2);
}




	/**
	 * 
	 * @param unknown $pre 缓存前缀
	 * @param unknown $salt 获取数据方法的参数
	 * @param string $istest 是否区分测试服
	 * @param string $product 是否区分产品
	 * @return string
	 */
	function getCacheKey($pre,$salt,$istest="",$product=""){
		if(!is_array($salt)){
			$res[] = $salt;
		}else {
			$res = $salt;
		}
		if(is_array($res) && !empty($res)){
			foreach($res as $k=> $v){
				$arr[$k] = is_array($v)?implode('@',$v):$v;
			}
			$arr = implode('@',$arr);
		}
		if(!isset(Core::$vars['cacheKeyPre'][$pre]) || empty(Core::$vars['cacheKeyPre'][$pre])){
			$msg = "invalid cachekey pre:{$pre},plz config it in config/inc.conf.php";
			die($msg);
		}else{
			$pre = Core::$vars['cacheKeyPre'][$pre]; //配置内容见config/inc.conf.php
		}
		$key = $pre.$arr;
		(!empty($istest))?$key .= "@test":"";
		(!empty($product))?$key .= "@".$product:"";
		
		if(strlen($key)>32){
//			die($key.":key's length can not be more than 32,please chk");
		}
		return $key;
		
	}

	
	
	/**
	 * @desc 把缓存按长短缓存方式存储,区分kt和redis
	 * @param unknown $key 缓存key
	 * @param unknown $data 缓存数据体
	 * @param unknown $cacheType 缓存类型,[redis[kt]]
	 * @param string $cacheTime 缓存时长 默认24小时
	 * @return boolean true
	 */
	 function SetCacheData($key,$data,$cacheType,$cacheTime="86400"){
		$timeKey = $key."_time";
		if($cacheType == 'redis'){
			Core::InitRedis();
			Core::$redis->SetKey($key);
			Core::$redis->setex($data,$cacheTime*5);
			Core::$redis->setKey($timeKey);
			Core::$redis->setex("1",$cacheTime);
		}else{
			Core::InitMemd();
			Core::$kt->Put($key,$data,$cacheTime*5);
			Core::$kt->Put($timeKey,"1",$cacheTime);
		}
		return true;
	}
	
	/**
	 * @desc 
	 * @param unknown $key 缓存key
	 * @param unknown $cacheType 缓存类型[redis[kt]]
	 * @param string $useLongCache 是否使用长缓存,在短缓存失效,又取不到实时数据的情况下,直接使用长缓存
	 * @return string 缓存内容数据体
	 */
	function getCacheData($key,$cacheType,$useLongCache=false){
		$res = "";
		$timeKey = $key."_time";
		if($cacheType == 'redis'){
			Core::InitRedis();
			Core::$redis->SetKey($timeKey);
			if(Core::$redis->Get() || $useLongCache){ //如果实时数据源没有数据,需要用到长缓存
				Core::$redis->setKey($key);
				$res = Core::$redis->Get();
			}
		}else{
			Core::InitMemd();
			if(Core::$kt->Get($timeKey) || $useLongCache ){ //如果实时数据源没有数据,需要用到长缓存 
				$res = Core::$kt->Get($key);
			}
		}
		return $res;
		
	}
	
	
	/**
	 * 获取错误类型的返回码
	 * @param unknown $errType
	 * @return unknown
	 */
	function getErrorInfo($errType){
		if(!isset(Core::$vars['error'][$errType]) || empty(Core::$vars['error'][$errType])){
			$msg = "invalid error type:{$errType},plz config it in config/inc.conf.php";
			die($msg);
		}else{
			$res = Core::$vars['error'][$errType];
		}
		return $res;
	}

	
	function getUserIdFromHex($user_hex){
		$user_id = "";
		if($user_hex){
			$user_hex = base64_decode($user_hex);
			list($_COOKIE['member_id'], $_COOKIE['pass_hex'], $_COOKIE['member_login']) = explode('|', $user_hex);
			$user_id = u_get_user_id();
		}
		return $user_id;
	}
?>
