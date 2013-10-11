<?php
class firmy {
	function __construct() {
		Core::InitDb ();
	}
	
	
	function test() {
		$db = Core::$db ['event'];
		$sql = "select * from firmy_assets limit 4";
		$r = $db->fetchOne ( $sql );
		print_r($r);
	}
}

?>