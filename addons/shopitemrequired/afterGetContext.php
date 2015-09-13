<?php
if ($target == 'shop') {
	REQUIRE_ONCE $Addon->getPath().'/init.php';
	if ($view == 'seller.item') {
		$context.= PHP_EOL.'<script>var _SHOP_REQUIRED_CATEGORYS = '.json_encode($categorys,JSON_UNESCAPED_UNICODE).';</script>'.PHP_EOL;
		$IM->addSiteHeader('script',$Addon->getDir().'/scripts/script.js');
	}
}
?>