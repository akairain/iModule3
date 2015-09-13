<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$fid = $Widget->getValue('fid') != null && is_array($Widget->getValue('fid')) == false ? array($Widget->getValue('fid')) : $Widget->getValue('fid');
$type = $Widget->getValue('type') != null && in_array($Widget->getValue('type'),array('post','ment')) == true ? $Widget->getValue('type') : 'post';
$title = $Widget->getValue('title');
$titleIcon = $Widget->getValue('titleIcon');
$label = $type == 'post' ? $Widget->getValue('label') : null;
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 10;
$sort = $Widget->getValue('sort') != null && in_array($Widget->getValue('sort'),array('idx','reg_date','hit','good','bad','ment','last_ment')) == true ? $Widget->getValue('sort') : 'idx';

if (count($fid) == 1 && $title == null) {
	$forumPage = $Module->getForumPage($fid[0],$label);
	if ($forumPage != null) {
		$title = $forumPage->title;
		$link = $IM->getUrl($forumPage->menu,$forumPage->page,false);
	} else {
		$forum = $Module->getForum($fid[0]);
		$title = $forum->title;
		$link = '#';
	}
} elseif ($title == null) {
	$title = $Widget->getLanguage($type);
	$link = '#';
}

if ($label != null) {
	$lists = $Module->db()->select($Module->getTable('post_label').' l')->join($Module->getTable('post').' p','l.idx=p.idx','LEFT')->where('l.label',$label);
} else {
	$lists = $Module->db()->select($Module->getTable($type).' p','p.*');
}

$lists = $lists->orderBy('p.'.$sort,'desc')->limit($count);

if ($fid != null) $lists->where('fid',$fid,'IN');
if ($type == 'ment') $lists->where('is_delete','FALSE');
$lists = $lists->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	if ($type == 'post') {
		$postPage = $Module->getPostPage($lists[$i]->idx);
		$lists[$i]->title = $lists[$i]->title;
		if ($postPage != null) {
			$lists[$i]->link = $IM->getUrl($postPage->menu,$postPage->page,'view',$lists[$i]->idx);
		} else {
			$forumPage = 
			$lists[$i]->link = '';
		}
	} else {
		$postPage = $Module->getPostPage($lists[$i]->parent);
		$lists[$i]->title = $lists[$i]->search == '' ? $Module->getLanguage('onlyHtml') : $lists[$i]->search;
		if ($postPage != null) {
			$lists[$i]->link = $IM->getUrl($postPage->menu,$postPage->page,'view',$lists[$i]->parent).'#ment'.$lists[$i]->idx;
		} else {
			$lists[$i]->link = '';
		}
	}
}

INCLUDE $Widget->getTempletPath().'/templet.php';
?>