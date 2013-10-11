<?php
if(!defined('IN_SYS')) {
    header("HTTP/1.1 404 Not Found");
    die;
}
/**
 * Logger 
 * 
 * @package log facility
 */
class Logger
{    
    const LOG_DIR = '/diska/logs/';
    //const LOG_DIR = '/web/project/git/redian/';

    const PROJECT = '3g';

    //default max file size is 1G
    const MAX_SIZE = 1024000000;

    //notice:times are in seconds
    const TIMEOUT_HTTP = 3;
    //const TIMEOUT_HTTP = 0.001;

    const TIMEOUT_DB = 2;
    //const TIMEOUT_DB = 0.001;

    const TIMEOUT_REDIS = 2;
    //const TIMEOUT_REDIS = 0.001;

    const TIMEOUT_MEMCACHED = 2;
    //const TIMEOUT_MEMCACHED = 0.001;
    
    /**
     * @name Log 
     * 
     * @param string $message 
     * @param string $file 
     * @static
     * @access public
     * @return void
     */
    public static function Log($message, $file = '')
    {
		
        $file = empty($file) ? 'system' : $file;
        $file = self::PROJECT.'_'.$file.'_'.date('Ymd').'.log';
        try {
            $logFile = self::LOG_DIR.$file;
            if (!is_dir(self::LOG_DIR)) {
                mkdir(self::LOG_DIR);
                chmod(self::LOG_DIR, 0777);
            }
            if (!file_exists($logFile)) {
                file_put_contents($logFile, '');
                chmod($logFile, 0777);
            }
            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            if(filesize($logFile)<self::MAX_SIZE) {
                $message = sprintf("\n%s\t%s",date('Y-m-d H:i:s',time()),$message);
                $rs = file_put_contents($logFile,$message,FILE_APPEND);
				
            }
        }
        catch (Exception $e) {
            //print_r($e);
        }
    }
}
