		<?php if ($IM->language == 'ko') { ?>
		<div id="ir"></div>
		
		<section data-bid="ir" class="column">
			<h2>보도자료(IR)</h2>
			
			<article></article>
		</section>
		
		<div class="splitLine"><div id="notice"></div></div>
		
		<section data-bid="notice" class="column">
			<h2>공지사항</h2>
			
			<article></article>
		</section>
		
		<div class="splitLine"><div id="blog"></div></div>
		
		<section data-type="blog" class="column">
			<h2>블로그</h2>
			
			<article>
				<?php $IM->getWidget('rssreader')->setValue('rss','http://blog.coursemos.kr/rss')->setValue('limit',10)->setTemplet('@blog')->doLayout(); ?>
			</article>
		</section>
		<?php } else { ?>
		<div id="ir"></div>
		
		<section data-bid="ir" class="column">
			<h2>IR</h2>
			
			<article></article>
		</section>
		
		<div class="splitLine"><div id="notice"></div></div>
		
		<section data-bid="notice" class="column">
			<h2>Notice</h2>
			
			<article></article>
		</section>
		
		<div class="splitLine"><div id="blog"></div></div>
		
		<section data-type="blog" class="column">
			<h2>Blog</h2>
			
			<article>
				<?php $IM->getWidget('rssreader')->setValue('rss','http://blog.coursemos.kr/rss')->setValue('limit',10)->setTemplet('@blog')->doLayout(); ?>
			</article>
		</section>
		<?php } ?>
		
		
		<script>
		function getBoardList(bid,page) {
			var limit = 5;
			var pagenum = 5;
			var start = (page - 1) * limit;
			
			$.ajax({
				type:"POST",
				url:ENV.getApiUrl("board","getList"),
				data:{bid:bid,sort:"reg_date",start:start,limit:limit},
				dataType:"json",
				success:function(data) {
					$article = $("section[data-bid="+bid+"] > article");
					$article.empty();
			
					var $list = $("<ul>").addClass("list");
					var $page = $("<ul>").addClass("page").data("bid",bid);
					
					for (var i=0, loop=data.lists.length;i<loop;i++) {
						var reg_date = new Date(data.lists[i].reg_date * 1000).toJSON().substring(0,10).replace(/\-/g,".");
						var $item = $("<li>").html('<span class="reg_date">'+reg_date+'</span>'+data.lists[i].title);
						$item.data("data",data.lists[i]);
						$item.on("click",function() {
							var data = $(this).data("data");
							iModule.modal.show(data.title,data.content,{text:"CLOSE",click:function() { iModule.modal.close(); }});
						});
						$list.append($item);
					}
					
					var p = Math.floor(data.start / data.limit) + 1;
					var totalPage = Math.ceil(data.total / data.limit) == 0 ? 1 : Math.ceil(data.total / data.limit);
					var startPage = Math.floor((p-1)/pagenum) * pagenum + 1;
					var endPage = startPage + pagenum - 1 < totalPage ? startPage + pagenum - 1 : totalPage;
					var prevPageStart = startPage - pagenum > 0 ? startPage - pagenum : false;
					var nextPageStart = endPage + 1 < totalPage ? endPage + 1 : false;
				
					var prevPage = p > 1 ? p - 1 : false;
					var nextPage = p < totalPage ? p + 1 : false;
					
					$prevPageStart = $("<li>").html('<i class="fa fa-angle-double-left"></i>').data("page",prevPageStart);
					if (prevPageStart === false) $prevPageStart.addClass("disabled");
					$page.append($prevPageStart);
					
					$prevPage = $("<li>").html('<i class="fa fa-angle-left"></i>').data("page",prevPage);
					if (prevPage === false) $prevPage.addClass("disabled");
					$page.append($prevPage);
					
					for (var i=startPage;i<=endPage;i++) {
						$paging = $("<li>").html(i).data("page",i);
						if (i == p) $paging.addClass("current");
						$page.append($paging);
					}
					
					$nextPage = $("<li>").html('<i class="fa fa-angle-right"></i>').data("page",nextPage);
					if (nextPage === false) $nextPage.addClass("disabled");
					$page.append($nextPage);
					
					$nextPageStart = $("<li>").html('<i class="fa fa-angle-double-right"></i>');
					if (nextPageStart === false) {
						$nextPageStart.addClass("disabled");
					}
					$page.append($nextPageStart);
					
					$article.append($list);
					$article.append($page);
					
					$page.find("li").on("click",function() {
						if ($(this).hasClass("current") || $(this).hasClass("disabled")) return;
						getBoardList($(this).parent().data("bid"),$(this).data("page"));
					});
				}
			});
		}
		
		$(document).ready(function() {
			getBoardList("ir",1);
			getBoardList("notice",1);
		});
		</script>