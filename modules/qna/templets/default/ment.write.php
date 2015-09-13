<div class="answerWrite">
	<table class="writeTable">
	<tr>
		<td class="warning">
			<i class="fa fa-warning"></i> 댓글내용은 정보공유를 목적으로 등록 후 수정 및 삭제가 불가능합니다.<br>지속적으로 노출되면 문제가 있을 내용이나 개인정보등이 댓글내용에 포함되지 않도록 주의하여 주시기 바랍니다.
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
				<?php $Module->getWysiwyg('content')->setRequired(true)->setHeight(100)->doLayout(); ?>
			</div>
		</td>
	</tr>
	</table>
	
	<input type="checkbox" name="is_secret" value="TRUE" style="display:none;">
	
	<div class="actionButton">
		<div class="btnGroup">
			<button type="button" class="btn toggle" data-name="is_secret"><i class="fa fa-square"></i> 답변자 및 질문자에게만 공개</button>
			<button type="submit" class="btn btnRed" data-loading="등록중..."><i class="fa fa-comment-o"></i> 댓글등록하기</button>
		</div>
	</div>
</div>
