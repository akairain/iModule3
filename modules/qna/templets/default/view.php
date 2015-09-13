	<div class="contextTitle">
		<i class="fa fa-file-text-o"></i> <?php echo $Module->getLanguage('view/title'); ?> <span class="count">#<?php echo number_format($post->idx); ?></span>
	</div>
	<article class="postArticle">
		<h4><span><?php echo $Module->getLanguage('question'); ?></span> <?php echo $post->title; ?></h4>

		<div class="articleContent">
			<div class="voteArea">
				<button type="button" onclick="Qna.vote.good(<?php echo $post->idx; ?>,this);"<?php echo $voted == 'GOOD' ? ' class="selected"' : ''; ?>><i class="fa fa-caret-up"></i></button>
				
				<div class="liveUpdateQnaVote<?php echo $post->idx; ?>"><?php echo number_format($post->vote); ?></div>
				
				<button type="button" onclick="Qna.vote.bad(<?php echo $post->idx; ?>,this);"<?php echo $voted == 'BAD' ? ' class="selected"' : ''; ?>><i class="fa fa-caret-down"></i></button>
			</div>
			
			<div class="postContent">
				<?php echo $post->content; ?>
				
				<?php if (count($attachments) > 0) { ?>
				<div class="blankSpace"></div>
				<div class="contextTitle">
					<i class="fa fa-floppy-o"></i> <?php echo $Module->getLanguage('view/attachment'); ?> <span class="count"><?php echo number_format(count($attachments)); ?></span>
				</div>
				
				<ul class="attachment">
					<?php for ($i=0, $loop=count($attachments);$i<$loop;$i++) { $fileIcon = array('image'=>'fa-file-image-o'); ?>
					<li><a href="<?php echo $attachments[$i]->download; ?>" download="<?php echo $attachments[$i]->name; ?>"><span class="filesize">(<?php echo GetFileSize($attachments[$i]->size); ?>)</span><i class="fa <?php echo empty($fileIcon[$attachments[$i]->type]) == true ? 'fa-file-o' : $fileIcon[$attachments[$i]->type]; ?>"></i> <?php echo $attachments[$i]->name; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				
				<div class="labels">
					<?php $labels = $Module->getLabels($post->idx); foreach ($labels as $label) { ?>
					<a href="<?php echo $Module->getLabelUrl($label->label); ?>" class="label"><?php echo $label->title; ?></a>
					<?php } ?>
				</div>
				
				<div class="authorArea">
					<div class="author">
						<div class="frame">
							<?php echo $IM->getModule('member')->getMemberPhoto($post->midx); ?>
						</div>
						
						<div class="info">
							<div class="nickname"><?php echo $post->name; ?></div>
							
							<div class="block level">
								<span class="text">LV.<b><?php echo $post->member->level->level; ?></b></span>
								<span class="bar">
									<span class="percentage" style="width:<?php echo $post->member->level->exp/$post->member->level->next*100 < 5 ? 5 : $post->member->level->exp/$post->member->level->next*100; ?>%"></span>
									
									<span class="levelDetail">
										<span class="arrowBox"><?php echo number_format($post->member->level->exp); ?>/<?php echo number_format($post->member->level->next); ?></span>
									</span>
								</span>
							</div>
							
							<div class="reg_date"><?php echo GetTime('Y-m-d H:i:s',$post->reg_date); ?></div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
		<div class="blankSpace"></div>
		
		<div class="contextTitle">
			<i class="fa fa-font"></i> <?php echo $Module->getLanguage('answer'); ?> <span class="count liveUpdateBoardPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->answer); ?></span>
		</div>
		
		<?php echo $Module->getAnswerList($idx); ?>
		
		<?php echo $Module->getAnswerWrite($idx); ?>
		
		<?php // echo $Module->getMentList($idx); ?>
		
		<div class="mentBottom">
			<?php // echo $Module->getMentPagination($idx); ?>
		</div>
		
		<div class="postAnswerWrite">
			<?php // echo $Module->getAnswerWrite($idx); ?>
		</div>
	</article>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list').$IM->getQueryString(); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<a href="<?php echo $IM->getUrl(null,null,'write').$IM->getQueryString(); ?>" class="btn btnRed"><i class="fa fa-question"></i> <?php echo $Module->getLanguage('button/write'); ?></a>
		</td>
	</tr>
	</table>