$(document).on("Qna.ment.init",function(e,form) {
	var toggle = form.find(".btnGroup .toggle");
	for (var i=0, loop=toggle.length;i<loop;i++) {
		$(toggle[i]).find("i").removeClass().addClass("fa "+(form.find("input[name="+$(toggle[i]).attr("data-name")+"]").is(":checked") == true ? "fa-check-square-o" : "fa-square"));
		$(toggle[i]).removeClass("selected").addClass(form.find("input[name="+$(toggle[i]).attr("data-name")+"]").is(":checked") == true ? "selected" : "");
		$(toggle[i]).attr("disabled",form.find("input[name="+$(toggle[i]).attr("data-name")+"]").is(":disabled"));
	}
	
	toggle.on("click",function() {
		var checkbox = $(this).parents("form").find("input[name="+$(this).attr("data-name")+"]");
		
		if (checkbox.is(":checked") == true) {
			checkbox.prop("checked",false);
			$(this).find("i").removeClass("fa-check-square-o").addClass("fa-square");
		} else {
			checkbox.prop("checked",true);
			$(this).find("i").removeClass("fa-square").addClass("fa-check-square-o");
		}
		
		checkbox.triggerHandler("change");
	});
	
	form.find("input[type=checkbox]").on("change",function() {
		if ($(this).is(":checked") == true) {
			form.find(".btnGroup .toggle[data-name="+$(this).attr("name")+"] i").removeClass().addClass("fa fa-check-square-o");
			form.find(".btnGroup .toggle[data-name="+$(this).attr("name")+"]").addClass("selected");
		} else {
			form.find(".btnGroup .toggle[data-name="+$(this).attr("name")+"] i").removeClass().addClass("fa fa-square-o");
			form.find(".btnGroup .toggle[data-name="+$(this).attr("name")+"]").removeClass("selected");
		}
	});
});