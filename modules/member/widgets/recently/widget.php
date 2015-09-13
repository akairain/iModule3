<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$gidx = $Widget->getValue('gidx');
$title = $Widget->getValue('title') ? $Widget->getValue('title') : $Widget->getLanguage('title');
$titleIcon = $Widget->getValue('titleIcon') ? $Widget->getValue('titleIcon') : '<i class="fa fa-male"></i>';
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 10;
$cache = $Widget->getValue('cache') ? $Widget->getValue('cache') : 3600;

if ($Widget->cacheCheck() < time() - $cache) {
	$lists = $Module->db()->select($Module->getTable('member'),'idx')->orderBy('idx','desc')->limit($count);
	if ($gidx) $lists->where('gidx',$gidx);
	$lists = $lists->get();
	
	for ($i=0, $loop=count($lists);$i<$loop;$i++) {
		$lists[$i] = $Module->getMember($lists[$i]->idx);
	}
	
	$data = json_encode($lists,JSON_UNESCAPED_UNICODE);
	$Widget->cacheStore($data);
} else {
	$data = $Widget->cache();
}

$lists = json_decode($data);

INCLUDE $Widget->getTempletFile();
?>