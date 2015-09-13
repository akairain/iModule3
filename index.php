<?php
REQUIRE_ONCE './configs/init.config.php';

$IM = new iModule();

if (Request('loggedIdx')) {
	$_SESSION['MEMBER_LOGGED'] = Encoder(json_encode(array('idx'=>Request('loggedIdx'),'time'=>time(),'ip'=>$_SERVER['REMOTE_ADDR'])));
}

$IM->doLayout();
?>