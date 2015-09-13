<div class="mentWrite">
	<div class="contextTitle">
		<i class="fa fa-comment-o"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span>
	</div>
	
	<table class="writeTable">
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setHeight(100)->doLayout(); ?>
			</div>
		</td>
	</tr>
	</table>
	
	<div class="actionButton">
		<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('mentWrite/loading'); ?>"><i class="fa fa-pencil"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span></button>
	</div>
</div>
