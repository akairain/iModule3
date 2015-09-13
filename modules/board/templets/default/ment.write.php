<div class="mentWrite">
	<div class="contextTitle">
		<i class="fa fa-comment-o"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span>
	</div>
	
	<table class="writeTable">
	<?php if ($IM->getModule('member')->isLogged() == false) { ?>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('name'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="name" class="inputControl" style="width:150px;" required>
				<div class="helpBlock" data-error="<?php echo $Module->getLanguage('mentWrite/help/name/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('password'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="password" name="password" class="inputControl" style="width:150px;" autocomplete="off" required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('mentWrite/help/password/default'); ?>" data-error="<?php echo $Module->getLanguage('mentWrite/help/password/error'); ?>" data-modify="<?php echo $Module->getLanguage('mentModify/password'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('email'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="email" class="inputControl" style="width:250px; max-width:100%;">
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('mentWrite/help/email/default'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<?php } ?>
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setHeight(100)->doLayout(); ?>
			</div>
		</td>
	</tr>
	</table>
	
	<div style="display:none;">
		<?php echo $Module->getOptionForm('mentWrite','is_secret'); ?>
		<?php echo $Module->getOptionForm('mentWrite','is_hidename'); ?>
	</div>
	
	<div class="btnGroup">
		<button type="button" class="btn toggle" data-name="is_secret"><i class="fa fa-square"></i> <?php echo $Module->getLanguage('mentWrite/option_short/is_secret'); ?></button>
		<button type="button" class="btn toggle" data-name="is_hidename"><i class="fa fa-square"></i> <?php echo $Module->getLanguage('mentWrite/option_short/is_hidename'); ?></button>
		<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('mentWrite/loading'); ?>"><i class="fa fa-pencil"></i> <span data-title-write="<?php echo $Module->getLanguage('mentWrite/write'); ?>" data-title-reply="<?php echo $Module->getLanguage('mentWrite/reply'); ?>" data-title-modify="<?php echo $Module->getLanguage('mentWrite/modify'); ?>"></span></button>
	</div>
</div>
