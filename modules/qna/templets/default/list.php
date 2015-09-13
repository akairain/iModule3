	<div class="contextTitle">
		<div class="contextTab">
			<ul class="sort">
				<li onclick="Qna.sort('idx');"<?php echo $sort == 'idx' ? ' class="selected"' : ''; ?>>최근등록질문</li>
				<li onclick="Qna.sort('new');"<?php echo $sort == 'new' ? ' class="selected"' : ''; ?>>답변없는질문</li>
				<li onclick="Qna.sort('answer');"<?php echo $sort == 'answer' ? ' class="selected"' : ''; ?>>최근답변질문</li>
				<li onclick="Qna.sort('mypost');"<?php echo $sort == 'mypost' ? ' class="selected"' : ''; ?>>나의질문</li>
			</ul>
		</div>
	</div>
	
	<table class="listTable">
	<thead>
		<tr>
			<th class="vote hidden-xs">
				<div><?php echo $Module->getLanguage('good'); ?></div>
			</th>
			<th class="answer">
				<div><?php echo $Module->getLanguage('answer'); ?></div>
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
					<span class="liveUpdateQnaVote<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->vote); ?></span>
					votes
				</div>
			</td>
			<td class="answer">
				<div class="count<?php echo $lists[$i]->answer > 0 ? ' hasAnswer' : ''; ?><?php echo $lists[$i]->is_select == 'TRUE' ? ' selected' : ''; ?>">
					<span class="liveUpdateQna<?php echo $lists[$i]->idx; ?>"><?php echo number_format($lists[$i]->answer); ?></span>
					answers
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
	
	<table class="searchTable">
	<tr>
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