<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$class = $Widget->getValue('class') != null ? $Widget->getValue('class') : $IM->printError('NOT_FOUND_CLASS');
$class = $Module->getClass($class);

$attend = $Module->getAttend($class->idx);
$lists = $Module->db()->select($Module->getTable('subject'))->where('parent',$class->idx)->orderBy('reg_date','asc')->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i] = $Module->getArticle('subject',$lists[$i]);
}
INCLUDE $Widget->getTempletPath().'/templet.php';
?>