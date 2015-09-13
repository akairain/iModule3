var iModule = {
	focusDelay:function(object,time) {
		if (time !== 0) {
			time = time ? time : 500;
			setTimeout(iModule.focusDelay,time,object,0);
			return;
		} else {
			object.focus();
		}
	},
	isInScroll:function(object) {
		return object.offset().top + 100 < $(window).height() + $(document).scrollTop() && object.offset().top - 100 > $(document).scrollTop();
	},
	selectFieldEvent:function() {
		var list = $(this).data("control").find("li[data-value='"+$(this).val()+"']");
		if (list.length > 0) {
			$(this).data("control").find("button").html(list.html()+' <span class="arrow"></span>');
		}
	},
	getNumberFormat:function(number,round_decimal) {
		var number = parseInt(number);
		return number.toFixed(round_decimal).replace(/(\d)(?=(\d{3})+$)/g, "$1,");
	},
	initSelectControl:function(selectControl) {
		if (selectControl.is("div") == false) return;
		if (selectControl.attr("data-field").indexOf("#") == 0) {
			var selectField = $(selectControl.attr("data-field"));
		} else {
			var selectField = selectControl.parents("form").find("input[name="+selectControl.attr("data-field")+"]");
		}
		selectControl.data("field",selectField);
		selectField.data("control",selectControl);
		
		var selectValue = selectField.val();
		
		if (selectValue.length > 0 && selectControl.find("li[data-value='"+selectValue+"']").length > 0) {
			selectControl.find("button").html(selectControl.find("li[data-value='"+selectValue+"']").html()+' <span class="arrow"></span>');
		}
		
		selectField.off("change",iModule.selectFieldEvent);
		selectField.on("change",iModule.selectFieldEvent);
	
		selectControl.find("button").attr("type","button");
		selectControl.find("button").off("click");
		selectControl.find("button").on("click",function(event) {
			if ($(this).parents("div.selectControl").hasClass("selectControlExtend") == true) {
				$(this).parents("div.selectControl").removeClass("selectControlExtend");
				$(this).parents("div.selectControl").find("li:not(.divider):visible").attr("tabindex",null);
			} else {
				$(this).parents("div.selectControl").addClass("selectControlExtend");
				if ($(this).parents("div.selectControl").attr("value") !== undefined) {
					$(this).parents("div.selectControl").find("li:not(.divider):visible").attr("tabindex",1);
					iModule.focusDelay($(this).parents("div.selectControl").find("li[data-value='"+$(this).parents("div.selectControl").attr("value")+"']"),100);
				}
			}
			$(this).focus();
			event.preventDefault();
		});
		
		selectControl.find("button").off("keydown");
		selectControl.find("button").on("keydown",function(event) {
			if (event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 27) {
				event.preventDefault();
				if ($(this).parents("div.selectControl").hasClass("selectControlExtend") == false || ($(this).parents("div.selectControl").hasClass("selectControlExtend") == true && event.keyCode == 27)) {
					return $(this).click();
				}
				
				var items = $(this).parents("div.selectControl").find("li:not(.divider):visible").attr("tabindex",1);
				if (items.length == 0) return;
				
				var index = items.index(items.filter(":focus"));
	
				if (event.keyCode == 38 && index > 0) index--;
				if (event.keyCode == 40 && index < items.length - 1) index++;
				if (!~index) index = 0;
				
				$(items).eq(index).focus();
			}
		});
		
		selectControl.find("ul > li").off("keydown");
		selectControl.find("ul > li").on("keydown",function(event) {
			event.preventDefault();
			
			if (event.keyCode == 38 || event.keyCode == 40 || event.keyCode == 27) {
				if ($(this).parents("div.selectControl").length == 0 || ($(this).parents("div.selectControl").hasClass("selectControlExtend") == true && event.keyCode == 27)) {
					return $($(this).parents("div.selectControl").find("button")).click();
				}
				
				var items = $(this).parents("div.selectControl").find("li:not(.divider):visible");
	
				if (items.length == 0) return;
				
				var index = items.index(items.filter(":focus"));
	
				if (event.keyCode == 38 && index > 0) index--;
				if (event.keyCode == 40 && index < items.length - 1) index++;
				if (!~index) index = 0;
				
				$(items).eq(index).focus();
				event.preventDefault();
			}
			
			if (event.keyCode == 13) {
				var items = $(this).parents("div.selectControl").find("li:not(.divider):visible");
				var index = items.index(items.filter(":focus"));
				if (!~index) return;
				
				$(items).eq(index).click();
				event.preventDefault();
			}
		});
		
		selectControl.find("ul > li").off("keyword");
		selectControl.find("ul > li").on("click",function(event) {
			if ($(this).hasClass("divider") == true) return;
			
			$(this).parents("div.selectControl").data("field").val($(this).attr("data-value"));
			$(this).parents("div.selectControl").data("field").triggerHandler("change");
			
			$($(this).parents("div.selectControl").find("button")).click();
			$($(this).parents("div.selectControl").find("button")).focus();
		});
	},
	inputStatus:function(object,status,message) {
		if (object.is("form") == true) object = object.find("input, textarea");
		if (object.is("input, textarea") == false) return;
		if (object.parents(".inputBlock, .inputInline").length == 0) return;
		
		if (object.length > 1) {
			for (var i=0, loop=object.length;i<loop;i++) {
				iModule.inputStatus($(object[i]),status,message);
			}
		} else {
			var inputBlock = object.parents(".inputBlock, .inputInline");
			var helpBlock = inputBlock.find(".helpBlock");
			
			inputBlock.removeClass("hasSuccess hasError");
			if (status == "success") {
				inputBlock.addClass("hasSuccess");
				
				if (helpBlock.length == 1) {
					if (message !== undefined) helpBlock.html(message);
					else if (helpBlock.attr("data-success") !== undefined) helpBlock.html(helpBlock.attr("data-success"));
				}
			} else if (status == "error") {
				inputBlock.addClass("hasError");
				if (helpBlock.length == 1) {
					if (message !== undefined) helpBlock.html(message);
					else if (helpBlock.attr("data-error") !== undefined) helpBlock.html(helpBlock.attr("data-error"));
				} else if (message !== undefined) {
					iModule.alertMessage.show("error",message,5);
				}
			} else if (status == "default") {
				if (message !== undefined) helpBlock.html(message);
				else if (helpBlock.attr("data-default") !== undefined) helpBlock.html(helpBlock.attr("data-default"));
			}
		}
	},
	buttonStatus:function(object,status) {
		if (object.is("button") == true) var button = object;
		else if (object.is("form") == true) var button = object.find("button[type=submit]");
		else return;
		if (button.length == 0) return;
		
		if (status == "loading") {
			button.data("default",button.html());
			var text = '<i class="fa fa-spin fa-spinner"></i>';
			if (button.attr("data-loading") !== undefined) text+= ' '+button.attr("data-loading");
			else text+= ' Loading...';
			button.html(text).attr("disabled",true);
		} else if (status == "reset") {
			if (button.data("default") !== undefined) {
				button.html(button.data("default"));
			}
			button.attr("disabled",false);
		}
	},
	alertMessage:{
		idx:0,
		show:function(type,message,timer) {
			if ($("#iModuleAlertMessage").length == 0) {
				alert(message);
			} else {
				var idx = iModule.alertMessage.idx;
				var item = $("<div>").attr("id","iModuleAlertMessageItem-"+iModule.alertMessage.idx).addClass(type).addClass("message").css("display","none");
				item.html(message);
				var close = $("<div>").addClass("close").append($("<i>").addClass("fa fa-times-circle"));
				close.data("idx",idx);
				close.on("click",function() {
					iModule.alertMessage.close($(this).data("idx"));
				});
				item.append(close);
				
				$("#iModuleAlertMessage").append(item);
				iModule.alertMessage.idx++;
				
				iModule.alertMessage.slideDown(idx);
				setTimeout(iModule.alertMessage.close,timer * 1000,idx);
			}
		},
		slideDown:function(idx) {
			$("#iModuleAlertMessageItem-"+idx).slideDown();
		},
		close:function(idx) {
			if ($("#iModuleAlertMessageItem-"+idx).length > 0) {
				$("#iModuleAlertMessageItem-"+idx).find(".close").css("visibility","hidden");
				$("#iModuleAlertMessageItem-"+idx).width($("#iModuleAlertMessageItem-"+idx).width());
				$("#iModuleAlertMessageItem-"+idx).animate({marginLeft:-$("#iModuleAlertMessageItem-"+idx).outerWidth(true),opacity:0},"",function() {
					$(this).remove();
				});
			}
		},
		progress:function(id,loaded,total) {
			if ($("#iModuleAlertMessage").length == 0) return;
			
			if (total > 0 && loaded < total) {
				if ($("#iModuleAlertMessageProgress-"+id).length == 0) {
					$("#iModuleAlertMessage").append($("<div>").addClass("progress").attr("id","iModuleAlertMessageProgress-"+id).append($("<span>")));
				}
				$("#iModuleAlertMessageProgress-"+id+" > span").css("width",(loaded/total*100)+"%");
			} else {
				if ($("#iModuleAlertMessageProgress-"+id).length == 0) return;
				
				$("#iModuleAlertMessageProgress-"+id+" > span").css("width","100%");
				$("#iModuleAlertMessageProgress-"+id).fadeOut(3000,function() {
					$(this).remove();
				});
			}
		}
	},
	modal:{
		modal:null,
		enable:function() {
			var scroll = parseInt($("#iModuleWrapper").css("marginTop").replace("px","")) * -1;
			
			$("#iModuleWindowDisabled").remove();
			$("#iModuleWrapper").css("paddingLeft",0);
			$("body").css("overflow","auto");
			$("#iModuleWrapper").css("position","static");
			$("#iModuleWrapper").css("marginTop",0);
			
			$(document).scrollTop(scroll);
			
			if ($("#iModuleNavigation.fixed").length == 1) {
				$("#iModuleNavigation.fixed").css("left",0);
			}
			
			$(document).triggerHandler("modal.enable");
		},
		disable:function(disableNavigation,callback) {
			if ($("#iModuleWindowDisabled").length == 0) {
				$("#iModuleWrapper").append($("<div>").attr("id","iModuleWindowDisabled"));
			}
			
			if (typeof callback == "function") {
				$("#iModuleWindowDisabled").on("click",callback);
			}
			
			if (disableNavigation == true) {
				$("#iModuleWindowDisabled").css("zIndex",3000);
			} else {
				$("#iModuleWindowDisabled").css("zIndex",1000);
			}
			
			var width = $(document).width();
			var scroll = $(document).scrollTop();
			
			$("body").css("overflow","hidden");
			$("#iModuleWrapper").css("marginTop",-scroll).css("position","fixed");
			$("#iModuleWrapper").css("width","100%");
			
			if (disableNavigation == true) {
				if ($(document).width() - width > 0) {
					$("body").css("overflowY","scroll");
				} else {
					$("#iModuleWrapper").css("paddingLeft",$(document).width() - width);
				}
			}
			
			if ($("#iModuleNavigation.fixed").length == 1) {
				$("#iModuleNavigation.fixed").css("left",-($(document).width() - width)/2);
			}
			$("#iModuleWindowDisabled").height($(window).height());
			
			$(document).triggerHandler("modal.disable",[disableNavigation,$(document).width() - width]);
		},
		show:function(title,contentHtml,submit,buttons) {
			iModule.modal.disable(true);
			
			iModule.modal.modal = $("<div>").addClass("modal").attr("data-role","modal");
			
			var header = $("<header>").html(title);
			var close = $("<i>").addClass("fa fa-times");
			close.on("click",function() {
				iModule.modal.close();
			});
			header.prepend(close);
			
			iModule.modal.modal.append(header);
			
			var content = $("<div>").addClass("content");
			content.html(contentHtml);
			content.find("img").each(function() {
				$(this).on("load",function() {
					if ($(this).parents(".content").width() < $(this).width()) {
						$(this).width($(this).parents(".content").width());
					}
					iModule.modal.center();
				});
			});
			
			iModule.modal.modal.append(content);
			
			var button = $("<div>").addClass("button");
			if ($.isArray(buttons) == true) {
				for (var i=0, loop=buttons.length;i<loop;i++) {
					var item = $("<button>").addClass(buttons[i].style ? buttons[i].style : "default").html(buttons[i].text);
					item.on("click",buttons[i].click);
					button.append($("<div>").append(item));
				}
			}
			
			var submitButton = $("<button>").addClass(submit.style ? submit.style : "submit").html(submit.text);
			if (submit.type) submitButton.attr("type",submit.type);
			submitButton.on("click",submit.click);
			button.append($("<div>").append(submitButton));
			
			iModule.modal.modal.append(button);
			
			$("#iModuleWindowDisabled").append(iModule.modal.modal);
			iModule.modal.modal.data("width",iModule.modal.modal.outerWidth());
			
			if ($("#iModuleWindowDisabled").innerWidth() > iModule.modal.modal.data("width")) {
				iModule.modal.modal.outerWidth(iModule.modal.modal.data("width"));
			} else {
				iModule.modal.modal.outerWidth($("#iModuleWindowDisabled").innerWidth() - 20);
			}
			
			if ($("#iModuleWindowDisabled").innerHeight() > iModule.modal.modal.outerHeight() + 40) {
				iModule.modal.modal.css("marginTop",($("#iModuleWindowDisabled").innerHeight() - iModule.modal.modal.outerHeight()) / 2);
			} else {
				iModule.modal.modal.css("margin","20px auto");
				$("#iModuleWindowDisabled").css("overflowY","scroll");
			}
			
			$(document).triggerHandler("modal.show",iModule.modal.modal);
		},
		showForm:function(title,contentHtml,submit,buttons) {
			iModule.modal.disable(true);
			
			iModule.modal.modal = $("<div>").addClass("modal").attr("data-role","modal");
			
			var header = $("<header>").html(title);
			var close = $("<i>").addClass("fa fa-times");
			close.on("click",function() {
				iModule.modal.close();
			});
			header.prepend(close);
			
			iModule.modal.modal.append(header);
			
			var content = $("<div>").addClass("content");
			content.html(contentHtml);
			content.find("img").each(function() {
				$(this).on("load",function() {
					if ($(this).parents(".content").width() < $(this).width()) {
						$(this).width($(this).parents(".content").width());
					}
					iModule.modal.center();
				});
			});
			
			iModule.modal.modal.append(content);
			
			var button = $("<div>").addClass("button");
			if ($.isArray(buttons) == true) {
				for (var i=0, loop=buttons.length;i<loop;i++) {
					var item = $("<button>").addClass(buttons[i].style ? buttons[i].style : "default").html(buttons[i].text);
					item.on("click",buttons[i].click);
					button.append($("<div>").append(item));
				}
			}
			
			var submitButton = $("<button>").attr("type","submit").addClass(submit.style ? submit.style : "submit").html(submit.text);
			button.append($("<div>").append(submitButton));
			
			iModule.modal.modal.append(button);
			
			var form = $("<form>");
			form.on("submit",function() {
				submit.fn($(this));
				return false;
			});
			form.append(iModule.modal.modal);
			$("#iModuleWindowDisabled").append(form);
			iModule.modal.modal.data("width",iModule.modal.modal.outerWidth());
			
			if ($("#iModuleWindowDisabled").innerWidth() > iModule.modal.modal.data("width")) {
				iModule.modal.modal.outerWidth(iModule.modal.modal.data("width"));
			} else {
				iModule.modal.modal.outerWidth($("#iModuleWindowDisabled").innerWidth() - 20);
			}
			
			if ($("#iModuleWindowDisabled").innerHeight() > iModule.modal.modal.outerHeight() + 40) {
				iModule.modal.modal.css("marginTop",($("#iModuleWindowDisabled").innerHeight() - iModule.modal.modal.outerHeight()) / 2);
			} else {
				iModule.modal.modal.css("margin","20px auto");
				$("#iModuleWindowDisabled").css("overflowY","scroll");
			}
			
			$(document).triggerHandler("modal.show",iModule.modal.modal);
		},
		showHtml:function(html) {
			iModule.modal.disable(true);
			
			$("#iModuleWindowDisabled").html(html);
			
			$("#iModuleWindowDisabled").find("img").each(function() {
				$(this).on("load",function() {
					if ($(this).parents(".content").width() < $(this).width()) {
						$(this).width($(this).parents(".content").width());
					}
					iModule.modal.center();
				});
			});
			
			iModule.modal.modal = $("#iModuleWindowDisabled div[data-role=modal]");
			iModule.modal.modal.data("width",iModule.modal.modal.outerWidth());
			
			if ($("#iModuleWindowDisabled").innerWidth() > iModule.modal.modal.data("width")) {
				iModule.modal.modal.outerWidth(iModule.modal.modal.data("width"));
			} else {
				iModule.modal.modal.outerWidth($("#iModuleWindowDisabled").innerWidth() - 20);
			}
			
			if ($("#iModuleWindowDisabled").innerHeight() > iModule.modal.modal.outerHeight() + 40) {
				iModule.modal.modal.css("marginTop",($("#iModuleWindowDisabled").innerHeight() - iModule.modal.modal.outerHeight()) / 2);
			} else {
				iModule.modal.modal.css("margin","20px auto");
				$("#iModuleWindowDisabled").css("overflowY","scroll");
			}
			
			$(document).triggerHandler("modal.show",iModule.modal.modal);
		},
		center:function() {
			if (iModule.modal.modal == null) return;
			
			if ($("#iModuleWindowDisabled").innerWidth() > iModule.modal.modal.data("width")) {
				iModule.modal.modal.outerWidth(iModule.modal.modal.data("width"));
			} else {
				iModule.modal.modal.outerWidth($("#iModuleWindowDisabled").innerWidth() - 20);
			}
			
			if ($("#iModuleWindowDisabled").innerHeight() > iModule.modal.modal.outerHeight() + 40) {
				iModule.modal.modal.css("marginTop",($("#iModuleWindowDisabled").innerHeight() - iModule.modal.modal.outerHeight()) / 2);
			} else {
				iModule.modal.modal.css("margin","20px auto");
				$("#iModuleWindowDisabled").css("overflowY","scroll");
			}
		},
		close:function() {
			if (iModule.modal.modal == null) return;
			
			$(document).triggerHandler("modal.close",iModule.modal.modal);
			
			iModule.modal.modal.remove();
			iModule.modal.enable();
		}
	},
	slideMenu:{
		hide:function() {
			if ($("#iModuleSlideMenu").is(":visible") == true) {
				iModule.slideMenu.toggle(false);
				$(document).triggerHandler("slideMenu.hide",$("#iModuleSlideMenu"));
			}
		},
		toggle:function(animate) {
			if ($("#iModuleSlideMenu").length == 0) return;
			
			if ($("#iModuleSlideMenu").is(":visible") == true) {
				if (animate == true) {
					$("#iModuleSlideMenu").animate({right:-$("#iModuleSlideMenu").outerWidth(true)},{step:function(now) {
						$("#iModuleWrapper").css("left",-($("#iModuleSlideMenu").outerWidth(true)+now));
						
						if ($("#iModuleNavigation.fixed").length == 1) {
							$("#iModuleNavigation").css("left",-($("#iModuleSlideMenu").outerWidth(true)+now));
						}
						
						$(document).triggerHandler("slideMenu.animate",now);
						
						if (now == -$("#iModuleSlideMenu").outerWidth(true)) {
							iModule.modal.enable();
							$("#iModuleSlideMenu").hide();
							$(document).triggerHandler("slideMenu.hide",$("#iModuleSlideMenu"));
						}
					}});
				} else {
					$("#iModuleSlideMenu").css("right",-$("#iModuleSlideMenu").outerWidth(true));
					$("#iModuleWrapper").css("left",0);
					if ($("#iModuleNavigation.fixed").length == 1) {
						$("#iModuleNavigation").css("left",0);
					}
					iModule.modal.enable();
					$("#iModuleSlideMenu").hide();
					$(document).triggerHandler("slideMenu.hide",$("#iModuleSlideMenu"));
				}
			} else {
				$("#iModuleSlideMenu").show();
				$("#iModuleSlideMenu").css("right",-$("#iModuleSlideMenu").outerWidth(true));
				
				iModule.modal.disable(false,function() { iModule.slideMenu.toggle(true); });
				
				$("#iModuleSlideMenu").height($(window).height());
				
				if (animate == true) {
					$("#iModuleSlideMenu").animate({right:0},{step:function(now) {
						$("#iModuleWrapper").css("left",-($("#iModuleSlideMenu").outerWidth(true)+now));
						if ($("#iModuleNavigation.fixed").length == 1) {
							$("#iModuleNavigation").css("left",-($("#iModuleSlideMenu").outerWidth(true)+now));
						}
						$(document).triggerHandler("slideMenu.animate",now);
						if (now == $("#iModuleSlideMenu").outerWidth(true)) {
							$(document).triggerHandler("slideMenu.show",$("#iModuleSlideMenu"));
						}
					}});
				} else {
					$("#iModuleSlideMenu").css("right",0);
					$("#iModuleWrapper").css("left",-$("#iModuleSlideMenu").outerWidth(true));
					if ($("#iModuleNavigation.fixed").length == 1) {
						$("#iModuleNavigation").css("left",-$("#iModuleSlideMenu").outerWidth(true));
					}
					$(document).triggerHandler("slideMenu.show",$("#iModuleSlideMenu"));
				}
			}
		},
		resize:function() {
			if ($("#iModuleSlideMenu").is(":visible") == false) return;
			$("#iModuleSlideMenu").css("right",-$("#iModuleSlideMenu").outerWidth(true));
			
			$("#iModuleSlideMenu").height($(window).height());
			
			$("#iModuleSlideMenu").css("right",0);
			$("#iModuleWrapper").css("left",-$("#iModuleSlideMenu").outerWidth(true));
			if ($("#iModuleNavigation.fixed").length == 1) {
				$("#iModuleNavigation").css("left",-$("#iModuleSlideMenu").outerWidth(true));
			}
			$(document).triggerHandler("slideMenu.show",$("#iModuleSlideMenu"));
		}
	}
};

$(document).ready(function() {
	if ($("#iModuleHeader").length == 1 && $("#iModuleNavigation").length == 1) {
		if ($("#iModuleHeader").is(":visible") == false || $(document).scrollTop() > $("#iModuleHeader").outerHeight(true)) {
			$("#iModuleNavigation").addClass("fixed");
//			$("#iModuleWrapper").css("paddingTop",$("#iModuleNavigation").outerHeight(true));
			$("#iModuleAlertMessage").css("top",$("#iModuleNavigation").outerHeight(true));
		} else {
			$("#iModuleNavigation").removeClass("fixed");
//			$("#iModuleWrapper").css("paddingTop",0);
			$("#iModuleAlertMessage").css("top",0);
		}
	}
	
	$("input, textarea").on("focus",function() {
		if ($("#iModuleNavigation").hasClass("fixed") == false) return;
		
		if (iModule.isInScroll($(this)) == false) {
			if ($(this).offset().top < $(document).scrollTop() + $("#iModuleNavigation.fixed").outerHeight(true) + 10) {
				$(document).scrollTop($(this).offset().top - $("#iModuleNavigation.fixed").outerHeight(true) - 10);
			}
		}
	});
	
	$("#iModuleSlideMent a").bind("touchstart touchend", function(e) {
		$(this).toggleClass("touch");
	});

	$(".selectControl").each(function() {
		iModule.initSelectControl($(this));
	});
	
	$("body").on("click",function(event) {
		if ($(event.target).parents("div.selectControl").length == 0) {
			$("div.selectControl").removeClass("selectControlExtend");
		}
	});
	
	$(".wrapContent img").each(function() {
		$(this).width("auto");
		if ($(this).width() > 0 && $(this).parents(".wrapContent").innerWidth() < $(this).width()) {
			$(this).width($(this).parents(".wrapContent").innerWidth());
		}
	});
	
	
	$(".wrapContent img").on("load",function() {
		if ($(this).parents(".wrapContent").innerWidth() < $(this).width()) {
			$(this).attr("data-width",$(this).width());
			$(this).width($(this).parents(".wrapContent").innerWidth());
		}
	});
	
	$(".wrapContent iframe").each(function() {
		$(this).width("100%").height(9 * $(this).parents(".wrapContent").innerWidth() / 16);
	});
	
	$(document).on("scroll",function() {
		if ($("#iModuleHeader").length == 1 && $("#iModuleNavigation").length == 1) {
			if ($("#iModuleWindowDisabled").is(":visible") == true) return;
			
			if ($("#iModuleHeader").is(":visible") == false || $(document).scrollTop() > $("#iModuleHeader").outerHeight(true)) {
				$("#iModuleNavigation").addClass("fixed");
//				$("#iModuleWrapper").css("paddingTop",$("#iModuleNavigation").outerHeight(true));
				$("#iModuleAlertMessage").css("top",$("#iModuleNavigation").outerHeight(true));
			} else {
				$("#iModuleNavigation").removeClass("fixed");
//				$("#iModuleWrapper").css("paddingTop",0);
				$("#iModuleAlertMessage").css("top",0);
			}
		}
	});
	
	$(window).on("resize",function() {
		iModule.slideMenu.resize();
		
		if ($("#iModuleHeader").length == 1 && $("#iModuleNavigation").length == 1) {
			if ($("#iModuleWindowDisabled").is(":visible") == true) return;
			
			if ($("#iModuleHeader").is(":visible") == false || $(document).scrollTop() > $("#iModuleHeader").outerHeight(true)) {
				$("#iModuleNavigation").addClass("fixed");
//				$("#iModuleWrapper").css("paddingTop",$("#iModuleNavigation").outerHeight(true));
			} else {
				$("#iModuleNavigation").removeClass("fixed");
//				$("#iModuleWrapper").css("paddingTop",0);
			}
		}
		
		if ($("#iModuleWindowDisabled").is(":visible") == true) {
			$("#iModuleWindowDisabled").height($(window).height());
			
			if (iModule.modal.modal != null) {
				if ($("#iModuleWindowDisabled").innerWidth() > iModule.modal.modal.data("width")) {
					iModule.modal.modal.outerWidth(iModule.modal.modal.data("width"));
				} else {
					iModule.modal.modal.outerWidth($("#iModuleWindowDisabled").innerWidth() - 20);
				}
				
				if ($("#iModuleWindowDisabled").innerHeight() > iModule.modal.modal.outerHeight() + 40) {
					iModule.modal.modal.css("marginTop",($("#iModuleWindowDisabled").innerHeight() - iModule.modal.modal.outerHeight()) / 2);
				} else {
					iModule.modal.modal.css("margin","20px auto");
					$("#iModuleWindowDisabled").css("overflowY","scroll");
				}
			}
		}
		
		var wrapImages = $(".wrapContent img");
		for (var i=0, loop=wrapImages.length;i<loop;i++) {
			var image = $(wrapImages[i]).width("auto");
			if (image.width() > 0 && image.parents(".wrapContent").innerWidth() < image.width()) {
				image.width(image.parents(".wrapContent").innerWidth());
			}
		}
		
		var wrapIframes = $(".wrapContent iframe");
		for (var i=0, loop=wrapIframes.length;i<loop;i++) {
			$(wrapIframes[i]).width("100%").height(9 * iframe.parents(".wrapContent").innerWidth() / 16);
		}
	});
});












$(document).ready(function() {
	$("span.moment[data-time][data-format][data-moment]").each(function() {
		$(this).html(moment.unix($(this).attr("data-time")).locale("en").format($(this).attr("data-moment")));
	});
});