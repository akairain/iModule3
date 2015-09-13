<?php
class DB {
	private $connectors = array();
	
	private $code;
	private $table;
	
	function __construct() {
		global $_CONFIGS;
		
		if (isset($_CONFIGS->db) == true) $this->db();
	}
	
	function db($code='default',$prefix=null) {
		global $_CONFIGS;
		
		if (isset($this->connectors[$code]) == false) {
			if ($code == 'default') $db = $_CONFIGS->db;
			if (!$db) return $this;
			
			if (file_exists(__IM_PATH__.'/classes/DB/'.$db->type.'.class.php') == false) die('Not Support Database : '.$db->type);
			
			if (isset($db->charset) == false) $db->charset = 'utf8';
			REQUIRE_ONCE __IM_PATH__.'/classes/DB/'.$db->type.'.class.php';
			
			$this->connectors[$code] = new $db->type($db);
		}
		
		$prefix = $prefix == null ? __IM_DB_PREFIX__ : $prefix;
		$this->connectors[$code]->setPrefix($prefix);
		
		return $this->connectors[$code];
	}
	
	function createCode($type,$host,$username,$password,$database,$port=null,$charset=null) {
		$code = array('type'=>$type,'host'=>$host,'username'=>$username,'password'=>$password,'database'=>$database);
		if ($port !== null) $code['port'] = $port;
		if ($charset !== null) $code['charset'] = $charset;
		
		return Encoder(json_encode($code));
	}
}
?>