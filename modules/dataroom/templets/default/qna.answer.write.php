<div class="qnaWrite">
	<div class="contextTitle">
		<i class="fa fa-comment-o"></i> <span data-title-write="<?php echo $Module->getLanguage('qnaWrite/writeAnswer'); ?>" data-title-modify="<?php echo $Module->getLanguage('qnaWrite/modifyAnswer'); ?>"></span>
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
	
	<div class="button">
		<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('qnaWrite/loading'); ?>"><i class="fa fa-pencil"></i>  <span data-title-write="<?php echo $Module->getLanguage('mentWrite/writeAnswer'); ?>"></span></button>
	</div>
</div>
