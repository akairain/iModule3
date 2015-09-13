<div class="WidgetDataroomRecentlyDefault">
	<div class="contextTitle">
		<?php echo $titleIcon ? $titleIcon : '<i class="fa fa-download"></i>'; ?> <?php echo $link ? '<a href="'.$link.'">'.$title.'</a>' : $title; ?>
	</div>
	
	<div class="listPanel row">
		<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
		<div class="col-sm-3">
			<div class="item">
				<a href="<?php echo $lists[$i]->link; ?>" class="logo" style="background-image:url(<?php echo $lists[$i]->logo == null ? '' : $lists[$i]->logo->path; ?>);">
					<?php if ($lists[$i]->category != 0) { ?><div class="category"><?php echo $Module->getCategory($lists[$i]->category)->title; ?></div><?php } ?>
					<?php if ($lists[$i]->last_version) { ?><div class="version">Latest <?php echo $lists[$i]->last_version; ?></div><?php } ?>
				</a>
				
				<div class="detail">
					<a href="<?php echo $lists[$i]->link; ?>" class="title"><?php echo $lists[$i]->title; ?></a>
					<div class="description"><?php echo $lists[$i]->search; ?></div>
					
					<div class="count">
						<i class="fa fa-comments"></i><span class="ment liveUpdateDataroomPostMent<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->ment); ?></span>
						<i class="fa <?php echo $lists[$i]->price > 0 ? 'fa-shopping-cart' : 'fa-download'; ?>"></i><span class="download liveUpdateDataroomDownload<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->download); ?></span>
						
						<span class="price">
							<i class="fa fa-rub"></i><?php echo $lists[$i]->price == 0 ? 'FREE' : number_format($lists[$i]->price); ?>
						</span>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>