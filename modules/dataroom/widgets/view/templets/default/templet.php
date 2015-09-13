<div class="WidgetDataroomViewDefault">
	<div class="viewPanel">
		<div class="itemBox">
			<div class="itemPanel">
				<div class="logoPanel">
					<div class="logo" style="background-image:url(<?php echo $post->logo == null ? '' : $post->logo->path; ?>);"></div>
				</div>
				
				<div class="detailPanel">
					<div class="detail">
						<h4><?php echo $post->title; ?></h4>
						
						<div class="description"><?php echo $post->search; ?></div>
						
						<div class="price">
							<i class="fa fa-rub"></i> <?php echo $post->price == 0 ? 'FREE' : number_format($post->price); ?>
						</div>
						
						<div class="splitLine"></div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('name'); ?></div>
							<div class="value"><?php echo $post->name; ?></div>
						</div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('homepage'); ?></div>
							<div class="value"><?php echo $post->homepage; ?></div>
						</div>
						
						<div class="splitLine"></div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('license'); ?></div>
							<div class="value"><?php echo $post->license; ?></div>
						</div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('version'); ?></div>
							<div class="value version"><?php echo $post->last_version; ?> <span class="date">(<?php echo GetTime('Y.m.d H:i',$post->last_update); ?>)</span></div>
						</div>
						
						<div class="splitLine"></div>
						
						<div class="button">
							<button class="btn <?php echo $purchase == null ? 'btnRed' : 'btnBlue'; ?>" onclick="Dataroom.download(<?php echo $post->idx; ?>);">
								<i class="fa <?php echo $post->price == 0 ? 'fa-download' : 'fa-shopping-cart'; ?>"></i>
								<?php if ($purchase == null) echo $post->price == 0 ? $Module->getLanguage('button/download_free') : $Module->getLanguage('button/download_buy'); else echo $Module->getLanguage('button/download_buyed'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="postContext"><?php echo $post->content; ?></div>
	</div>
</div>