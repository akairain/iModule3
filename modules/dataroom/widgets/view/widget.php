<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$idx = $Widget->getValue('idx');
$post = $Module->getArticle('post',$Module->getPost($idx));
$purchase = $Module->getPurchaseData($idx);

INCLUDE $Widget->getTempletPath().'/templet.php';
?>
