	<article class="postArticle">
		<div class="articleContent">
			<div class="voteArea">
				<button type="button" onclick="Qna.vote.good(<?php echo $answer->idx; ?>,this);"<?php echo $voted == 'GOOD' ? ' class="selected"' : ''; ?>><i class="fa fa-caret-up"></i></button>
				
				<div class="liveUpdateQnaVote<?php echo $answer->idx; ?>"><?php echo number_format($answer->vote); ?></div>
				
				<button type="button" onclick="Qna.vote.bad(<?php echo $answer->idx; ?>,this);"<?php echo $voted == 'BAD' ? ' class="selected"' : ''; ?>><i class="fa fa-caret-down"></i></button>
				
			</div>
			
			<div class="postContent">
				<?php echo $answer->content; ?>
				
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
				
				<div class="authorArea">
					<div class="author">
						<div class="frame">
							<?php echo $IM->getModule('member')->getMemberPhoto($answer->midx); ?>
						</div>
						
						<div class="info">
							<div class="nickname"><?php echo $answer->name; ?></div>
							
							<div class="block level">
								<span class="text">LV.<b><?php echo $answer->member->level->level; ?></b></span>
								<span class="bar">
									<span class="percentage" style="width:<?php echo $answer->member->level->exp/$answer->member->level->next*100 < 5 ? 5 : $answer->member->level->exp/$answer->member->level->next*100; ?>%"></span>
									
									<span class="levelDetail">
										<span class="arrowBox"><?php echo number_format($answer->member->level->exp); ?>/<?php echo number_format($answer->member->level->next); ?></span>
									</span>
								</span>
							</div>
							
							<div class="reg_date"><?php echo GetTime('Y-m-d H:i:s',$answer->reg_date); ?></div>
						</div>
						
					</div>
				</div>
				
				<?php if ($use_select == true) { ?>
				<div class="selectButton">
					<div class="help">
						<div class="boxDefault">답변이 도움되셨다면 채택하여 주세요. 답변채택시 <span class="point"><i class="fa fa-rub"></i><?php echo number_format(ceil($qna->select_point/2)); ?></span>가 적립됩니다.</div>
					</div>
					<div class="button">
						<button type="submit" class="btn btnBlue" data-loading="<?php echo $Module->getLanguage('answerWrite/loading'); ?>" onclick="Qna.answer.select(<?php echo $answer->idx; ?>);"><i class="fa fa-check"></i> <?php echo $Module->getLanguage('button/select'); ?></button>
					</div>
				</div>
				<?php } ?>
				
				<?php echo $Module->getMentList($answer->idx); ?>
				<?php echo $Module->getMentWrite($answer->idx); ?>
			</div>
		</div>
	</article>
	