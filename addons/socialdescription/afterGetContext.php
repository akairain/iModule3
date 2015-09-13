<?php
if ($target == 'core') {
	if (preg_match('/(facebook|kakaotalk)/',$_SERVER['HTTP_USER_AGENT']) == true) {
		$IM->addSiteHeader('meta',array('property'=>'og:type','content'=>'website'));
		$IM->addSiteHeader('meta',array('property'=>'og:title','content'=>$IM->getSiteTitle()));
		$IM->addSiteHeader('meta',array('property'=>'og:url','content'=>$IM->getSiteCanonical()));
		$IM->addSiteHeader('meta',array('property'=>'og:description','content'=>$IM->getSiteDescription()));
		$IM->addSiteHeader('meta',array('property'=>'og:image','content'=>$IM->getSiteImage(true)));
	}
	
	if (preg_match('/(Twitter|Telegram)/',$_SERVER['HTTP_USER_AGENT']) == true) {
		$IM->addSiteHeader('meta',array('property'=>'twitter:card','content'=>'summary'));
		$IM->addSiteHeader('meta',array('property'=>'twitter:title','content'=>$IM->getSiteTitle()));
		$IM->addSiteHeader('meta',array('property'=>'twitter:url','content'=>$IM->getSiteCanonical()));
		$IM->addSiteHeader('meta',array('property'=>'twitter:description','content'=>$IM->getSiteDescription()));
		$IM->addSiteHeader('meta',array('property'=>'twitter:image','content'=>$IM->getSiteImage(true)));
	}
}
?>