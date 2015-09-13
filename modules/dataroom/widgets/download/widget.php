<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$idx = $Widget->getValue('idx');
$post = $Module->getArticle('post',$Module->getPost($idx));
$purchase = $Module->getPurchaseData($idx);
$versions = $Module->getVersions($idx);

$titleIcon = $Widget->getValue('titleIcon');
$title = $Widget->getValue('title') != null ? $Widget->getValue('title') : $post->title;

INCLUDE $Widget->getTempletPath().'/templet.php';
?>
