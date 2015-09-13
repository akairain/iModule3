$(document).ready(function() {
	$(".ModuleLmsDefault .classCover").on("mouseover",function() {
		$(this).parents(".classImage").find(".info").stop();
		$(this).parents(".classImage").find(".info").animate({height:25},"fast");
	});
	
	$(".ModuleLmsDefault .classCover").on("mouseout",function() {
		$(this).parents(".classImage").find(".info").stop();
		$(this).parents(".classImage").find(".info").animate({height:0},"fast");
	});
});