<?php
if(!defined('IN_SYS')) 
{
	header("HTTP/1.1 404 Not Found");
	die;
}
/**
 *	@name:	class.File.php
 *	@author firmy
 *	@desc: 	文件操作
 	<code>
 		$file = "./test.txt";
 		$content = "test";
 		$len = File::Write($file, $content);
 		if(strlen($content)!==$len) {
 			echo 'error';
 		}
 	</code>
 *
 */
 class File {
 	
 	/**
 	 * @name Write
 	 * @access public
 	 * @desc 写一个文件
 	 * @param string $fileName 文件名（应包括完整路径）
 	 * @param string $fileContent 写入的文件内容
 	 * @return int 写入的长度
 	 * 
 	 */
 	public static function Write($fileName, $fileContent) {
 		self::CheckDir($fileName);
 		return file_put_contents($fileName, $fileContent);
 	}
 	
 	
 	/**
 	 * @name Append
 	 * @access public
 	 * @desc 向某个文件中附加数据
 	 * @param string $fileName 文件名（应包括完整路径）
 	 * @param string $fileContent 写入的文件内容
 	 * @return int 写入的长度
 	 * 
 	 */
 	public static function Append($fileName, $fileContent) {
 		self::CheckDir($fileName);
 		return file_put_contents($fileName, $fileContent, FILE_APPEND);
 	}
 	
 	/**
	 * @access public
	 * @desc 检查路径是否存在，否则创建（需要相应权限）
	 * @param string $dir 目录路径
	 * @return void
	 * 
	 */
	public static function CheckDir($dir) {
		$dir_curr = '';
		$names = explode('/',$dir);
		foreach($names as $v) {
			if(strpos($v, ':')) {	//win platform
				continue;
			}
			if($v=='.'||$v=='..') {
				$dir_curr .= $v;
			}else{
				if(strpos($v,'.')) {
					continue;
				}else{
					$dir_curr .= '/'.$v;
				}
			}
			if(!is_dir($dir_curr)) {
				mkdir($dir_curr,0777,true);
			}
		}
	}
 }
?>