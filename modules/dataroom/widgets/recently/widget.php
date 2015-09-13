<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$did = $Widget->getValue('did') != null && is_array($Widget->getValue('did')) == false ? array($Widget->getValue('did')) : $Widget->getValue('did');
$title = $Widget->getValue('title');
$titleIcon = $Widget->getValue('titleIcon');
$category = $Widget->getValue('category');
$sort = 'last_update';
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 4;

if (count($did) == 1) {
	$dataroomPage = $Module->getDataroomPage($did[0],$category);
	if ($dataroomPage != null) {
		$title = $title == null ? $dataroomPage->title : $title;
		$link = $IM->getUrl($dataroomPage->menu,$dataroomPage->page,false);
	} else {
		$dataroom = $Module->getDataroom($did[0]);
		$title = $dataroom->title.'?';
		$link = '';
	}
} elseif ($title == null) {
//	$title = $Widget->getLanguage($type);
	$link = '';
}

$lists = $Module->db()->select($Module->getTable('post'))->orderBy($sort,'desc')->limit($count);

if (count($did) == 1 && $category != null) {
	$dataroom = $Module->getDataroom($did[0]);
	if ($dataroom->use_category != 'NONE') {
		if (is_array($category) == false) $lists = $lists->where('category',$category);
		else $lists = $lists->where('category',$category,'IN');
	}
}

//if ($did != null) $lists->where('did',$did,'IN');
$lists = $lists->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$postPage = $Module->getPostPage($lists[$i]->idx);
	
	$lists[$i] = $Module->getArticle('post',$lists[$i]);
	$lists[$i]->link = $this->IM->getUrl($postPage->menu,$postPage->page,'view',$lists[$i]->idx);
}

INCLUDE $Widget->getTempletPath().'/templet.php';
?>