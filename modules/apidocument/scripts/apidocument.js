var Apidocument = {
	toggle:function(object) {
		$(object).parents("tr.toggle").toggleClass("opened");
	},
	post:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Board.post.check($(this));
					});
				}
			}
			
			$(document).triggerHandler("Apidocument.post.init",[form]);
		},
		submit:function(form) {
			var form = $(form);
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			var wysiwygControl = form.find("textarea[data-wysiwyg=true]");
			for (var i=0, loop=wysiwygControl.length;i<loop;i++) {
				$(wysiwygControl[i]).redactor("code.showVisual");
				$(wysiwygControl[i]).redactor("code.sync");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("apidocument","postWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						location.href = result.redirect;
					} else {
						var errorMsg = "";
						for (field in result.errors) {
							iModule.inputStatus(form.find("input[name="+field+"], textarea[name="+field+"]"),"error",result.errors[field]);
						}
						
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
						
						iModule.buttonStatus(form,"reset");
					}
				},
				error:function() {
					iModule.alertMessage.show("Server Connect Error!");
				}
			});
			
			return false;
		}
	}
};

$(document).ready(function() {
	$(document).on("scroll",function() {
		var sectionHeaders = $(".sectionHeader");
		for (var i=0, loop=sectionHeaders.length;i<loop;i++) {
			var sectionHeader = $(sectionHeaders[i]);
			if (sectionHeader.parents("div.ModuleApidocumentSection").height() > 200 && sectionHeader.parents("div.ModuleApidocumentSection").offset().top < $(document).scrollTop() + $("#iModuleNavigation.fixed").outerHeight(true)) {
				sectionHeader.css("position","fixed");
				sectionHeader.css("zIndex",1);
				sectionHeader.css("width",sectionHeader.parents("div.ModuleApidocumentSection").width());
				sectionHeader.css("top",0);
				sectionHeader.css("paddingTop",$("#iModuleNavigation.fixed").outerHeight(true));
				sectionHeader.css("left",sectionHeader.parents("div.ModuleApidocumentSection").offset().left);
				sectionHeader.parents("div.ModuleApidocumentSection").css("paddingTop",sectionHeader.height());
				
				if (sectionHeader.parents("div.ModuleApidocumentSection").offset().top + sectionHeader.parents("div.ModuleApidocumentSection").height() > $(document).scrollTop() + $("#iModuleNavigation.fixed").outerHeight(true)) {
					sectionHeader.css("visibility","visible");
				} else {
					sectionHeader.css("visibility","hidden");
				}
			} else {
				sectionHeader.parents("div.ModuleApidocumentSection").css("paddingTop",0);
				sectionHeader.css("position","static");
				sectionHeader.css("zIndex","auto");
				sectionHeader.css("width","auto");
				sectionHeader.css("paddingTop",0);
				sectionHeader.css("top",0);
				sectionHeader.css("left",0);
			}
		}
	});
	
	$(window).on("resize",function() {
		$(document).trigger("scroll");
	});
});