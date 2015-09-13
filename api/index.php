<?php
header('Access-Control-Allow-Origin:*');
REQUIRE_ONCE '../configs/init.config.php';

$IM = new iModule();
$site = $IM->getSite();

if (isset($_SERVER['HTTP_AUTHORIZATION']) == true) {
	$IM->getModule('member')->loginByToken($_SERVER['HTTP_AUTHORIZATION']);
}

if (Request('module') != null) {
	$results = $IM->getModule(Request('module'))->getApi(Request('api'));
	
	if ($results !== null) {
		header("Content-type: text/json; charset=utf-8",true);
		exit(json_encode($results,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
	}
}
?>