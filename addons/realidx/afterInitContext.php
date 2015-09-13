<?php
if ($target == 'board') {
	if ($view == 'list') {
		for ($i=0, $loop=sizeof($values->lists);$i<$loop;$i++) {
			$values->lists[$i]->loopnum = $values->lists[$i]->idx;
		}
	}
}
?>