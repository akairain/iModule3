	<div class="footer">
		<div class="inner hidden-xs">
			<div class="container">
				<ul class="sitemap">
					<?php foreach ($menus = $IM->getMenus() as $menu) { if ($menu->menu == 'index' || $menu->menu == 'board') continue; ?>
					<li>
						<ul class="section">
							<li class="title"><a href="<?php echo $IM->getUrl($menu->menu,false); ?>"><?php echo $menu->title; ?></a></li>
							<?php foreach ($IM->getPages($menu->menu) as $page) { ?>
							<li class="subpage"><a href="<?php echo $IM->getUrl($page->menu,$page->page); ?>"><?php echo $page->title; ?></a></li>
							<?php } ?>
						</ul>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		
		<div class="bottom">
			<div class="container" style="background-image:url(<?php echo $IM->getSiteLogo('footer'); ?>);">
				<div class="text">
					<a href="http://blog.coursemos.kr/" class="blog"><i class="fa fa-rss-square"></i></a>
					<a href="http://facebook.com/coursemos.seeds" class="facebook"><i class="fa fa-facebook-official"></i></a>
					<?php if ($IM->language == 'ko') { ?>
					<span class="hidden-xs">㈜유비온 152-746 서울시 구로구 디지털로34길 27 대륭포스트타워 3차 6층 601호<br></span>
					TEL: <a href="tel:0237828714">+82 2 3782 8714</a><span class="visible-xs-inline"><br></span><span class="hidden-xs">&nbsp;&nbsp;&nbsp;&nbsp;</span>E-mail: <a href="mailto:sales@naddle.net">sales@naddle.net</a>
					<?php } else { ?>
					<span class="hidden-xs">Ubion Co., Ltd. 601, 6th Fl., Daeryungpost tower 3cha, Digital-ro 34 gil 27, Guro-gu, Seoul, Republic of Korea (152-746)<br></span>
					TEL: <a href="tel:0237828714">+82 2 3782 8714</a><span class="visible-xs-inline"><br></span><span class="hidden-xs">&nbsp;&nbsp;&nbsp;&nbsp;</span>E-mail: <a href="mailto:sales@naddle.net">sales@naddle.net</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	
	<?php if ($IM->language == 'ko') { ?>
	<button type="button" class="btn btnRed btnQna" data-modal-title="문의하기" data-title="문의제목" data-sender-name="담당자명" data-sender-email="답변받을 메일주소" data-content="문의내용"><i class="fa fa-question-circle"></i> 문의하기</button>
	<?php } else { ?>
	<button type="button" class="btn btnRed btnQna" data-modal-title="Leave a message" data-title="Title" data-sender-name="Name" data-sender-email="E-mail" data-content="Context"><i class="fa fa-send"></i> Leave a message</button>
	<?php } ?>
</div>

<nav id="iModuleSlideMenu" class="sidemenu" role="navigation">
	<div>
		<ul>
			<li class="home">
				<a href="<?php echo $IM->getUrl('index',false); ?>" style="background-image:url(<?php echo $IM->getSiteLogo('slidemenu'); ?>);"><?php echo $IM->language == 'ko' ? '홈으로' : 'HOME'; ?></a>
			</li>
			<?php $menus = $IM->getMenus(); for ($i=0, $loop=count($menus);$i<$loop;$i++) { if ($menus[$i]->menu == 'index' || ($menus[$i]->menu == 'board' && $_SERVER['REMOTE_ADDR'] != '115.89.228.234')) continue; $pages = $IM->getPages($menus[$i]->menu); ?>
			<li class="menu<?php echo $IM->menu == $menus[$i]->menu ? ' opened' : ''; ?>">
				<a href="<?php echo $IM->getUrl($menus[$i]->menu,false); ?>">
					<?php if (count($pages) > 0) { ?><i class="fa fa-chevron-up"></i><i class="fa fa-chevron-down"></i><?php } ?>
					<i class="fa fa-plus"></i><i class="fa fa-minus"></i>
					&nbsp;&nbsp;<?php echo $menus[$i]->title; ?>
				</a>
				<?php if (count($pages) > 0) { ?>
				<ul>
					<?php for ($j=0, $loopj=count($pages);$j<$loopj;$j++) { ?>
					<li class="page"><a href="<?php echo $IM->getUrl($menus[$i]->menu,$pages[$j]->page,false); ?>"><i class="fa fa-caret-right"></i>&nbsp;&nbsp;<?php echo $pages[$j]->title; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
	</div>
</nav>

<script>
$("#iModuleNavigation a, ul.sitemap a").on("click",function(e) {
	if ("<?php echo $IM->menu; ?>" == "board" || "<?php echo $IM->menu; ?>" == "clients") return;
	
	if ($(this).attr("href").indexOf("<?php echo $IM->getUrl($IM->menu,false); ?>") == 0) {
		var link = $(this).attr("href").replace("<?php echo $IM->getUrl($IM->menu,false); ?>","").replace("/","");
		if (link.length == 0) $("html, body").animate({scrollTop:0},"fast");
		else $("html, body").animate({scrollTop:$("#"+link).offset().top - $("#iModuleNavigation").height() - $("#SubTab").height() + 1},"fast");
		
		if (typeof history.pushState !== "undefined") {
			history.pushState("COURSEMOS - "+$(this).text(),"COURSEMOS - "+$(this).text(),$(this).attr("href"));
		}
		document.title = "COURSEMOS - "+$(this).text();
		
		e.preventDefault();
		e.stopPropagation();
	}
});
</script>

<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-65942906-1', 'auto');
ga('send', 'pageview');
</script>

</body>
</html>