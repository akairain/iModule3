<ul class="blog">
	<?php foreach ($lists as $list) { ?>
	<li>
		<article>
			
			<?php if (!empty($list->images)) { ?>
			<div class="image">
				<a href="<?php echo $list->link; ?>" target="_blank" style="background-image:url(<?php echo $list->images[0]; ?>);"></a>
			</div>
			<?php } ?>
			<div class="description">
				<h4><a href="<?php echo $list->link; ?>" target="_blank"><?php echo $list->title; ?></a></h4>
				<p><?php echo strip_tags($list->content); ?></p>
			</div>
		</article>
	</li>
	<?php } ?>
</ul>