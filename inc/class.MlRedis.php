<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
  * @name Redis 子类
  */
class MlRedis extends Redis {

	private $key = null;
	private $last_key = null;
	private $_connect = array();
	
	public function __construct($key = NULL) {
		$this->key = $key;
	}
	
	/**
	 * @desc 修改键值
	 * @param unknown_type $key
	 */
	public function SetKey($key = NULL) {
		$key = (defined('IS_TEST') && IS_TEST) ? md5('test_'.$key) : $key;
		$this->last_key = $this->key;
		$this->key = $key;
	}
	
	/**
	 * @desc 获取当前key
	 * @return unknown
	 */
	public function GetKey(){
		return $this->key;
	}
	
	/**
	 * @desc: 连接
	 * @return unknown
	 */
	public function Connect() {
		$hash = $this->Hash();
		if( (isset($this->_connect[$hash]) && $this->_connect[$hash] !== TRUE) || $this->last_key === null || $this->key !== $this->last_key) {
			$host =	Core::$configs['redis'][$hash]['host'];
			$port = Core::$configs['redis'][$hash]['port'];	
			$this->_connect[$hash] = parent::connect($host,$port,2);
		}
		return $this->_connect[$hash];
	}
	
	/**
	 * @desc: Hash  随机选取redis组
	 * @return unknown
	 */
	private function Hash() {
 		$count = count(Core::$configs['redis']);//配置
		$hash = sprintf("%u", crc32($this->key));   
		$mod = ($hash % $count);
		return $mod;
	}
	
	/**
	 * @desc 取得键对应的值
	 * @param unknown_type $decode
	 * @return unknown
	 */
	public function Get($decode = TRUE){
		$this->Connect();
		$value = parent::get($this->key);
		
		if ($value && $decode === TRUE){
			return json_decode($value,TRUE);
		}
		return $value;
	}
	
	/**
	 * @desc 为当前键设置值
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function Set($value = NULL) {
		$this->Connect();
		if($value){
			$value = json_encode($value);
		}
		return parent::set($this->key,$value);
	}
	
	/**
	 * @desc: 为当前键设置值和生存时间
	 * @param unknown_type $value
	 * @param unknown_type $expire
	 * @return unknown
	 */
	public function Setex($value = NULL,$expire = 60, $json_encode=TRUE) {
		$this->Connect();
		if($value){
			$value = json_encode($value);
		}
		//return parent::setex($this->key,$expire,$value);
		$rs =  parent::setex($this->key,$expire,$value);
		if(!$rs){
			logs('redis','Setex  '.$this->key.'写缓存失败<br>');
		}
		return $rs;
	}
	
	/**
	 * @desc 删除键值
	 * @return unknown
	 */
	public function Delete(){
		$this->Connect();
		return parent::delete($this->key);
	}

	
	/**
	 * @desc 在名称为key的list左边（头）添加一个值为value的 元素
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function IPush($value = NULL){
		$this->connect();
		if($value){
			$value = json_encode($value);
		}
		return parent::lPush($this->key,$value);
	}
	
	/**
	 * @desc 输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
	 * @return unknown
	 */
	public function IPop(){
		$this->connect();
		$value= parent::lPop($this->key);
		if ($value){
			return json_decode($value,TRUE);
		}
		return $value;
	}
	
	/**
	 * @desc 在名称为key的list右边（尾）添加一个值为value的 元素
	 * @param unknown_type $value
	 * @return unknown
	 */
	public function RPush($value = NULL){
		$this->connect();
		if($value){
			$value = json_encode($value);
		}
		return parent::rPush($this->key,$value);
	}
	
	/**
	 * @desc 输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
	 * @return unknown
	 */
	public function RPop() {
		$this->connect();
		$value = parent::rPop($this->key);
		if ($value) {
			return json_decode($value,TRUE);
		}
		return $value;
	}
	
	/**
	 * @desc 输出名称为key的list左(头)起/右（尾）起的第一个元素，删除该元素
	 * @return unknown
	 */
	public function Incr() {
		$this->connect();
		$value = parent::incr($this->key);
		return $value;
	}
	
	/**
	 * @desc 返回名称为key的list有多少个元素
	 * @return unknown
	 */
	public function LSize() {
		$this->connect();
		$value = parent::lSize($this->key);
		return $value;
	}
	
	/**
	 * @desc 返回名称为key的list中start至end之间的元素（end为 -1 ，返回所有）
	 * @param unknown_type $start
	 * @param unknown_type $end
	 * @return unknown
	 */
	public function LGetRange($start = 0,$end = -1) {
		$this->connect();
		$value = parent::lGetRange($this->key,$start,$end);	
		if ($value && is_array($value)){
			foreach ($value as & $v){
				$v = json_decode($v,TRUE);
			}
		}
		return $value;
	}
	
	/**
	 * 给key重命名
	 * $redis->Set('x', '42');
	 * $redis->Rename('x', 'y');
	 * $redis->Get('y'); // → 42
	 * $redis->Get('x'); // → `FALSE`
	 * @return unknown
	 */
	public function Rename(){
		$this->connect();
		$newkey = $this->key.".old";
		return parent::renameKey($this->key,$newkey);
	}
}


