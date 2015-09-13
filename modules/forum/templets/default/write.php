	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage($post === null ? 'postWrite/write' : 'postWrite/modify'); ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('title'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="title" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->title,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-error="<?php echo $Module->getLanguage('postWrite/help/title/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setContent($post !== null ? $post->content : '')->loadFile($post !== null ? $post->attachments : array())->doLayout(); ?>
			</div>
		</td>
	</tr>
	<?php if (count($labels) > 0) { ?>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('label'); ?></td>
		<td class="input">
			<div class="inputInline">
				<?php for ($i=0, $loop=count($labels);$i<$loop;$i++) { ?>
				<label><input type="checkbox" name="labels[]" value="<?php echo $labels[$i]->idx; ?>"<?php echo ($post !== null && in_array($labels[$i]->idx,$post->labels) == true) || (isset($default->label) == true && $default->label == $labels[$i]->idx) ? ' checked' : ''; ?>> <?php echo $labels[$i]->title; ?></label>
				<?php } ?>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<?php } ?>
	<tr class="splitBottom">
		<td colspan="2"><div></div></td>
	</tr>
	</table>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list'); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('postWrite/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage($post === null ? 'button/submit' : 'button/modify'); ?></button>
		</td>
	</tr>
	</table>