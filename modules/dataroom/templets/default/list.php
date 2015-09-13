	<div class="contextTitle">
		<div class="contextTab">
			<ul class="sort">
				<li onclick="Dataroom.sort('update');"<?php echo $sort == 'update' ? ' class="selected"' : ''; ?>>업데이트<?php echo $sort == 'update' ? ' <span class="fa fa-caret-down"></span>' : ''; ?></li>
				<li onclick="Dataroom.sort('download');"<?php echo $sort == 'download' ? ' class="selected"' : ''; ?>>다운로드<?php echo $sort == 'download' ? ' <span class="fa fa-caret-down"></span>' : ''; ?></li>
				<li onclick="Dataroom.sort('purchase');"<?php echo $sort == 'purchase' ? ' class="selected"' : ''; ?>>구매자료</li>
				<li onclick="Dataroom.sort('mypost');"<?php echo $sort == 'mypost' ? ' class="selected"' : ''; ?>>나의자료</li>
			</ul>
		</div>
	</div>
	
	<div class="listPanel row">
		<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
		<div class="col-sm-4">
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
	
	<table class="searchTable">
	<tr>
		<td class="searchInput">
			<div class="liveSearchControl">
				<input type="text" name="keyword" value="<?php echo $keyword; ?>">
				<button type="submit" class="btnRed"><span class="fa fa-search"></span></button>
			</div>
		</td>
		<td class="buttonRight">
			<a href="<?php echo $IM->getUrl(null,null,'write'); ?>" class="btn btnRed"><i class="fa fa-upload"></i> <?php echo $Module->getLanguage('button/write'); ?></a>
		</td>
	</tr>
	</table>
	
	<div class="center"><?php echo $pagination->html; ?></div>