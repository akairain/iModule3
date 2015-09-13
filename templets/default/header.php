<?php
if (defined('__IM__') == false) exit;
define('__IM_HEADER_INCLUDED__',true);

$temp = explode('.',$_SERVER['HTTP_HOST']);
$domain = $temp[1];
$IM->addSiteHeader('style',$IM->getTempletDir().'/styles/style.css');
$IM->addSiteHeader('script',$IM->getTempletDir().'/scripts/script.js');
if ($IM->domain == 'www.arzz.com') $IM->addSiteHeader('meta',array('name'=>'google-site-verification','content'=>'KIeZ8Rp2AXaSMTy4hnQTE1PB_NwVTUSMUXAkhhbFZw4'));
if ($IM->domain == 'www.minitalk.kr') $IM->addSiteHeader('meta',array('name'=>'google-site-verification','content'=>'xwIxiSh5h6--wpfUdV7bM4QfoiddBE3lBsmviqulNvU'));
?>
<!DOCTYPE HTML>
<html lang="<?php echo $IM->language; ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?php echo $IM->getSiteTitle(); ?></title>
<?php echo $IM->getSiteHeader(); ?>
</head>
<body>

<div id="iModuleWrapper">
	<div id="iModuleAlertMessage"></div>
	
	<header id="iModuleHeader" class="hidden-xs">
		<div class="topmenu">
			<div class="container">
				<ul class="familySite">
					<li><a href="https://www.arzz.com">알쯔닷컴</a></li>
					<li><a href="http://blog.arzz.com">블로그</a></li>
					<li><a href="https://www.minitalk.kr">미니톡</a></li>
					<li><a href="http://www.firc.kr">에프IRC</a></li>
					<li><a href="http://www.imodule.kr">아이모듈</a></li>
					<li><a href="http://www.azuploader.kr">에이지업로더</a></li>
					<li><a href="https://www.examples.kr">예제</a></li>
				</ul>
			</div>
		</div>
		
		<div class="header">
			<div class="container">
				<h1<?php echo $IM->getSite()->logo !== null ? ' style="background-image:url('.$IM->getSiteLogo().');"' : ''; ?>><a href="<?php echo __IM_DIR__.'/'.$IM->language.'/'; ?>"><?php echo $IM->getSite()->title; ?></a></h1>
				
				<div class="topRight">
					<ins class="adsbygoogle" style="display:inline-block;width:468px;height:60px" data-ad-client="ca-pub-3210736654114323" data-ad-slot="8159304661" data-override-format="true" data-page-url="http://<?php echo $_SERVER['HTTP_HOST']; ?>"></ins>
					<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
				</div>
			</div>
		</div>
	</header>
	
	<div class="naviWrapper">
		<nav id="iModuleNavigation" class="navigation" role="navigation">
			<div class="container">
				<ul class="hidden-xs hidden-sm">
					<?php $menus = $IM->getMenus(); for ($i=0, $loop=count($menus);$i<$loop;$i++) { $pages = $IM->getPages($menus[$i]->menu); ?>
					<li>
						<a href="<?php echo $IM->getUrl($menus[$i]->menu,false); ?>"<?php echo $IM->menu == $menus[$i]->menu ? ' class="selected"' : ''; ?>><?php echo $menus[$i]->title; ?></a>
						<?php if (count($pages) > 0) { ?>
						<ul class="dropdown">
							<?php for ($j=0, $loopj=count($pages);$j<$loopj;$j++) { $pageCountInfo = $IM->getPageCountInfo($pages[$j]); ?>
							<li>
								<a href="<?php echo $IM->getUrl($menus[$i]->menu,$pages[$j]->page,false); ?>">
									<?php if ($pageCountInfo != null) { ?>
									<span class="badge<?php echo $pageCountInfo->last_time > time() - 60*60*24*3 ? ' new' : ''; ?>"><?php echo isset($pageCountInfo->count) == true ? number_format($pageCountInfo->count) : $pageCountInfo->text; ?></span>
									<?php } ?>
									<?php echo $pages[$j]->title; ?>
								</a>
							</li>
							<?php } ?>
						</ul>
						<?php } ?>
					</li>
					<?php } ?>
				</ul>
				
				<a href="<?php echo __IM_DIR__.'/'; ?>" class="emblem visible-xs-inline-block visible-sm-inline-block"<?php echo $IM->getSite()->emblem !== null ? ' style="background-image:url('.$IM->getSite()->emblem.');"' : ''; ?>><?php echo $IM->getSite()->title; ?></a>
				
				<button class="menu visible-xs-inline-block visible-sm-inline-block" onclick="iModule.slideMenu.toggle(true);"><i class="fa fa-bars"></i> MENU</button>
				
				<div class="menu push" onclick="TogglePush(this);">
					<i class="fa fa-bell"></i>
					<span class="badge"><?php echo $IM->getModule('push')->getPushCount('UNCHECK'); ?></span>
					
					<div class="list">
						<div class="arrowBox">
							<b><?php echo $IM->getModule('push')->getLanguage('title'); ?></b>
							<button><?php echo $IM->getModule('push')->getLanguage('button/config'); ?></button>
							<i class="fa fa-circle"></i>
							<button><?php echo $IM->getModule('push')->getLanguage('button/read_all'); ?></button>
						</div>
						
						<ul>
							<li class="loading"></li>
						</ul>
					</div>
				</div>
			</div>
		</nav>
	</div>
	
	<div class="context">