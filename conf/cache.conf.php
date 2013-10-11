<?php
/**
 *	@name:	inc.Mem.php
 *	@todo: 	Memcache 配置文件
 *	@author:firmy
 *
 */
if (! defined ( 'IN_SYS' )) {
	header ( "HTTP/1.1 404 Not Found" );
	die ();
}


Core::$configs ['mem'] = array ();

/**
 * memcache servers configure
 */
Core::$configs ['mem'] ['mm'] ['servers'] = array (
		array (
				'host' => '127.0.0.1',
				'port' => 11211,
				'weight' => 75 
		) 
);

?>