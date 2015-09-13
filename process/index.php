<?php
REQUIRE_ONCE '../configs/init.config.php';

$IM = new iModule();
$site = $IM->getSite();

if (Request('module') != null) {
	$results = $IM->getModule(Request('module'))->doProcess(Request('action'));
	
	if ($results !== null) {
		header("Content-type: text/json; charset=utf-8",true);
		exit(json_encode($results,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
	}
}
?>