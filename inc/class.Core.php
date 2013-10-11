<?php
if(!defined('IN_SYS')){
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@name:	class.Core.php
 *	@desc: 	Core类，主要用于配置变量及一些实例对象的调度
 *	@author firmy
 *
 */
class Core {

 /**
   * @access  public
   * @var array
   * @name 数据库对象
   * @example
	* Core::InitDb();
	* Core::$db['event']->dataArray("status = 1","dataline desc");
   */
 	public static $db = array();


 /**
   * @access  public
   * @var array
   * @name $redis对象
   * @example
   	   * Core::InitRedis();
   	   * $_key = "unique_redis_key";
   	   * $_cacheTime = 3600;
   	   * Core::SetKey($_key);
	   * if(empty(Core::$redis->Get())){
	   * 	//数据库相关操作..
	   *    Core::$var['data'] = "...";
	   * 	Core::$redis->Setex($_key,Core::$var['data'],$_cacheTime);
	   * }
	   *
	   * //删除key操作
	   * Core::$redis->Delete($_key);
   */
 	public static $redis = '';

 /**
   * @access  public
   * @var array
   * @name memcache对象
   * @example
   	   * Core::InitMemCache();
   	   * $_key = "unique_memcache_key";
   	   * $_cacheTime = 3600;
	   * if(empty(Core::$mem['group1']->Get($_key))){
	   * 	//数据库相关操作..
	   *    Core::$var['data'] = "...";
	   * 	Core::$mem['group1']->Set($_key,Core::$var['data'],$_cacheTime);
	   * }
	   *
	   * //删除key操作
	   * Core::$mem['group1']->Del($_key);
   */
 	public static $mem = array();

 /**
   * @access  public
   * @var array
   * @name 文件缓存对象
   * @example
	   * $_key = "unique_datacache_key";
	   * $_cacheTime = 3600;
	   * if (!(Core::$vars['list'] = Core::$dc->getData($_key,$_cacheTime)) || $_GET['i']=='c'){
	   * 		//数据库相关操作..
	   * 		Core::$vars['list'] = "...";
				Core::$dc->setData($_key,Core::$vars['list']);
	   * }
   */
 	public static $dc = '';


  /**
    * @access public
    * @var Wc类对象
    * @example
    * 	Core::InitWc();
		$valueArray = array(
				'id'=>Core::$vars['id'],
				'page'=>Core::$vars['page'],
				'sid'=>SID,
			);
		$wc_set['cacheTime'] = 300;
		$wc_set['pageName'] = "页面名称";//可选
		Wc::cache($valueArray,$wc_set,$_GET['dy']);

    */

 	public static $wc = '';

 /**
   * @access public
   * @var array
   * @name 记录config文件夹下的配置文件
   */
 	public static $configs ;

  /**
   * @access public
   * @var array
   * @name 用于tpl.xxx.php模板文件的输出--前台输出
   * @example
	   * <?php foreach(Core::$vars['list'] $data) { ?>
	   * <?php print_r($data); ?>
	   * <?php } ?>
   */
 	public static $vars = array();

 /**
   * @access public
   * @var array
   * @name 用于处理临时数据
   * @example
   	   * 函数getUserHome
	   * 注意使用后unset(Core::$temp['tempData']);
   */
 	public static $temp = array();

 	//public static $Hc = '';

    //public static $tpl = array();

        public static $kt = '';  // kt

	/**
	 * @name Startup
	 * @desc 初始化数据库连接
	 * @param array $keys 需要初始化哪些数据库
	 * @return vid
	 *
	 */
	public static function InitDb($keys=array()) {
		$p = Core::$configs['db'];
		if(empty($keys)) {
			foreach($p as $k=>&$v) {
		 		if(isset(Core::$db[$k])) continue;
		 		Core::$db[$k] = new Db( $v, array('_charset'=>$v['_charset']) );
		 	}
		}else if(is_array($keys)) {
			foreach($keys as $v) {
		 		if(isset(Core::$db[$v])) continue;
		 		Core::$db[$v] = new Db( $p[$v], array('_charset'=>$p[$v]['_charset']) );
		 	}
		}else if(is_string($keys) && !isset(Core::$db[$keys])) {
			if(!isset($p[$keys])) return;
			Core::$db[$keys] = new Db( $p[$keys], array('_charset'=>$p[$keys]['_charset']) );
		}
	}
	/**
	 * @name InitMemCache
	 * @desc 初始化MemCache连接
	 * @param array $keys 需要初始化哪些MemCache
	 * @return void
	 *
	 */
	public static function InitMemCache ($keys=array())  {
		load_cfg('Mem');	//load configure
		$p = Core::$configs['mem'];
		if(empty($keys)) {
			foreach($p as $k=>&$v) {
		 		if(isset(Core::$mem[$k])) {
		 			continue;
		 		}
		 		Core::$mem[$k] = new Mem($v['servers']);
		 	}
		} else if(is_array($keys)) {
			foreach($keys as $v) {
		 		if(isset(Core::$mem[$v]) || !isset($p[$v])) continue;
		 		Core::$mem[$v] = new Mem($p[$v]['server']);
		 	}
		} else if(is_string($keys) && !isset(Core::$mem[$keys])) {
			if(!isset($p[$keys])) return;
			Core::$mem[$keys] = new Mem($p[$keys]['server']);
		}
	}

	/**
	   * @name InitDataCache
	   * @param Array $key 初始化数据缓存
	   * @return void
	   */
	public static function InitDataCache($conf = array()){
		load_cfg('Dc'); //load configuration
		Core::$dc = new DataCache();
		if (!$conf){
				Core::$dc->setConfig(Core::$configs['Dc']);
		}elseif (is_array($conf)){
				Core::$dc->setConfig($conf);
		}elseif (is_string($conf)){
		}
	}

	/**
	   * @name InitHtmlCache
	   * @param Array $key config html cache
	   * @return void
	   */
	public static function InitHtmlCache($conf = array()){
		load_cfg('Hc'); //load configuration
		Core::$Hc = new HtmlCache();
		if (!$conf){
				Core::$dc->setConfig(Core::$configs['Hc']);
		}elseif (is_array($conf)){
				Core::$dc->setConfig($conf);
		}elseif (is_string($conf)){
		}
	}

	/**
	   * @name InitTemplateEngine
	   * @param Array $key 初始化模板引擎
	   * @return void
	   */
	public static function InitTemplateEngine($key = array()){
		load_cfg('Template');
		if (!$key){
			foreach (Core::$configs['Template'] as $k =>& $v){
				Core::$tpl[$k] = new template();
				Core::$tpl[$k]->setConfig($v);
			}
		}elseif (is_array($key)){
			foreach ($key as $v){
				Core::$tpl[$v] = new template();
				Core::$tpl[$v]->setConfig(Core::$configs['Template'][$v]);
			}
		}
	}

	/**
	   * @name InitWc
	   * @param Array $key 初始化静态页面缓存
	   * @return void
	   */
	public static function InitWc($conf = array()){
		load_cfg('Wc'); //load configuration
		if (!$conf){
				Wc::set(Core::$configs['Wc']);
		}elseif (is_array($conf)){
				Wc::set($conf);
		}elseif (is_string($conf)){

		}
	}

	/**
	 * @name InitRedis
	 * @desc 初始化redis
	 * @return void
	 */
	public static function InitRedis() {
		Core::$redis = new MlRedis();
	}
        
      
        /**
	 * @name InitMemd
	 * @desc 初始化MemCache连接
	 * @param array $keys 需要初始化哪些MemCache
	 * @return void
	 *
	 */
        public static function InitMemd ($keys=array(),$no_proxy=false)  {
		load_cfg('Mem');	//load configure
		if( (defined('NO_KT_PROXY') && NO_KT_PROXY) || $no_proxy ){ // 定义了不要代理
			if(Core::$configs['memd']["3g"]){
				Core::$kt = new Memd(Core::$configs['memd']["3g"]);
			}
		}else{
			if(Core::$configs['memd']["proxy"]){
				Core::$kt = new Memd(Core::$configs['memd']["proxy"]);
			}			
		}
	}
 }
?>