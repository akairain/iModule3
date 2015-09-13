<?php
error_reporting(E_ALL);
ini_set('display_errors',true);

header("Content-type: text/html; charset=utf-8",true);

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/iModule.config.php') == true) {
	REQUIRE_ONCE $_SERVER['DOCUMENT_ROOT'].'/iModule.config.php';
}

define('__IM__',true);
define('__IM_VERSION__','3.0.0');
define('__IM_DB_PREFIX__','im_');
if (defined('__IM_PATH__') == false) define('__IM_PATH__',str_replace('/configs','',__DIR__));
if (defined('__IM_DIR__') == false) define('__IM_DIR__',str_replace($_SERVER['DOCUMENT_ROOT'],'',__IM_PATH__));

REQUIRE_ONCE __IM_PATH__.'/classes/functions.php';

$_CONFIGS = new stdClass();
$_ENV = new stdClass();

try {
	$_CONFIGS->key = isset($_CONFIGS->key) == true ? $_CONFIGS->key : FileReadLine(__IM_PATH__.'/configs/key.config.php',1);
	$_CONFIGS->db = isset($_CONFIGS->db) == true ? $_CONFIGS->db : json_decode(Decoder(FileReadLine(__IM_PATH__.'/configs/db.config.php',1)));
	$_CONFIGS->installed = true;
} catch (Exception $e) {
	$_CONFIGS->key = null;
	$_CONFIGS->db = null;
	$_CONFIGS->installed = false;
}

if ($_CONFIGS->db === null || $_CONFIGS->db === false) $_CONFIGS->installed = false;

function __autoload($class) {
	if ($class != 'Module' && preg_match('/^Module/',$class) == true) {
		$module = strtolower(str_replace('Module','',$class));
		if (file_exists(__IM_PATH__.'/modules/'.$module.'/'.$class.'.class.php') == true) REQUIRE_ONCE __IM_PATH__.'/modules/'.$module.'/'.$class.'.class.php';
	} else {
		if (file_exists(__IM_PATH__.'/classes/'.$class.'.class.php') == true) REQUIRE_ONCE __IM_PATH__.'/classes/'.$class.'.class.php';
	}
}

session_start();
?>