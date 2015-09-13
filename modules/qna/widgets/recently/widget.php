<?php
if (defined('__IM__') == false) exit;

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$qid = $Widget->getValue('qid') != null && is_array($Widget->getValue('qid')) == false ? array($Widget->getValue('qid')) : $Widget->getValue('qid');
$type = $Widget->getValue('type') != null && in_array($Widget->getValue('type'),array('all','question','answer')) == true ? $Widget->getValue('type') : 'all';
$title = $Widget->getValue('title');
$titleIcon = $Widget->getValue('titleIcon');
$label = $Widget->getValue('label');
$count = is_numeric($Widget->getValue('count')) == true ? $Widget->getValue('count') : 10;
$sort = $Widget->getValue('sort') != null && in_array($Widget->getValue('sort'),array('idx','reg_date','vote','last_answer')) == true ? $Widget->getValue('sort') : 'idx';

if (count($qid) == 1) {
	$qnaPage = $Module->getQnaPage($qid[0],$label);
	if ($qnaPage != null) {
		$title = $title == null ? $qnaPage->title : $title;
		$link = $IM->getUrl($qnaPage->menu,$qnaPage->page,false);
	} else {
		$qnaPage = $Module->getQnaPage($qid[0]);
		$title = $title == null ? $qnaPage->title : $title;
		$link = $IM->getUrl($qnaPage->menu,$qnaPage->page,false);
	}
} elseif ($title == null) {
	$title = $Widget->getLanguage($type);
	$link = '';
}


if ($label != null) {
	$lists = $Module->db()->select($Module->getTable('post').' p')->join($Module->getTable('post_label').' l','p.parent=l.idx','LEFT')->where('l.label',$label)->orderBy('p.'.$sort,'desc')->limit($count);
} else {
	$lists = $Module->db()->select($Module->getTable('post').' p')->orderBy('p.'.$sort,'desc')->limit($count);
}

if ($qid != null) $lists->where('p.qid',$qid,'IN');
if ($type != 'all') $lists->where('p.type',strtoupper($type));

$lists = $lists->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$postPage = $Module->getPostPage($lists[$i]->parent);
	$lists[$i] = $Module->getArticle('post',$lists[$i]);
	if ($postPage != null) {
		$lists[$i]->link = $IM->getUrl($postPage->menu,$postPage->page,'view',$lists[$i]->parent);
	} else {
		$qnaPage = $Module->getQnaPage($lists[$i]->qid);
		$lists[$i]->link = $IM->getUrl($qnaPage->menu,$qnaPage->page,'view',$lists[$i]->parent);
	}
}

INCLUDE $Widget->getTempletPath().'/templet.php';
?>