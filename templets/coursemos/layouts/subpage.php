<?php
$IM->addSiteHeader('link',array('rel'=>'canonical','href'=>$IM->getUrl(null,false,false,false,true)));

if ($IM->language == 'ko') {
	$introText = array(
		'coursemos'=>array('코스모스','학습 플랫폼 서비스'),
		'service'=>array('서비스','코스모스ː학습 플랫폼 서비스 브랜드'),
		'clients'=>array('클라이언트','코스모스 플랫폼 서비스를 경험하다'),
		'introduce'=>array('소개','진지하지만 유쾌한 사람들, 코스모스 팀'),
		'news'=>array('소식','코스모스가 전하고 싶은 이야기')
	);
} else {
	$introText = array(
		'coursemos'=>array('COURSEMOS','Learning Platform Service'),
		'service'=>array('SERVICE','COURSEMOSːBrand for learning platform service'),
		'clients'=>array('Clients','Explore the platform service of COURSEMOS'),
		'introduce'=>array('ABOUT US','Impassioned & Cheerful'),
		'news'=>array('NEWS','More stories on COURSEMOS')
	);
}
?>
	<div class="subHeader <?php echo $IM->menu; ?>">
		<div class="subIntro" style="background-image:url(<?php echo $IM->getTempletDir(); ?>/images/<?php echo $IM->menu; ?>.jpg);">
			<h2><?php echo $introText[$IM->menu][0]; ?></h2>
			<div class="line"></div>
			<h3><?php echo $introText[$IM->menu][1]; ?></h3>
		</div>
	
		<?php $pages = $IM->getPages($IM->menu); if (count($pages) > 0) { ?>
		<div class="subTab">
			<ul id="SubTab">
				<?php foreach ($pages as $page) { ?>
				<li data-tab="<?php echo $page->page; ?>" <?php echo $page->page == $IM->page ? ' class="selected"' : ''; ?>><?php echo $page->title; ?></li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
	</div>
	
	<div class="subContent">
		<div class="container <?php echo $IM->menu; ?>">
			<?php echo $context; ?>
		</div>
	</div>

	<script>
	$(document).ready(function() {
		if ($(document).scrollTop() + $("#iModuleNavigation.fixed").height() + $(".subTab").height() > $(".subContent > .container").offset().top) {
			$(".subHeader").addClass("fixed");
			$(".subContent").addClass("fixed");
		} else {
			$(".subHeader").removeClass("fixed");
			$(".subContent").removeClass("fixed");
		}
		
		$("#SubTab").find("li:first-child").addClass("selected");
		
		if ("<?php echo $IM->menu; ?>" == "clients") return;
		<?php if (Request('page') !== null) { ?>
		var position = $("#<?php echo Request('page'); ?>").offset().top - $("#iModuleNavigation").height() - $("#SubTab").height();
		$("html, body").animate({scrollTop:position + 1},"fast");
		<?php } ?>
	});
	
	$("#SubTab").find("li").on("click",function() {
		if ("<?php echo $IM->menu; ?>" == "board") {
			location.href = "<?php echo $IM->getUrl($IM->menu,false); ?>/"+$(this).attr("data-tab");
			return;
		}
		if ("<?php echo $IM->menu; ?>" == "clients") return;
		
		var position = $("#"+$(this).attr("data-tab")).offset().top - $("#iModuleNavigation").height() - $("#SubTab").height();
		$("html, body").animate({scrollTop:position + 1},"fast");
		
		if (typeof history.pushState !== "undefined") {
			history.pushState("COURSEMOS - "+$(this).text(),"COURSEMOS - "+$(this).text(),"<?php echo $IM->getUrl($IM->menu,false); ?>/"+$(this).attr("data-tab"));
		}
		document.title = "COURSEMOS - "+$(this).text();
	});
	
	$("#iModuleNavigation a").on("click",function(e) {
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
	
	$(document).on("scroll",function() {
		if ($(document).scrollTop() + $("#iModuleNavigation.fixed").height() + $(".subTab").height() > $(".subContent > .container").offset().top) {
			$(".subHeader").addClass("fixed");
			$(".subContent").addClass("fixed");
		} else {
			$(".subHeader").removeClass("fixed");
			$(".subContent").removeClass("fixed");
		}
		
		if ("<?php echo $IM->menu; ?>" == "clients") return;
		
		var scroll = $(document).scrollTop() + $("#iModuleNavigation").height() + $("#SubTab").height();
		var tab = $("#SubTab").find("li");
		var index = 0;
		for (var i=0, loop=tab.length;i<loop;i++) {
			if (scroll >= $("#"+$(tab[i]).attr("data-tab")).offset().top) {
				var index = i;
			} else {
				break;
			}
		}
		
		tab.removeClass("selected");
		$(tab[index]).addClass("selected");
	});
	</script>