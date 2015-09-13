	<?php if ($isView == true) { ?>
	<div style="height:50px;"></div>
	<?php } ?>
	<div class="contextTitle">
		<i class="fa fa-bars"></i> <?php echo $Module->getLanguage('list/title'); ?> <span class="count"><?php echo number_format($totalCount); ?></span>
	</div>
	
	<table class="listTable">
	<thead>
		<tr>
			<th class="vote hidden-xs">
				<div><?php echo $Module->getLanguage('good'); ?></div>
			</th>
			<th class="answer">
				<div><?php echo $Module->getLanguage('ment'); ?></div>
			</th>
			<th class="hit hidden-xs">
				<div><?php echo $Module->getLanguage('hit'); ?></div>
			</th>
			<th class="title">
				<div><?php echo $Module->getLanguage('title'); ?></div>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php for ($i=0, $loop=sizeof($lists);$i<$loop;$i++) { ?>
		<tr>
			<td class="vote hidden-xs">
				<div class="count">
					<span class="liveUpdateForumVote<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->vote); ?></span>
					votes
				</div>
			</td>
			<td class="answer">
				<div class="count<?php echo $lists[$i]->ment > 0 ? ' hasReply' : ''; ?>">
					<span class="liveUpdateForumMent<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->ment); ?></span>
					replies
				</div>
			</td>
			<td class="hit hidden-xs">
				<div class="count">
					<span><?php echo number_format($lists[$i]->hit); ?></span>
					views
				</div>
			</td>
			<td class="title">
				<div class="wrap">
					<a href="<?php echo $lists[$i]->link; ?>" class="title">
						<?php echo $lists[$i]->title; ?>
						<?php if ($lists[$i]->is_link) { ?><span class="link"></span><?php } ?>
						<?php if ($lists[$i]->is_file) { ?><span class="file"></span><?php } ?>
						<?php if ($lists[$i]->is_video) { ?><span class="video"></span><?php } ?>
						<?php if ($lists[$i]->is_image) { ?><span class="image"></span><?php } ?>
						
					</a>
					
					<div class="labels">
						<?php $labels = $Module->getLabels($lists[$i]->idx); foreach ($labels as $label) { ?>
						<a href="<?php echo $Module->getLabelUrl($label->label); ?>" class="label"><?php echo $label->title; ?></a>
						<?php } ?>
						
						<div class="author"><?php echo $lists[$i]->name; ?><span class="reg_date hidden-xs"> At <?php echo GetTime('Y-m-d H:i:s',$lists[$i]->reg_date); ?></span><span class="reg_date visible-xs-inline"> <?php echo GetTime('y.m.d H:i',$lists[$i]->reg_date); ?></span></div>
					</div>
				</div>
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