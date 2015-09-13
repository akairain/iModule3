<div class="mentBlock" style="margin-left:<?php echo $ment->depth * 3; ?>%;">
	<div class="mentDepth"></div>
	<?php if ($ment->is_delete == 'TRUE') { ?>
	<div class="deleteMent"><?php echo $Module->getLanguage('error/deletedeMent'); ?></div>
	<?php } else { ?>
	<div class="mentHeader">
		<div class="photo">
			<div class="frame"><?php echo $IM->getModule('member')->getMemberPhoto($ment->midx); ?></div>
		</div>
		
		<div class="info">
			<div class="hidden-xs">
				<div class="postDetail author">
					<span class="block"><?php echo $ment->name; ?></span>
					<span class="block reg_date"><?php echo GetTime('Y.m.d H:i:s',$ment->reg_date); ?></span>
				</div>
				
				<div class="postDetail">
					<span class="level">
						<span class="text">LV.<b><?php echo $ment->member->level->level; ?></b></span>
						<span class="bar">
							<span class="percentage" style="width:<?php echo $ment->member->level->exp/$ment->member->level->next*100 < 5 ? 5 : $ment->member->level->exp/$ment->member->level->next*100; ?>%"></span>
							
							<span class="levelDetail">
								<span class="arrowBox"><?php echo number_format($ment->member->level->exp); ?>/<?php echo number_format($ment->member->level->next); ?></span>
							</span>
						</span>
					</span>
				</div>
			</div>
			
			<div class="visible-xs">
				<div class="postDetail author">
					<span class="block"><?php echo $ment->name; ?></span>
				</div>
				
				<div class="postDetail">
					<span class="block reg_date"><?php echo GetTime('Y.m.d H:i:s',$ment->reg_date); ?></span>
				</div>
			</div>
		</div>
		
		<div class="button">
			<div>
				<button type="button" class="reply" onclick="Forum.ment.reply(<?php echo $ment->idx; ?>,this);" data-cancel="<i class='fa fa-times'></i> <?php echo $Module->getLanguage('button/reply_cancel'); ?>"><i class="fa fa-reply"></i> <?php echo $Module->getLanguage('button/reply'); ?></button>
				<button type="button" class="modify" onclick="Forum.ment.modify(<?php echo $ment->idx; ?>,this);" data-cancel="<i class='fa fa-times'></i> <?php echo $Module->getLanguage('button/modify_cancel'); ?>"><i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('button/modify'); ?></button>
				<button type="button" class="delete" onclick="Forum.ment.delete(<?php echo $ment->idx; ?>,this);"><i class="fa fa-times"></i></button>
			</div>
			<div class="ip">
				<?php echo $ment->ip; ?>
			</div>
		</div>
	</div>
	
	<div class="mentContext">
		<?php echo $ment->content; ?>
		
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
	</div>
	<?php } ?>
</div>