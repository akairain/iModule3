<?php
if (defined('__IM__') == false) exit;
define('__IM_HEADER_INCLUDED__',true);

$IM->addSiteHeader('style',$IM->getTempletDir().'/styles/style.css');
$IM->addSiteHeader('style',$IM->getTempletDir().'/styles/style.css');
$IM->addSiteHeader('script',$IM->getTempletDir().'/scripts/script.js');
?>
<!DOCTYPE HTML>
<html lang="<?php echo $IM->language; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?php echo $IM->getSiteTitle(); ?></title>
<meta name="google-site-verification" content="jaQkQe8Z2NrK5t6PfrpVys-eYclQtSwwlLdtZ62gH0w">
<?php echo $IM->getSiteHeader(); ?>
<link rel="alternate" hreflang="en" href="http://www.coursemos.kr/en/<?php echo $IM->menu ? $IM->menu.'/' : ''; ?>">
<link rel="alternate" hreflang="ko" href="http://www.coursemos.kr/ko/<?php echo $IM->menu ? $IM->menu.'/' : ''; ?>">
</head>
<body>

<div id="iModuleWrapper">
	<div id="iModuleAlertMessage"></div>
	<div id="iModuleNavigation" class="fixed">
		<div class="container">
			<h1 style="background-image:url(<?php echo $IM->getSiteLogo(); ?>);"><a href="<?php echo $IM->getUrl(false); ?>">COURSEMOS</a></h1>
			<i class="fa fa-bars visible-xs visible-sm" onclick="iModule.slideMenu.toggle(true);"></i>
			<div class="language">
				<button type="button">Language <i class="fa fa-caret-down"></i><i class="fa fa-caret-up"></i></button>
				
				<ul class="subpage">
					<li><a href="http://www.coursemos.kr/ko/<?php echo $IM->menu ? $IM->menu.'/' : ''; ?>" hreflang="ko" rel="alternate">한국어</a></li>
					<li><a href="http://www.coursemos.kr/en/<?php echo $IM->menu ? $IM->menu.'/' : ''; ?>" hreflang="en" rel="alternate">English</a></li>
				</ul>
			</div>
			
			<ul class="menu hidden-xs hidden-sm">
				<?php $menus = $IM->getMenus(); foreach ($menus as $menu) { if ($menu->menu == 'index' || ($menu->menu == 'board' && $_SERVER['REMOTE_ADDR'] != '115.89.228.234')) continue; $pages = $IM->getPages($menu->menu); ?>
				<li<?php echo $IM->menu == $menu->menu ? ' class="selected"' : ''; ?>>
					<a href="<?php echo $IM->getUrl($menu->menu,false); ?>"><?php echo $menu->title; ?></a>
					
					<?php if (count($pages) > 0) { ?>
					<ul class="subpage">
						<?php foreach ($pages as $page) { ?>
						<li><a href="<?php echo $IM->getUrl($page->menu,$page->page,false); ?>"><?php echo $page->title; ?></a></li>
						<?php } ?>
					</ul>
					<?php } ?>
				</li>
				<?php } ?>
				<li class="bar">|</li>
			</ul>
			
			
		</div>
	</div>