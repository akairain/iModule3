	<div class="contextTitle">
		<i class="fa fa-university"></i> <?php echo $Module->getLanguage('list/title'); ?>
	</div>
	
	<div class="listPanel row">
		<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
		<div class="col-sm-3 col-xs-6">
			<div class="item">
				<div class="classImage" style="background-image:url(<?php echo $lists[$i]->cover != null ? $lists[$i]->cover->path : ''; ?>);">
					<a href="<?php echo $lists[$i]->link; ?>" class="classCover"></a>
					<div class="classTitle">
						<div class="title"><?php echo $lists[$i]->title; ?></div>
						<div class="author"><?php echo $lists[$i]->name; ?></div>
						<div class="info">
							<i class="fa fa-user"></i> <?php echo number_format($lists[$i]->student); ?> <i class="fa fa-book"></i> <?php echo number_format($lists[$i]->subject); ?>
						</div>
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
			<a href="<?php echo $IM->getUrl(null,null,'create'); ?>" class="btn btnRed"><i class="fa fa-university"></i> 강의 개설하기</a>
		</td>
	</tr>
	</table>
	
	<div class="center"><?php echo $pagination->html; ?></div>