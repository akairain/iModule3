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
	
	$("#iModuleNavigation .language button").on("click",function() {
		if ($(this).parent().hasClass("selected") == true) {
			$(this).parent().removeClass("selected");
		} else {
			$(this).parent().addClass("selected");
		}
	});
	
	$(document).on("slideMenu.animate",function(target,position) {
		if ($(".subHeader.fixed").length == 1) {
			console.log($("#iModuleSlideMenu").outerWidth(true)+position);
			$(".subHeader.fixed").width($(window).width());
			$(".subHeader.fixed").css("left",-($("#iModuleSlideMenu").outerWidth(true)+position));
		}
	});
	
	$(document).on("slideMenu.show",function(target,position) {
		if ($(".subHeader.fixed").length == 0) return;
		$(".subHeader.fixed").width($(window).width());
		$(".subHeader.fixed").css("left",-($("#iModuleSlideMenu").outerWidth(true)));
	});
	
	$(document).on("slideMenu.hide",function(target,position) {
		if ($(".subHeader.fixed").length == 0) return;
		$(".subHeader.fixed").width("100%");
		$(".subHeader.fixed").css("left",0);
	});
	
	$(".btnQna").on("click",function() {
		var $button = $(this);
		
		var content = [
			'<div class="row">',
				'<div class="col-xs-5">',
					'<div class="label">'+$button.attr("data-sender-name")+'</div>',
					'<div class="inputBlock">',
						'<input type="text" name="sender_name" class="inputControl">',
						'<div class="helpBlock"></div>',
					'</div>',
				'</div>',
				'<div class="col-xs-7">',
					'<div class="label">'+$button.attr("data-sender-email")+'<span style="color:red;">*</span></div>',
					'<div class="inputBlock">',
						'<input type="text" name="sender_email" class="inputControl">',
						'<div class="helpBlock"></div>',
					'</div>',
				'</div>',
			'</div>',
			'<div class="label" style="margin-top:10px;">'+$button.attr("data-title")+'<span style="color:red;">*</span></div>',
			'<div class="inputBlock">',
				'<input type="text" name="subject" class="inputControl">',
				'<div class="helpBlock"></div>',
			'</div>',
			'<div class="label" style="margin-top:10px;">'+$button.attr("data-content")+'<span style="color:red;">*</span></div>',
			'<div class="inputBlock">',
				'<textarea name="content" rows="10" class="textareaControl"></textarea>',
				'<div class="helpBlock"></div>',
			'</div>'
		];
		
		iModule.modal.showForm($button.attr("data-modal-title"),content.join("\n"),{text:"<i class='fa fa-send'></i> SEND",type:"submit",fn:function($form) {
			var params = {};
			params.sender_name = $form.find("input[name=sender_name]").val();
			params.sender_email = $form.find("input[name=sender_email]").val();
			params.subject = $form.find("input[name=subject]").val().length > 0 ? "[홈페이지문의] "+$form.find("input[name=subject]").val() : '';
			params.content = $form.find("textarea[name=content]").val().length > 0 ? $form.find("textarea[name=content]").val()+"\n\n"+location.href+"\n"+navigator.userAgent : '';
			params.receiver_name = "코스모스영업팀";
			params.receiver_email = "sales@naddle.net";
			params.bcc_name = $form.find("input[name=sender_name]").val();
			params.bcc_email = $form.find("input[name=sender_email]").val();
			
			iModule.buttonStatus($form,"loading");
			
			$.ajax({
				type:"POST",
				url:ENV.getApiUrl("email","send"),
				data:params,
				dataType:"json",
				success:function(data) {
					if (data.success == true) {
						iModule.alertMessage.show("success",data.message,5);
						iModule.modal.close();
					} else {
						for (field in data.errors) {
							iModule.inputStatus($form.find("input[name="+field+"], textarea[name="+field+"]"),"error",data.errors[field]);
						}
						iModule.buttonStatus($form,"reset");
					}
				}
			});
		}},[{text:"CANCEL",click:function() { iModule.modal.close(); }}]);
	});
	
	$(document).on("modal.show",function() {
		$(".btnQna").hide();
	});
	
	$(document).on("modal.close",function() {
		$(".btnQna").show();
	});
});