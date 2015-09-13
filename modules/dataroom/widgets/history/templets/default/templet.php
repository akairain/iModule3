<div class="WidgetDataroomHistoryDefault">
	<?php for ($i=0, $loop=count($versions);$i<$loop;$i++) { ?>
	<article class="historyBox">
		<h3>Version <?php echo $versions[$i]->version; ?></h3>
		
		<div class="release"><?php echo GetTime('F d, Y'); ?></div>
		
		<ul>
			<?php foreach ($versions[$i]->history as $history) { ?>
			<li><?php echo $history; ?></li>
			<?php } ?>
		</ul>
		
		<i class="icon fa fa-cog"></i>
	</article>
	<?php } ?>
</div>