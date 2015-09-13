	<?php if ($isView == true) { ?>
	<div style="height:50px;"></div>
	<?php } ?>
	<div class="contextTitle">
		<i class="fa fa-bars"></i> <?php echo $Module->getLanguage('list/title'); ?> <span class="count"><?php echo number_format($totalCount); ?></span>
	</div>
	
	<table class="listTable">
	<thead>
		<tr>
			<th class="loopnum hidden-xs">
				<div><?php echo $Module->getLanguage('loopnum'); ?></div>
			</th>
			<th class="title">
				<div><?php echo $Module->getLanguage('title'); ?></div>
			</th>
			<th class="name">
				<div><?php echo $Module->getLanguage('name'); ?></div>
			</th>
			<th class="reg_date hidden-xs">
				<div><?php echo $Module->getLanguage('reg_date'); ?></div>
			</th>
			<th class="hit">
				<div><?php echo $Module->getLanguage('hit'); ?></div>
			</th>
			<th class="good hidden-xs">
				<div><span class="fa fa-thumbs-o-up"></span></div>
			</th>
			<th class="bad hidden-xs">
				<div><span class="fa fa-thumbs-o-down"></span></div>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php for ($i=0, $loop=sizeof($lists);$i<$loop;$i++) { ?>
		<tr>
			<td class="loopnum hidden-xs">
				<div><?php echo $idx == $lists[$i]->idx ? '<i class="fa fa-caret-right"></i>' : number_format($lists[$i]->loopnum); ?></div>
			</td>
			<td class="title">
				<div>
					<a href="<?php echo $lists[$i]->link; ?>">
						<?php if ($lists[$i]->is_secret) { ?><i class="fa fa-lock"></i><?php } ?>
						<?php if ($lists[$i]->is_image) { ?><span class="image"></span><?php } ?>
						<?php if ($lists[$i]->is_video) { ?><span class="video"></span><?php } ?>
						<?php if ($lists[$i]->is_file) { ?><span class="file"></span><?php } ?>
						<?php if ($lists[$i]->is_link) { ?><span class="link"></span><?php } ?>
						<span class="ment liveUpdateBoardPostMent<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->ment); ?></span>
						<?php echo $lists[$i]->title; ?>
					</a>
				</div>
			</td>
			<td class="name">
				<div><?php echo $lists[$i]->name; ?></div>
			</td>
			<td class="reg_date hidden-xs">
				<div><?php echo GetTime('Y.m.d',$lists[$i]->reg_date); ?></div>
			</td>
			<td class="hit">
				<div><?php echo number_format($lists[$i]->hit); ?></div>
			</td>
			<td class="good hidden-xs">
				<div class="liveUpdateBoardPostGood<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->good); ?></div>
			</td>
			<td class="bad hidden-xs">
				<div class="liveUpdateBoardPostBad<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->bad); ?></div>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>
	
	<input type="hidden" name="key" value="<?php echo $key; ?>">
	<table class="searchTable">
	<tr>
		<td class="searchKey">
			<div class="selectControl" data-field="key">
				<button>제목+내용 <span class="arrow"></span></button>

				<ul>
					<li data-value="content">제목+내용</li>
					<li data-value="name">작성자</li>
					<li data-value="ment">댓글내용</li>
				</ul>
			</div>
		</td>
		<td class="searchInput">
			<div class="liveSearchControl">
				<input type="text" name="keyword" value="<?php echo $keyword; ?>">
				<button type="submit" class="btnRed"><span class="fa fa-search"></span></button>
			</div>
		</td>
		<td class="buttonRight">
			<a href="<?php echo $IM->getUrl(null,null,'write'); ?>" class="btn btnRed"><i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('button/write'); ?></a>
		</td>
	</tr>
	</table>
	
	<div class="center"><?php echo $pagination->html; ?></div>