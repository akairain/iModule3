	<div class="contextTitle">
		<i class="fa fa-file-text-o"></i> <?php echo $Module->getLanguage('view/title'); ?> <span class="count">#<?php echo number_format($post->idx); ?></span>
	</div>
	<article class="postArticle">
		<div class="postHeader">
			<table>
			<tr>
				<td class="photo">
					<div class="frame"><?php echo $IM->getModule('member')->getMemberPhoto($post->midx); ?></div>
				</td>
				<td class="info">
					<h4><?php echo $post->title; ?></h4>
					
					<div class="hidden-xs">
						<div class="postDetail">
							<span class="block"><?php echo $post->name; ?></span>
							<span class="block level">
								<span class="text">LV.<b><?php echo $post->member->level->level; ?></b></span>
								<span class="bar">
									<span class="percentage" style="width:<?php echo $post->member->level->exp/$post->member->level->next*100 < 5 ? 5 : $post->member->level->exp/$post->member->level->next*100; ?>%"></span>
									
									<span class="levelDetail">
										<span class="arrowBox"><?php echo number_format($post->member->level->exp); ?>/<?php echo number_format($post->member->level->next); ?></span>
									</span>
								</span>
							</span>
							<span class="block reg_date"><?php echo GetTime('Y.m.d H:i:s',$post->reg_date); ?></span>
							<span class="block hit"><i class="fa fa-file-text-o"></i> <?php echo $Module->getLanguage('hit'); ?> <b><?php echo number_format($post->hit); ?></b></span>
							<span class="block ment"><i class="fa fa-comment-o"></i> <?php echo $Module->getLanguage('ment'); ?> <b class="liveUpdateBoardPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></b></span>
							<span class="block good"><i class="fa fa-thumbs-o-up"></i> <?php echo $Module->getLanguage('good'); ?> <b class="liveUpdateBoardPostGood<?php echo $post->idx; ?>"><?php echo number_format($post->good); ?></b></span>
							<span class="block bad"><i class="fa fa-thumbs-o-down"></i> <?php echo $Module->getLanguage('bad'); ?> <b class="liveUpdateBoardPostBad<?php echo $post->idx; ?>"><?php echo number_format($post->bad); ?></b></span>
						</div>
					</div>
					
					<div class="visible-xs">
						<div class="postDetail">
							<span class="block"><?php echo $post->name; ?></span>
							<span class="block level">
								<span class="text">LV.<b><?php echo $post->member->level->level; ?></b></span>
								<span class="bar">
									<span class="percentage" style="width:<?php echo $post->member->level->exp/$post->member->level->next * 100 < 5 ? 5 : $post->member->level->exp/$post->member->level->next*100; ?>%"></span>
									
									<span class="levelDetail">
										<span class="arrowBox"><?php echo number_format($post->member->level->exp); ?>/<?php echo number_format($post->member->level->next); ?></span>
									</span>
								</span>
							</span>
						</div>
					</div>
				</td>
			</tr>
			</table>
		</div>
		
		<div class="postContent">
			<div class="visible-xs" style="margin-bottom:10px;">
				<div class="postDetail">
					<span class="block hit"><i class="fa fa-file-text-o"></i> <?php echo $Module->getLanguage('hit'); ?> <b><?php echo number_format($post->hit); ?></b></span>
					<span class="block ment"><i class="fa fa-comment-o"></i> <?php echo $Module->getLanguage('ment'); ?> <b class="liveUpdateBoardPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></b></span>
					<span class="block good"><i class="fa fa-thumbs-o-up"></i> <?php echo $Module->getLanguage('good'); ?> <b class="liveUpdateBoardPostGood<?php echo $post->idx; ?>"><?php echo number_format($post->good); ?></b></span>
					<span class="block bad"><i class="fa fa-thumbs-o-down"></i> <?php echo $Module->getLanguage('bad'); ?> <b class="liveUpdateBoardPostBad<?php echo $post->idx; ?>"><?php echo number_format($post->bad); ?></b></span>
				</div>
				<div class="postDetail">
					<span class="block reg_date"><?php echo GetTime('Y.m.d H:i:s',$post->reg_date); ?></span>
				</div>
			</div>
			
			<?php echo $post->content; ?>
		</div>
		
		<div class="voteButton">
			<button type="button" onclick="Board.vote.good(<?php echo $post->idx; ?>,this);" class="good<?php echo $voted == 'GOOD' ? ' selected' : ''; ?>"><span><i class="fa fa-thumbs-o-up"></i></span><b class="liveUpdateBoardPostGood<?php echo $post->idx; ?>"><?php echo number_format($post->good); ?></b></button>
			
			<button type="button" onclick="Board.vote.bad(<?php echo $post->idx; ?>,this);" class="bad<?php echo $voted == 'BAD' ? ' selected' : ''; ?>"><span><i class="fa fa-thumbs-o-down"></i></span><b class="liveUpdateBoardPostBad<?php echo $post->idx; ?>"><?php echo number_format($post->bad); ?></b></button>
		</div>
		
		<?php if (count($attachments) > 0) { ?>
		<div class="contextTitle">
			<i class="fa fa-floppy-o"></i> <?php echo $Module->getLanguage('view/attachment'); ?> <span class="count"><?php echo number_format(count($attachments)); ?></span>
		</div>
		
		<ul class="attachment">
			<?php for ($i=0, $loop=count($attachments);$i<$loop;$i++) { $fileIcon = array('image'=>'fa-file-image-o'); ?>
			<li><a href="<?php echo $attachments[$i]->download; ?>" download="<?php echo $attachments[$i]->name; ?>"><span class="filesize">(<?php echo GetFileSize($attachments[$i]->size); ?>)</span><i class="fa <?php echo empty($fileIcon[$attachments[$i]->type]) == true ? 'fa-file-o' : $fileIcon[$attachments[$i]->type]; ?>"></i> <?php echo $attachments[$i]->name; ?></a></li>
			<?php } ?>
		</ul>
		<?php } ?>
		
		<div class="contextTitle">
			<i class="fa fa-comment-o"></i> <?php echo $Module->getLanguage('ment'); ?> <span class="count liveUpdateBoardPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></span>
		</div>
		
		<?php echo $Module->getMentList($idx); ?>
		
		<div class="mentBottom">
			<?php echo $Module->getMentPagination($idx); ?>
		</div>
		
		<div class="postMentWrite">
			<?php echo $Module->getMentWrite($idx); ?>
		</div>
	</article>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list').$IM->getQueryString(); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<button type="button" class="btn btnWhite" onclick="Board.post.modify(<?php echo $idx; ?>);"><i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('button/modify'); ?></button>
			<button type="button" class="btn btnRed" onclick="Board.post.delete(<?php echo $idx; ?>);"><i class="fa fa-trash-o"></i> <?php echo $Module->getLanguage('button/delete'); ?></button>
		</td>
	</tr>
	</table>