Shop.seller.promotion = {
	add:function(date) {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("shop","sellerPromotionAddModal"),
			data:{date:date},
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					iModule.modal.showHtml(result.modalHtml);
				} else {
					iModule.alertMessage.show("error",result.message,5);
				}
			}
		});
	},
	post:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			form.data("uploading",[]);
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Board.post.check($(this));
					});
				}
			}
			
			iModule.initSelectControl(form.find(".selectControl[data-field=item]"));
			iModule.initSelectControl(form.find(".selectControl[data-field=min]"));
			iModule.initSelectControl(form.find(".selectControl[data-field=max]"));
			
			$(document).triggerHandler("Shop.seller.promotion.post.init",[form]);
		},
		submit:function(form) {
			var form = $(form);
			
//			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("shop","sellerPromotionPost"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
//						location.href = result.redirect;
					} else {
						var errorMsg = "";
						for (field in result.errors) {
							if (field == "labels") iModule.inputStatus(form.find("input[name='labels[]']"),"error",result.errors[field]);
							else iModule.inputStatus(form.find("input[name="+field+"], textarea[name="+field+"]"),"error",result.errors[field]);
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