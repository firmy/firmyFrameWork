<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@name:	class.JException.php
 *
 */
 class JException extends Exception {
 	
 	public function JExcepiton ($message, $code=0) {
 		parent::__construct($message, $code);
 	}
 	
 	/**
 	 * @todo 输出错误的详细信息
 	 * @param int $exit 输出一条错误信息后是否退出程序
 	 * @return void
 	 */
 	public function ShowErrorMessage($exit = 0) {
 		$out = array(0, $this->getCode(), $this->getMessage());
 		$exit && (print json_encode($out));
 	}
 	
 }
?>