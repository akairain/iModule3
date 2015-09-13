<div class="answerWrite">
	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('answerWrite/write'); ?>
	</div>
	
	<?php if ($IM->getModule('member')->isLogged() == false) { ?>
	<table class="writeTable">
	<tr>
		<td class="warning">
			<div class="lineBox"><i class="fa fa-warning"></i> 답변을 입력하려면 먼저 로그인을 하여야 합니다.</div>
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	<?php } elseif ($Module->checkPermission('write_answer') == false) { ?>
	<table class="writeTable">
	<tr>
		<td class="warning">
			<div class="lineBox"><i class="fa fa-warning"></i> 답변을 입력할 권한이 없습니다.</div>
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	<?php } else { ?>
	<table class="writeTable">
	<tr>
		<td class="warning">
			<i class="fa fa-warning"></i> 답변내용은 정보공유를 목적으로 등록 후 수정 및 삭제가 불가능합니다.<br>지속적으로 노출되면 문제가 있을 내용이나 개인정보등이 답변내용에 포함되지 않도록 주의하여 주시기 바랍니다.
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	
	<table class="writeTable">
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setHeight(200)->doLayout(); ?>
			</div>
		</td>
	</tr>
	</table>
	
	<div class="actionButton">
		<div class="help">
			<div class="boxDefault">답변등록시 <span class="point"><i class="fa fa-rub"></i><?php echo number_format($qna->answer_point); ?></span>가 적립되며, 답변채택시 <span class="point"><i class="fa fa-rub"></i><?php echo number_format($qna->select_point + $post->point); ?></span>가 적립됩니다.</div>
		</div>
		<div class="button">
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('answerWrite/loading'); ?>"><i class="fa fa-pencil"></i> <?php echo $Module->getLanguage('answerWrite/write'); ?></button>
		</div>
	</div>
	<?php } ?>
</div>
