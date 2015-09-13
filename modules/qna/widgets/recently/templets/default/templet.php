<div class="WidgetQnaRecentlyDefault">
	<div class="listTitle">
		<?php echo $titleIcon ? $titleIcon : '<i class="fa fa-download"></i>'; ?> <?php echo $link ? '<b><a href="'.$link.'">'.$title.'</a></b>' : '<b>'.$title.'</b>'; ?>
		<div class="bar"><span></span></div>
	</div>
	
	<ul>
		<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
		<li<?php echo $lists[$i]->reg_date > time() - 60*60*24*3 ? ' class="new"' : ''; ?>>
			<a href="<?php echo $lists[$i]->link; ?>">
				<span class="name"><?php echo $lists[$i]->name; ?></span>
				<span class="type <?php echo $lists[$i]->type; ?><?php echo $lists[$i]->is_select == 'TRUE' ? ' selected' : ''; ?>"><?php echo $lists[$i]->type; ?></span>
				<?php echo $lists[$i]->title; ?>
			</a>
		</li>
		<?php } ?>
	</ul>
</div>