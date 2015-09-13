<?php
if ($target == 'qna') {
	if ($action == 'postWrite') {
		if ((in_array(12,$values->labels) == true || in_array(13,$values->labels) == true) && in_array(11,$values->labels) == false) {
			$values->labels[] = 11;
		}
	}
}
?>