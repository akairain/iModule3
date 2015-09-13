<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$type = $Widget->getValue('type') != null && in_array($Widget->getValue('type'),array('post','ment')) == true ? $Widget->getValue('type') : 'post';
$title = $Widget->getValue('title');
$titleIcon = $Widget->getValue('titleIcon');
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 10;
$sort = $Widget->getValue('sort') != null && in_array($Widget->getValue('sort'),array('reg_date','update_date')) == true ? $Widget->getValue('sort') : 'reg_date';
$cache = $Widget->getValue('cache') ? $Widget->getValue('cache') : 300;

if ($title == null) {
	$title = $Widget->getLanguage($type);
}

if ($Widget->cacheCheck() < time() - $cache) {
	$lists = $IM->db()->select($IM->getTable('article'))->where('type',$type)->orderBy($sort,'desc')->limit($count)->get();
	for ($i=0, $loop=count($lists);$i<$loop;$i++) {
		$article = $IM->getModule($lists[$i]->module)->getArticle($lists[$i]->type,$lists[$i]->idx,true);
		$article->module = $lists[$i]->module;
		$lists[$i] = $article;
	}
	
	$data = json_encode($lists,JSON_UNESCAPED_UNICODE);
	$Widget->cacheStore($data);
} else {
	$data = $Widget->cache();
}

$lists = json_decode($data);

INCLUDE $Widget->getTempletPath().'/templet.php';
?>