function TogglePush(button) {
	if ($(button).hasClass("opened") == true) {
		$(button).removeClass("opened");
		$(button).find("li.item, li.loading").remove();
		$(button).find("ul").append($("<li>").addClass("loading"));
	} else {
		$(button).addClass("opened");
		
		var list = $(button).find("ul");
		Push.getRecently(10,function(result) {
			list.find(".loading").remove();
			for (var i=0, loop=result.lists.length;i<loop;i++) {
				var item = $("<li>").addClass("item").addClass(result.lists.is_read ? "readed" : "unread");
				item.data("link",result.lists[i].link);
				item.on("click",function(e) {
					location.href = $(this).data("link");
					e.stopPropagation();
				});
				if (result.lists[i].image !== null) {
					item.append($("<div>").addClass("image").append($("<img>").attr("src",result.lists[i].image)));
				}
				item.append($("<div>").addClass("content").html(result.lists[i].content));
				list.append(item);
			}
		});
	}
}

$(document).ready(function() {
	$("#iModuleSlideMenu .fa-chevron-up, #iModuleSlideMenu .fa-chevron-down").on("click",function(e) {
		var arrow = $(this);
		var list = $(this).parents("li");
		var subpage = list.find("ul");
			
		if (list.hasClass("opened") == true) {
			var height = subpage.height("auto").height();
			subpage.animate({height:0},{step:function(step) {
				arrow.rotate(180-step/height*180);
				
				if (step == 0) {
					list.removeClass("opened");
					arrow.rotate(0);
				}
			}});
		} else {
			subpage.show();
			var height = subpage.height("auto").height();
			subpage.height(0);
			
			subpage.animate({height:height},{step:function(step) {
				arrow.rotate(step/height*180);
				
				if (step == height) {
					list.addClass("opened");
					arrow.rotate(0);
				}
			}});
		}
		e.preventDefault();
	});
	
	$("div[role=tab] li").on("click",function() {
		var toggleOn = $(this).parents("ul").find(".selected");
		toggleOn.removeClass("selected");
		$("div[role=tabpanel][data-toggle="+toggleOn.attr("data-toggle")+"]").hide();
		
		$(this).addClass("selected");
		$("div[role=tabpanel][data-toggle="+$(this).attr("data-toggle")+"]").show();
	});
	
	$("div[role=tab][data-type=mouseover] li").on("mouseover",function() {
		var toggleOn = $(this).parents("ul").find(".selected");
		toggleOn.removeClass("selected");
		$("div[role=tabpanel][data-toggle="+toggleOn.attr("data-toggle")+"]").hide();
		
		$(this).addClass("selected");
		$("div[role=tabpanel][data-toggle="+$(this).attr("data-toggle")+"]").show();
	});
	
	$("div[data-google-responsive]").each(function() { $(this).attr("data-width",$(this).width()); });
	
	var googleTimeout = null;
	$(window).on("resize",function() {
		$("div[data-google-responsive]").each(function() {
			if (parseInt($(this).attr("data-width")) != $(this).width()) {
				$(this).empty();
				$(this).attr("data-width",$(this).width());
				
				if (googleTimeout != null) {
					clearTimeout(googleTimeout);
					googleTimeout = null;
				}
				
				googleTimeout = setTimeout(ReprintGoogle,1000,$(this));
			}
		});
	});
	
	function ReprintGoogle(object) {
		object.html('<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3210736654114323" data-ad-slot="1232214968" data-ad-format="auto"></ins>');
		(adsbygoogle = window.adsbygoogle || []).push({});
	}
	
	var scrollTimeout = null;
	$(document).on("scroll",function() {
		if (scrollTimeout != null) {
			clearTimeout(scrollTimeout);
			scrollTimeout = null;
		}
		
		if ($(".rightFixed").length == 0) return;
		
		$(".rightFixedInner").stop();
		scrollTimeout = setTimeout(ScrollFixed,100);
	});
	
	function ScrollFixed() {
		if ($(".rightFixed").length == 0) return;
		var startPosition = $(document).scrollTop() + $("#iModuleNavigation.fixed").height() + 10;
		if (startPosition > $(".rightFixed").offset().top) {
			$(".rightFixedInner").addClass("fixed");
			$(".rightFixedInner").stop();
			
			if (startPosition + $(".rightFixedInner").height() > $(".footer").offset().top) {
				$(".rightFixedInner").animate({"top":$(".footer").offset().top - $(".rightFixed").offset().top - $(".rightFixedInner").height()},"fast");
			} else {
				$(".rightFixedInner").animate({"top":startPosition - $(".rightFixed").offset().top},"fast");
			}
		} else {
			$(".rightFixedInner").removeClass("fixed");
			$(".rightFixedInner").css("top",0);
		}
	}
});