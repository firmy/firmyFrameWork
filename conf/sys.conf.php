<?php
if (! defined ( 'IN_SYS' )) {
	header ( "HTTP/1.1 404 Not Found" );
	die ();
}
/**
 * all configuration
 */

define("ACTION", 'action');
define("DO", "do");
define('APP_DIR', ROOT_DIR.'app/');
define('DEFAULT_TEMPLATE', ROOT_DIR.'temp/default/');
define('DEFAULT_ACTION','main'); 
define('DO_METHOD','do');
define('DEFAULT_DO_METHOD','Run');


Core::$configs = array ();
Core::$configs ['define'] = array ();
                      
// $configs
/**
 * ******************************************
 * database servers configure
 * *******************************************
 */
Core::$configs ['db'] ['event'] = array (
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'test',
		'_charset' => 'utf8' 
);
