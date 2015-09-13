<?php
if ($target == 'member') {
	$Module = $IM->getModule('member');
	
	if ($action == 'login') {
		if ($results->success == false && isset($values->email) == true && isset($values->password) == true) {
			$check = $Module->db()->select($Module->getTable('member'))->where('email',$values->email)->getOne();
			if ($check != null && $check->status == 'ACTIVE') {
				$mHash = new Hash();
				if ($mHash->password_validate(md5(strtolower($values->password)),$check->password) == true || $mHash->password_validate(md5($values->password),$check->password) == true) {
					$Module->db()->update($Module->getTable('member'),array('password'=>$mHash->password_hash($values->password)))->where('idx',$check->idx)->execute();
					exit(json_encode($Module->doProcess('login'),JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
				}
			}
		}
	}
}
?>