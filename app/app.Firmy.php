<?php
class firmy {
	function __construct() {
		//初始化所有数据库连接
		Core::InitDb ();
	}
	
	function test() {
		//使用对应的数据库进行db操作
		$db = Core::$db ['event'];
		$sql = "select * from firmy_assets limit 4";
		$r = $db->fetchOne ( $sql );
		print_r($r);
	}
	
	function tt(){
		$data["Name"] = "Fred Irving Johnathan Bradley Peppergill";
		$data["FirstName"]=array("John","Mary","James","Henry");
		$data["LastName"]=array("Doe","Smith","Johnson","Case");
		$data["Class"]=array(array("A","B","C","D"), array("E", "F", "G", "H"), array("I", "J", "K", "L"), array("M", "N", "O", "P"));
		$data["contacts"]= array(array("phone" => "1", "fax" => "2", "cell" => "3"), array("phone" => "555-4444", "fax" => "555-3333", "cell" => "760-1234"));
		$data["option_values"]= array("NY","NE","KS","IA","OK","TX");
		$data["option_output"] = array("New York","Nebraska","Kansas","Iowa","Oklahoma","Texas");
		$data["option_selected"] = "NE";
		takeTpl("index",$data);
	}
}

?>