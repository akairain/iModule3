<?php echo $pagination->html; ?>

<button type="button" class="btn btnRed" onclick="Dataroom.qna.write(<?php echo $parent; ?>);"><i class="fa fa-question-circle"></i> <?php echo $Module->getLanguage('qnaWrite/write'); ?></button>