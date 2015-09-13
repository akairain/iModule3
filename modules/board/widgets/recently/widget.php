<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$bid = $Widget->getValue('bid') != null && is_array($Widget->getValue('bid')) == false ? array($Widget->getValue('bid')) : $Widget->getValue('bid');
$type = $Widget->getValue('type') != null && in_array($Widget->getValue('type'),array('post','ment')) == true ? $Widget->getValue('type') : 'post';
$title = $Widget->getValue('title');
$titleIcon = $Widget->getValue('titleIcon') ? $Widget->getValue('titleIcon') : '<i class="fa fa-bars"></i>';
$category = $type == 'post' ? $Widget->getValue('category') : null;
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 10;
$sort = $Widget->getValue('sort') != null && in_array($Widget->getValue('sort'),array('idx','reg_date','hit','good','bad','ment','last_ment')) == true ? $Widget->getValue('sort') : 'idx';

if (count($bid) == 1 && $title == null) {
	$boardPage = $Module->getBoardPage($bid[0],$category);
	if ($boardPage != null) {
		$title = $boardPage->title;
		$link = $IM->getUrl($boardPage->menu,$boardPage->page,false);
	} else {
		$board = $Module->getBoard($bid[0]);
		$title = $board->title;
		$link = '';
	}
} elseif ($title == null) {
	$title = $Widget->getLanguage($type);
	$link = '';
}

$lists = $Module->db()->select($Module->getTable($type))->orderBy($sort,'desc')->limit($count);

if ($type == 'post' && count($bid) == 1 && $category != null) {
	$board = $Module->getBoard($bid[0]);
	if ($board->use_category == 'USEDALL') $lists = $lists->where('category',array(0,$category),'IN');
	elseif ($board->use_category != 'NONE') $lists = $lists->where('category',$category);
}

if ($bid != null) $lists->where('bid',$bid,'IN');
if ($type == 'ment') $lists->where('is_delete','FALSE');
$lists = $lists->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	if ($type == 'post') {
		$lists[$i] = $Module->getArticle('post',$lists[$i],true);
	} else {
		$lists[$i] = $Module->getArticle('ment',$lists[$i],true);
		$lists[$i]->title = $lists[$i]->search == '' ? $Module->getLanguage('onlyHtml') : $lists[$i]->search;
	}
}

INCLUDE $Widget->getTempletPath().'/templet.php';
?>