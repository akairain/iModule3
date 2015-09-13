<div class="qnaBlock" onclick="Dataroom.qna.view(<?php echo $qna->idx; ?>);">
	<div class="photo"><div class="frame"><?php echo $IM->getModule('member')->getMemberPhoto($qna->midx); ?></div></div>
	<div class="item">
		<div class="title"><?php echo $qna->has_answer == 'TRUE' ? '<span class="badge">답변완료</span> ' : ''; ?><?php echo $qna->title; ?></div>
		<div class="detail"><?php echo $qna->name; ?> | <span class="reg_date"><?php echo GetTime('Y-m-d H:i:s',$qna->reg_date); ?></span></div>
	</div>
</div>