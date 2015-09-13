	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('postWrite/write'); ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td class="warning">
			<i class="fa fa-warning"></i> 질문내용은 정보공유를 목적으로 등록 후 수정 및 삭제가 불가능합니다.<br>지속적으로 노출되면 문제가 있을 내용이나 개인정보등이 질문내용에 포함되지 않도록 주의하여 주시기 바랍니다.
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('title'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="title" class="inputControl" required>
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
				<?php $Module->getWysiwyg('content')->setRequired(true)->doLayout(); ?>
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
				<label><input type="checkbox" name="labels[]" value="<?php echo $labels[$i]->idx; ?>"> <?php echo $labels[$i]->title; ?></label>
				<?php } ?>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<?php } ?>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('point'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="number" name="point" class="inputControl" style="width:100px;">
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('postWrite/help/point/default'); ?>"></div>
			</div>
		</td>
	</tr>
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
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('postWrite/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage('button/submit'); ?></button>
		</td>
	</tr>
	</table>