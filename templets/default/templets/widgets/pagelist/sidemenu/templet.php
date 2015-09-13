<?php
$IM->addSiteHeader('style',$Widget->templetDir.'/styles/style.css');
?>
<div class="WidgetPagelistSidebar">
	<div class="menuTitle">
		<h2><?php echo $menu->title; ?></h2>
		<div class="bar"><span></span></div>
	</div>
	
	<?php if (count($pages) > 0) { ?>
	<ul>
		<?php for ($i=0, $loop=count($pages);$i<$loop;$i++) { $pageCountInfo = $IM->getPageCountInfo($pages[$i]); ?>
		<li<?php echo $IM->page == $pages[$i]->page ? ' class="selected"' : ''; ?>>
			<a href="<?php echo $IM->getUrl($menu->menu,$pages[$i]->page,false); ?>">
				<?php if ($pageCountInfo != null) { ?>
				<span class="badge<?php echo $pageCountInfo->last_time > time() - 60*60*24*3 ? ' new' : ''; ?>"><?php echo isset($pageCountInfo->count) == true ? number_format($pageCountInfo->count) : $pageCountInfo->text; ?></span>
				<?php } ?>
				\
				<i class="fa <?php echo isset($pages[$i]->context->icon) == true && preg_match('/^fa\-/',$pages[$i]->context->icon) == true ? $pages[$i]->context->icon : 'fa-plus'; ?>"></i><b><?php echo $pages[$i]->title; ?></b>
			</a>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
</div>