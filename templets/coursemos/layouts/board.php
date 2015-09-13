	<div class="subHeader <?php echo $IM->menu; ?>">
		<div class="subIntro" style="background-image:url(<?php echo $IM->getTempletDir(); ?>/images/<?php echo $IM->menu; ?>.png);"></div>
	
		<?php $pages = $IM->getPages($IM->menu); if (count($pages) > 0) { ?>
		<div class="subTab">
			<div class="container">
				<ul>
					<?php foreach ($pages as $page) { ?>
					<li<?php echo $page->page == $IM->page ? ' class="selected"' : ''; ?>><a href="<?php echo $IM->getUrl($page->menu,$page->page,false); ?>"><?php echo $page->title; ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<div class="subContent">
		<div class="container <?php echo $IM->menu; ?>">
			<?php echo $context; ?>
		</div>
	</div>

	<script>
	$(document).ready(function() {
		if ($(document).scrollTop() + $("#iModuleNavigation.fixed").height() + $(".subTab").height() > $(".subContent > .container").offset().top) {
			$(".subHeader").addClass("fixed");
			$(".subContent").addClass("fixed");
		} else {
			$(".subHeader").removeClass("fixed");
			$(".subContent").removeClass("fixed");
		}
	});
	
	$(document).on("scroll",function() {
		if ($(document).scrollTop() + $("#iModuleNavigation.fixed").height() + $(".subTab").height() > $(".subContent > .container").offset().top) {
			$(".subHeader").addClass("fixed");
			$(".subContent").addClass("fixed");
		} else {
			$(".subHeader").removeClass("fixed");
			$(".subContent").removeClass("fixed");
		}
	});
	</script>