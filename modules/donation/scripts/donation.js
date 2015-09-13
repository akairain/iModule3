var Donation = {
	submit:function(form) {
		var form = $(form);
		
		iModule.inputStatus(form,"default");
		iModule.buttonStatus(form,"loading");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("donation","submit"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				iModule.inputStatus(form,"success");
				
				if (result.success == true) {
					iModule.alertMessage.show("success",result.message,5);
					form[0].reset();
					iModule.inputStatus(form,"default");
					Donation.getList(1);
				} else {
					for (field in result.errors) {
						if (field == "labels") iModule.inputStatus(form.find("input[name='labels[]']"),"error");
						else iModule.inputStatus(form.find("input[name="+field+"], textarea[name="+field+"]"),"error");
					}
					iModule.alertMessage.show("error",result.message,5);
				}
				iModule.buttonStatus(form,"reset");
			}
		});
		
		return false;
	},
	confirm:function(form) {
		var form = $(form);
		
		iModule.buttonStatus(form,"loading");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("donation","confirm"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					iModule.alertMessage.show("success",result.message,5);
					iModule.inputStatus(form,"success");
					iModule.modal.close();
					Donation.getList($("#ModuleDonationList").data("page"));
				} else {
					iModule.alertMessage.show("error",result.message,5);
				}
			}
		});
		
		return false;
	},
	show:function(idx) {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("donation","show"),
			data:{idx:idx},
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
	showInit:function() {
		var form = $("#ModuleDonationShowForm");
		var intype = $("#ModuleDonationWriteForm").find("div[data-field=intype] li");
		for (var i=0, loop=intype.length;i<loop;i++) {
			form.find("div[data-field=intype] ul").append($("<li>").attr("data-value",$(intype[i]).attr("data-value")).text($(intype[i]).text()));
		}
		
		form.find("input[data-type=number]").on("click",function() {
			$(this).select();
		});
		
		form.find("input[data-type=number]").on("blur",function() {
			$(this).val(iModule.getNumberFormat($(this).val().replace(/,/g,'')));
		});
		
		iModule.initSelectControl(form.find("div[data-field=intype]"));
		iModule.initSelectControl(form.find("div[data-field=status]"));
		
		if (form.find("input[name=status]").val() == "TRUE") {
			form.find("input, div.selectControl > button").attr("disabled",true);
		}
	},
	getList:function(page) {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("donation","getList"),
			data:{page:page},
			dataType:"json",
			success:function(result) {
				$("#ModuleDonationList").replaceWith(result.html);
				$("#ModuleDonationList").data("page",page);
			}
		});
	}
};

$(document).ready(function() {
	iModule.inputStatus($("#ModuleDonationWriteForm"),"default");
	Donation.getList(1);
	
	$(document).on("scroll",function() {
		if ($("#ModuleDonationTotal").offset().top - $(window).height() < $(document).scrollTop() && $("#ModuleDonationTotal").attr("data-ready") == "FALSE") {
			$("#ModuleDonationTotal").attr("data-ready","TRUE");
			$("#ModuleDonationTotal").animateNumber({
				number:$("#ModuleDonationTotal").attr("data-total"),
				numberStep:$.animateNumber.numberStepFactories.separator(",")
			},5000,function() {
				$("#ModuleDonationAverage").fadeIn();
			});
		}
	});
});