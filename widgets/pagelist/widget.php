<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$menu = $IM->getMenus($Widget->getValue('menu') == null ? $IM->menu : $Widget->getValue('menu'));
if ($menu === null) return;
$pages = $IM->getPages($menu->menu);
INCLUDE $Widget->getTempletPath().'/templet.php';
?>