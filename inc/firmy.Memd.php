<?php
/**
 *	@name:	class.Memd.php
 *	@desc: 	memCached 操作类库
 *	<code>
 *		$configs = array();
 *		$configs['server'][0] = array(
 *									'host'	=> '192.168.1.38',
 *									'port'	=> 22222,
 *									'weight'=> 11
 *								);
 *		$memd = & new Memd($configs['server']);
 *		$memd->Put('test', 'jingki', 10);
 *		$rs = $memd->Get('test');
 *		echo $rs;
 *	</code>
 */
 
 class Memd {
 	
	private $memCached;
	
	
	public function __construct(array $configServerArray) {
		$this->memCached = new Memcached();
		$this->memCached->addServers($configServerArray['servers']);
                if(!empty($configServerArray['options']) &&　is_array($configServerArray['options'])){
                     $opt = $configServerArray['options'];
                     if(isset($opt['OPT_DISTRIBUTION:DISTRIBUTION_CONSISTENT'])) $this->memCached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
                     if(isset($opt['OPT_LIBKETAMA_COMPATIBLE:TRUE'])) $this->memCached->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, TRUE );
                     if(isset($opt['OPT_HASH:HASH_MD5'])) $this->memCached->setOption(Memcached::OPT_HASH, Memcached::HASH_MD5 );  // 这个跟下面一个不冲突的，只有配置文件中定义了这里才会设置
                     if(isset($opt['OPT_HASH:HASH_CRC'])) $this->memCached->setOption(Memcached::OPT_HASH, Memcached::HASH_CRC );
                     if(isset($opt['OPT_NO_BLOCK:TRUE'])) $this->memCached->setOption(Memcached::OPT_NO_BLOCK, TRUE );
                     if(isset($opt['OPT_TCP_NODELAY:TRUE'])) $this->memCached->setOption(Memcached::OPT_TCP_NODELAY, TRUE );
                     if(isset($opt['OPT_PREFIX_KEY'])) $this->memCached->setOption(Memcached::OPT_PREFIX_KEY, $opt['OPT_PREFIX_KEY'] );
                     if(isset($opt['OPT_COMPRESSION:TRUE'])){
                          $this->memCached->setOption(Memcached::OPT_COMPRESSION, TRUE );
                     }else{
                          $this->memCached->setOption(Memcached::OPT_COMPRESSION, FALSE );
                     }
                }else{ // 默认设置不压缩，压缩了长子符串存不了
                      $this->memCached->setOption(Memcached::OPT_COMPRESSION, FALSE );       
                }
		
	}
	
	/**
	 * @name AddServers
	 * @desc 添加一组server
	 *
	 */
	public function AddServers($arr) {
		$this->memCached->addServers($arr);
	}
	
	/**
	 * @name Get
	 * @desc 取出某个值
	 * @param string $key
	 * @return mixed
	 *
	 */
	public function Get($key, $json_decode=true) {

		$key = (defined('IS_TEST') && IS_TEST) ? md5('test_'.$key) : $key;
		$data = $this->memCached->get($key);
		return $json_decode ? json_decode($data, true) : $data;
	}

	/**
	 * @name Put
	 * @desc 存储一条数据
	 * @param string $key
	 * @param mixed $val
	 * @param int $expire 有效期
	 * @return bool
	 *
	 */
	public function Put($key, $val, $expire=3600, $json_encode=true) {
		$key = (defined('IS_TEST') && IS_TEST) ? md5('test_'.$key) : $key;
		return $this->memCached->set($key, ($json_encode?json_encode($val):$val), intval($expire));
	}

	/**
     * @name GetCache
     * @desc 兼容每个cache产品的使用，统一使用其取cache值
     * @param string $key (缓存key名)
     * @param boolean $json_decode (是否解析json)
     * @return string (缓存值字符窜)
	 */
	public function GetCache( $key, $json_decode=TRUE ){

		$result = $this->Get($key, $json_decode);	//调用原版本的取缓存方式
		$result = $result ? $result : '';
		return $result;
	}

	/**
  	 * @name SetCache
  	 * @desc 兼容擦车产品的使用，统一使用其设置缓存
  	 * @param string $key (缓存key名)
  	 * @param unknown $value (设置的缓存值)
  	 * @param number $expiration (设置缓存过期时间，0为永不过期)
  	 * @param boolean $json_encode (是否转换成json)
  	 * @return boolean (设置是否成功)
	 */
	public function SetCache( $key, $value, $expiration = 2592000, $json_encode=TRUE ){

		$result = FALSE; //初始化结果变量，是否设置缓存成功
		if( is_numeric($expiration) ){
			$flag = $this->Put($key, $value, $expiration, $json_encode);	//调用原版本的设置缓存方式
			$result = empty($flag) ? $result : TRUE;
		}
		return $result;
	}
	/**
	 * @name Del
	 * @desc 删除某个值
	 * @param string $key
	 * @return bool
	 *
	 */
	public function Del($key) {
		return $this->memCached->delete($key);
	}
	
	public function getResultCode() {
		return $this->memCached->getResultCode();
	}

	public function getResultMessage() {
		return $this->memCached->getResultMessage();
	}
	public function getServerList () {
		return $this->memCached->getServerList();
	}
	public function getOption ($opt) {
		return $this->memCached->getOption($opt);
	}
 }
?>