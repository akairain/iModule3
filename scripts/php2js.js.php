<?php
REQUIRE_ONCE '../configs/init.config.php';
header('Content-Type: application/x-javascript; charset=utf-8');

$menu = Request('menu');
$page = Request('page');
$view = Request('view');
$IM = new iModule();
?>
var ENV = {
	DIR:"<?php echo __IM_DIR__; ?>",
	VERSION:"<?php echo __IM_VERSION__; ?>",
	LANGUAGE:"<?php echo $IM->language; ?>",
	MENU:<?php echo $menu ? '"'.$menu.'"' : 'null'; ?>,
	PAGE:<?php echo $page ? '"'.$page.'"' : 'null'; ?>,
	VIEW:<?php echo $view ? '"'.$view.'"' : 'null'; ?>,
	getProcessUrl:function(module,action) {
		return ENV.DIR+"/"+ENV.LANGUAGE+"/process/"+module+"/"+action;
	},
	getApiUrl:function(module,api) {
		return this.DIR+"/"+ENV.LANGUAGE+"/api/"+module+"/"+api;
	},
	getUrl:function(menu,page,view,number) {
		menu = menu === null ? ENV.MENU : menu;
		page = page === null && menu == ENV.MENU ? ENV.PAGE : page;
		view = view === null && menu == ENV.MENU && page == ENV.PAGE ? ENV.VIEW : view;
		
		var url = ENV.DIR;
		url+= "/" + ENV.LANGUAGE;
		if (menu === null || menu === false) return url;
		url+= "/" + menu;
		if (page === null || page === false) return url;
		url+= "/" + page;
		if (view === null || view === false) return url;
		url+= "/" + view;
		if (number === null) return url;
		url+= "/" + number;
		
		return url;
	}
};