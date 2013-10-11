<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@name:	class.Mem.php
 *	@desc: 	memcache 操作类库
 	<code>
 		$configs = array();
 		$configs['server'][0] = array(
 									'host'	=> '192.168.1.38',
 									'port'	=> 22222,
 									'weight'=> 11
 								);
 		$mem = & new Mem($configs['server']);
 		$mem->Put('test', 'jingki', 10);
 		$rs = $mem->Get('test');
 		echo $rs;
 	</code>
 */
 
 class Mem {
 	
	private $memCache;
	
	private $compression = false;
	private $compressMinSize = 20000;
	private $compressLevel	 = 0.2;
	
	const persistent = true;
	const weight	 = 10;
	const timeout	 = 1;
	const retryInterval = 15;
	
	public function Mem(array $configServerArray) {
		if(is_array($configServerArray)) {
			foreach($configServerArray as $val) {
				$this->AddServer($val);
			}
		}
	}
	
	protected function _Connect() {
		if (!$this->memCache) {
			$this->memCache = new Memcache;
		}
		if($this->compression) {
			$this->memCache->setCompressThreshold(20000, 0.2);
		}
	}
	
	/**
	 * @name AddServer
	 * @desc 添加一个server
	 *
	 */
	public function AddServer($arr) {
		$persistent = isset($arr['persistent']) && $arr['persistent'] 
						? $arr['persistent'] 
						: self::persistent;
		$timeout = isset($arr['timeout']) && $arr['timeout'] 
						? $arr['timeout'] 
						: self::timeout;
		$retry_interval = isset($arr['retry_interval']) && $arr['retry_interval'] 
						? $arr['retry_interval'] 
						: self::retryInterval;
		$this->_Connect();
		$this->memCache->addServer($arr['host'], $arr['port'],$persistent,$arr['weight']
								, $timeout, $retry_interval);
	}
	
	/**
	 * @name Get
	 * @desc 取出某个值
	 * @param string $key
	 * @return mixed
	 *
	 */
 	public function Get($key, $json_decode = true) {
		$value = $this->memCache->get ( $key );
		if ($value === FALSE) {
			return FALSE;
		} else {
			if ($json_decode === TRUE) {
				return json_decode ( $value, true );
			} else {
				return $value;
			}
		}
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
	public function Put($key, $val, $expire=864000) {
		//$this->_Connect();
		return $this->memCache->set($key, json_encode($val), false, intval($expire));
	}
	
	/**
	 * @name Del
	 * @desc 删除某个值
	 * @param string $key
	 * @return bool
	 *
	 */
	public function Del($key) {
		//$this->_Connect();
		return $this->memCache->delete($key);
	}

	/**
	 * @name Status
	 * @desc 查看状态
	 * @return array
	 *
	 */
	public function Status() {
		//$this->_Connect();
		return $this->memCache->getExtendedStats();
	}
	
	/**
	 * @name Flush
	 * @desc 清除所有memcache数据，慎用！
	 *
	 */
	public function Flush() {
		return $this->flush($this->memCache);
	}

	public function __destruct() {
		if ($this->memCache) {
			$this->memCache->close();
		}
	}
 }
?>