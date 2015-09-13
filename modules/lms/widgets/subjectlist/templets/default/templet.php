<div class="WidgetLmsSubjectlistDefault">
	
	<div class="subjectList">
		<ul class="subject">
			<?php foreach ($lists as $list) { ?>
			<li>
				<div class="percent">
					<?php if ($attend != null) { ?>
					<div class="bar" style="width:<?php echo $list->percent; ?>%;"></div>
					<div class="view"><?php echo $attend->mode == 'P' ? '수강율' : '진도율'; ?> : <?php echo sprintf('%0.2f',$list->percent); ?>%</div>
					<?php } ?>
				</div>
				<div class="title"><i class="fa fa-file-text-o"></i> <?php echo $list->title; ?></div>
				
				<div class="itemList">
					<div class="row">
						<?php foreach ($list->posts as $post) { $page = $Module->getPostPage($post->idx); ?>
						<div class="col-xs-6">
							<a href="<?php echo $IM->getUrl($page->menu,$page->page,'view',$post->idx); ?>" class="item">
								<div class="preview <?php echo $post->type; ?>"<?php if($post->image != null) { ?> style="background-image:url(<?php echo $post->image; ?>);"<?php } ?>>
									<?php if ($post->type == 'youtube' || $post->type == 'video') { ?><div class="video"><i class="fa fa-play"></i></div><?php } ?>
									
									<?php if ($attend != null) { ?>
									<div class="percent">
										<div class="bar" style="width:<?php echo $post->percent; ?>%;"></div>
										<div class="view"><?php echo $attend->mode == 'P' ? '수강율' : '진도율'; ?> : <?php echo sprintf('%0.2f',$post->percent); ?>%</div>
									</div>
									
									<?php if ($attend->mode == 'S' && $post->percent > 90) { ?><div class="complete"><i class="fa fa-check"></i></div><?php } ?>
									<?php } ?>
								</div>
								<div class="title"><?php echo $post->title; ?></div>
							</a>
						</div>
						<?php } ?>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>

<script>
/*
$(document).ready(function() {
	var itemlist = $(".WidgetLmsSubjectlistDefault .itemlist ul");
	
	itemlist.each(function() {
		$($(this).find("li").last()).css("marginRight",$(this).width() - 110);
		
		if ($(this).attr("data-position") && $(this).attr("data-position") != 0 && $(this).attr("data-position") < $(this).attr("data-items")) {
			console.log("조건!");
		} else {
			console.log("조건아님!");
		}
//		$($(this).find("li").pop()).css("width",200);
		/*
		$(this).data("items",items.length);
		
		if ($(this).data(""))
		console.log($(this).find("li"));
		
	})
});
*/
</script>