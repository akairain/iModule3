<div class="WidgetBoardRecentlyGallery">
	<div class="listTitle">
		<?php echo $titleIcon ? $titleIcon : '<i class="fa fa-camera-retro"></i>'; ?> <?php echo $link ? '<b><a href="'.$link.'">'.$title.'</a></b>' : '<b>'.$title.'</b>'; ?>
		<div class="bar"><span></span></div>
	</div>
	
	<div class="row">
		<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
		<div class="col-xs-6 col-sm-3">
			<a href="<?php echo $lists[$i]->link; ?>" class="frame">
				<div class="photo" style="background-image:url(<?php echo $lists[$i]->image != null ? $lists[$i]->image->thumbnail : ''; ?>);"></div>
				<div class="text">
					<span class="ment"><?php echo $lists[$i]->ment == 0 ? '' : number_format($lists[$i]->ment); ?></span>
					<?php echo $lists[$i]->title; ?>
				</div>
			</a>
		</div>
		<?php } ?>
	</div>
</div>

<script>
$(".WidgetBoardRecentlyGallery .frame").on("touchstart",function() {
	$(this).triggerHandler("mouseover");
});
</script>