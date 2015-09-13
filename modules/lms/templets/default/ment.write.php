<div class="mentWrite">
	<input type="text" name="position">
	
	<div class="mentInput">
		<div class="photo"><img src="<?php echo $IM->getModule('member')->getMember()->photo; ?>"></div>
		
		<div class="textInput">
			<textarea class="textareaControl"></textarea>
		</div>
	</div>
	
	<div class="actionButton">
		<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('mentWrite/loading'); ?>"><i class="fa fa-pencil"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span></button>
	</div>
</div>
