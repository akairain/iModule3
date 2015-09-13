<div class="mentContext">
	<?php if ($ment->midx == $question->midx) { ?>
	<div class="photo">
		<div class="frame"><?php echo $IM->getModule('member')->getMemberPhoto($ment->midx); ?></div>
	</div>
	<?php } ?>
	
	<div class="message <?php echo $ment->midx == $question->midx ? 'question' : 'answer'; ?>">
		<div class="name"><?php echo $IM->getModule('member')->getMemberNickname($ment->midx); ?></div>
		<div class="balloon">
			
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
	</div>
	
	<?php if ($ment->midx == $answer->midx) { ?>
	<div class="photo">
		<div class="frame"><?php echo $IM->getModule('member')->getMemberPhoto($ment->midx); ?></div>
	</div>
	<?php } ?>
</div>