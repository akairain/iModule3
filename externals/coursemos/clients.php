<?php
$clients = $IM->db('default','coursemos_')->select('clients_table')->orderBy('year','asc')->get();
?>
		<?php $IM->addSiteHeader('script',$IM->getTempletDir().'/scripts/jquery.isotope.min.js'); ?>
		<?php if ($IM->language == 'ko') { ?>
		<div id="all"></div>
		
		<section class="column">
			<h2>클라이언트</h2>
			
			<article>
				<p>
					현재 스무 개 이상의 학교/기관/기업에서 코스모스 학습 플랫폼 서비스를 경험하고 있습니다.<br>
					코스모스는 국내 뿐만 아니라 해외로 그 영역을 더욱 넓혀나가고 있습니다.
				</p>
			</article>
		</section>
		
		<style>
		
		</style>
		
		<section class="listContainer">
			<ul class="list">
				<?php $loop = 0; $latest = 0; foreach ($clients as $client) { $loop++; ?>
				<?php if ($latest != $client->year) { $latest = $client->year; ?>
				<li class="item university corporation corporation abroad yearbox">
					<article>
						<div class="logo"></div>
						<div class="box">
							<div class="center">
								<h4><?php echo $client->year; ?> <i class="fa fa-angle-right"></i></h4>
							</div>
						</div>
					</article>
				</li>
				<?php } ?>
				<li class="item <?php echo str_replace(',',' ',$client->category); ?>">
					<article>
						<div class="logo" style="background-image:url(<?php echo $IM->getTempletDir().'/images/clients/'.$client->idx.'.png'; ?>);"></div>
						<div class="box">
							<h4 class="name"><?php echo $client->title; ?></h4>
							<div class="line"></div>
							<div class="year"><?php echo $client->year; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
				<li class="item university corporation corporation abroad yearbox">
					<article>
						<div class="logo"></div>
						<div class="box <?php echo $client->year % 2 == 0 ? 'even' : 'odd'; ?>">
							<div class="center">
								<h4><i class="fa fa-ellipsis-h"></i></h4>
							</div>
						</div>
					</article>
				</li>
			</ul>
		</section>
		<?php } else { ?>
		<div id="all"></div>
		
		<section class="column">
			<h2>Clients</h2>
			
			<article>
				<p>
					Around thirty organizations (university/institute/corporate) around the world already experienced our learning platform service. We are dying the world the color of COURSEMOS!
				</p>
			</article>
		</section>
		
		<style>
		
		</style>
		
		<section class="listContainer">
			<ul class="list">
				<?php $loop = 0; $latest = 0; foreach ($clients as $client) { $loop++; ?>
				<?php if ($latest != $client->year) { $latest = $client->year; ?>
				<li class="item university corporation corporation abroad yearbox">
					<article>
						<div class="logo"></div>
						<div class="box">
							<div class="center">
								<h4><?php echo $client->year; ?> <i class="fa fa-angle-right"></i></h4>
							</div>
						</div>
					</article>
				</li>
				<?php } ?>
				<li class="item <?php echo str_replace(',',' ',$client->category); ?>">
					<article>
						<div class="logo" style="background-image:url(<?php echo $IM->getTempletDir().'/images/clients/'.$client->idx.'.png'; ?>);"></div>
						<div class="box">
							<h4 class="name"><span style="font-size:0.8em;"><?php echo $client->title_en; ?></span></h4>
							<div class="line"></div>
							<div class="year"><?php echo $client->year; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
				<li class="item university corporation corporation abroad yearbox">
					<article>
						<div class="logo"></div>
						<div class="box <?php echo $client->year % 2 == 0 ? 'even' : 'odd'; ?>">
							<div class="center">
								<h4><i class="fa fa-ellipsis-h"></i></h4>
							</div>
						</div>
					</article>
				</li>
			</ul>
		</section>
		<?php } ?>
		<script>
		$(".team article, .partner article, .ubion .circle").on("touchstart",function() {
			$(this).trigger("hover");
		});
		
		var $grid = null;
		function filterClients(filter) {
			$(".listContainer .list").addClass("animate");
			
			if (filter) {
				var position = $("#all").offset().top - $("#iModuleNavigation").height() - $("#SubTab").height();
				$("html, body").animate({scrollTop:position + 1},"fast");
			}
			
			var filter = filter == "all" || filter == "" ? "*" : "."+filter;
			$grid.isotope({filter:filter});
			
			$("#SubTab li").removeClass("selected");
			if (filter == "*") {
				$("#SubTab li[data-tab=all]").addClass("selected");
			} else {
				$("#SubTab li[data-tab="+filter.replace(".","")+"]").addClass("selected");
			}
		}
		
		$("#SubTab").find("li").on("click",function() {
			if (typeof history.pushState !== "undefined") {
				history.pushState("COURSEMOS - "+$(this).text(),"COURSEMOS - "+$(this).text(),"<?php echo $IM->getUrl($IM->menu,false); ?>/"+$(this).attr("data-tab"));
			}
			
			document.title = "COURSEMOS - "+$(this).text();
			filterClients($(this).attr("data-tab"));
		});
		
		$("#iModuleNavigation a").on("click",function(e) {
			if ($(this).attr("href").indexOf("<?php echo $IM->getUrl($IM->menu,false); ?>") == 0) {
				var filter = $(this).attr("href").split("clients").pop().split("/").pop();
				var link = "";
				if (filter) link = "/"+filter;
				
				if (typeof history.pushState !== "undefined") {
					history.pushState("COURSEMOS - "+$(this).text(),"COURSEMOS - "+$(this).text(),"<?php echo $IM->getUrl($IM->menu,false); ?>"+link);
				}
				document.title = "COURSEMOS - "+$(this).text();
				filterClients(filter);
				
				e.preventDefault();
				e.stopPropagation();
			}
		});
		
		$(window).on("popstate",function() {
			var filter = location.href.split("clients").pop().split("/").pop();
			filterClients(filter);
		});
		
		$(document).ready(function() {
			$grid = $(".listContainer .list").isotope({
				itemSelector:".item",
				resizable:true,
				resizesContainer:true,
				transitionDuration:"0.8s"
			});
			
			$grid.on("layoutComplete",function() {
				$(".listContainer .list").removeClass("animate");
			});
			
			var filter = location.href.split("clients").pop().split("/").pop();
			setTimeout(filterClients,50,filter);
		});
		</script>