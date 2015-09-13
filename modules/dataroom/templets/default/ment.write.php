<div class="mentWrite">
	<div class="contextTitle">
		<i class="fa fa-comment-o"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span>
	</div>
	
	<?php if ($IM->getModule('member')->isLogged() == false) { ?>
	<table class="writeTable">
	<tr>
		<td class="warning">
			<div class="lineBox"><i class="fa fa-warning"></i> 댓글을 입력하려면 먼저 로그인을 하여야 합니다.</div>
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	<?php } elseif ($Module->checkPermission('write_ment') == false) { ?>
	<table class="writeTable">
	<tr>
		<td class="warning">
			<div class="lineBox"><i class="fa fa-warning"></i> 댓글을 입력할 권한이 없습니다.</div>
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	<?php } else { ?>
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
		<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('mentWrite/loading'); ?>"><i class="fa fa-pencil"></i>  <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span></button>
	</div>
	<?php } ?>
</div>
