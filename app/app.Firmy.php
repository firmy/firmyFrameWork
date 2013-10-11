<?php
class firmy {
	function __construct() {
		echo "nice feel";
	}
	
	
	function test() {
		$dbcfg = $_GET['db'];
		Core::InitDb ();
		$db = Core::$db [$dbcfg];
		$sql = "select * from firmy_assets limit 4";
		$r = $db->dataArray ( $sql );
		print_r($r);
	}
}

?>