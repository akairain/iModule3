<?php
$IM->addSiteHeader('style',$IM->getTempletDir().'/styles/style.css');
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
	
	<nav id="iModuleNavigation" class="navigation" role="navigation">
		<h1><?php echo $IM->getSiteTitle(); ?></h1>
		
		<ul>
			<?php $menus = $IM->getMenus(); for ($i=0, $loop=count($menus);$i<$loop;$i++) { if ($menus[$i]->menu == 'index') continue; $pages = $IM->getPages($menus[$i]->menu); ?>
			<li<?php echo $IM->menu == $menus[$i]->menu ? ' class="selected"' : ''; ?>>
				<a href="<?php echo $IM->getUrl($menus[$i]->menu,false); ?>"><?php echo $menus[$i]->title; ?></a>
				<div class="hideShadow"></div>
			</li>
			<?php } ?>
		</ul>
	</nav>