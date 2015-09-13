var Minitalk = {
	hosting:{
		form:null,
		init:function() {
			Minitalk.hosting.form = $("form[name=MinitalkServiceForm]");
			Minitalk.hosting.form.find(".btnGroup > .toggle").on("click",function() {
				$(this).parents(".btnGroup").find(".toggle").removeClass("selected");
				$(this).parents(".btnGroup").find(".toggle i").removeClass("fa-check-square-o").addClass("fa-square");
				$(this).find("i").removeClass("fa-square").addClass("fa-check-square-o");
				$(this).addClass("selected");
				
				$(this).parents("form").find("input[name="+$(this).parents(".btnGroup").attr("data-field")+"]").val($(this).attr("data-value"));
				$(this).parents("form").find("input[name="+$(this).parents(".btnGroup").attr("data-field")+"]").triggerHandler("change");
			});
			
			Minitalk.hosting.form.find("input[name=type]").on("change",function() {
				Minitalk.hosting.getSelectType($(this).val());
			});
			
			Minitalk.hosting.form.find("input[name=service]").on("change",function() {
				if ($(this).val()) {
					Minitalk.hosting.getSelectUser();
					Minitalk.hosting.getSelectTime();
				} else {
					$(this).parents("form").find("div.selectControl[data-field=maxuser] button").attr("disabled",true);
					$(this).parents("form").find("div.selectControl[data-field=time] button").attr("disabled",true);
				}
			});
			
			Minitalk.hosting.form.find("input[name=maxuser]").on("change",function() {
				Minitalk.hosting.getPrice();
			});
			
			Minitalk.hosting.form.find("input[name=time]").on("change",function() {
				Minitalk.hosting.getPrice();
				Minitalk.hosting.getExpireDate();
			});
			
			Minitalk.hosting.form.find("input[name=idx]").on("change",function() {
				Minitalk.hosting.setSelectForm($(this).val());
			});
		},
		setSelectForm:function(idx) {
			if (idx.length == 0) return;
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getService"),
				data:{idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Minitalk.hosting.form.find("input[name=idx]").val(result.data.idx);
						Minitalk.hosting.form.find("input[name=service]").val(result.data.service);
						Minitalk.hosting.form.find("input[name=service]").triggerHandler("change");
						Minitalk.hosting.form.find("div.selectControl[data-field=service] button").attr("disabled",false);
						Minitalk.hosting.form.find("div.selectControl[data-field=idx] button").html(result.data.title+' <span class="arrow"></span>').attr("disabled",false);
						Minitalk.hosting.form.find("input[name=maxuser]").val(result.data.maxuser);
						Minitalk.hosting.form.find("input[name=maxuser]").triggerHandler("change");
						
						Minitalk.hosting.getPrice();
					} else {
						iModule.alertMessage.show("error",result.message);
					}
				}
			});
		},
		getSelectType:function(type) {
			Minitalk.hosting.form.find(".toggle").removeClass("selected");
			Minitalk.hosting.form.find(".toggle i").removeClass("fa-check-square-o").addClass("fa-square");
			Minitalk.hosting.form.find(".toggle[data-value="+type+"] i").removeClass("fa-square").addClass("fa-check-square-o");
			Minitalk.hosting.form.find(".toggle[data-value="+type+"]").addClass("selected");
			
			Minitalk.hosting.form.find("input").val("");
			Minitalk.hosting.form.find("input[name=type]").val(type);
			
			Minitalk.hosting.form.find("tr[data-type]").hide();
			Minitalk.hosting.form.find("tr[data-type="+type+"]").show();
			
			Minitalk.hosting.form.find("div.selectControl[data-field=service] button").html('서비스종류 선택 <span class="arrow"></span>').attr("disabled",true);
			Minitalk.hosting.form.find("div.selectControl[data-field=maxuser] button").html('접속자수 선택 <span class="arrow"></span>').attr("disabled",true);
			Minitalk.hosting.form.find("div.selectControl[data-field=time] button").html('신청기간 선택 <span class="arrow"></span>').attr("disabled",true);
			
			Minitalk.hosting.getPrice();
			
			if (type == "NEW") {
				Minitalk.hosting.form.find("input[name=service]").triggerHandler("change");
				Minitalk.hosting.form.find("div.selectControl[data-field=service] button").attr("disabled",false);
					
				Minitalk.hosting.form.find("input[name=idx]").val("");
				Minitalk.hosting.form.find("div.selectControl[data-field=idx] ul").empty();
			} else {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("minitalk","getMyHosting"),
					data:Minitalk.hosting.form.serialize(),
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							var idx = Minitalk.hosting.form.find("input[name=idx]").val();
							var isFind = false;
							
							Minitalk.hosting.form.find("div.selectControl[data-field=idx] ul").empty();
							for (var i=0, loop=result.lists.length;i<loop;i++) {
								Minitalk.hosting.form.find("div.selectControl[data-field=idx] ul").append($("<li>").attr("data-value",result.lists[i].idx).html(result.lists[i].title));
								isFind = result.lists[i].idx == isFind ? true : false;
							}
							
							Minitalk.hosting.form.find("input[name=idx]").val(isFind == true ? idx : "");
							Minitalk.hosting.form.find("div.selectControl[data-field=idx] button").html('서비스 선택 <span class="arrow"></span>');
							Minitalk.hosting.form.find("input[name=idx]").triggerHandler("change");
							Minitalk.hosting.form.find("div.selectControl[data-field=idx] button").attr("disabled",false);
							iModule.initSelectControl(Minitalk.hosting.form.find("div.selectControl[data-field=idx]"));
						} else {
							iModule.alertMessage.show("error",result.message,5);
							Minitalk.hosting.getSelectType("NEW");
						}
					}
				});
			}
		},
		getSelectUser:function() {
			Minitalk.hosting.form.find("input[name=maxuser]").val("");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getSelectUser"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Minitalk.hosting.form.find("div.selectControl[data-field=maxuser] ul").empty();
						for (var i=0, loop=result.lists.length;i<loop;i++) {
							Minitalk.hosting.form.find("div.selectControl[data-field=maxuser] ul").append($("<li>").attr("data-value",result.lists[i].usernum).html(result.lists[i].html));
							if (result.lists[i].selected == true) {
								Minitalk.hosting.form.find("input[name=maxuser]").val(result.lists[i].usernum);
							}
						}
						
						Minitalk.hosting.form.find("input[name=maxuser]").triggerHandler("change");
						Minitalk.hosting.form.find("div.selectControl[data-field=maxuser] button").html('접속자수 선택 <span class="arrow"></span>');
						Minitalk.hosting.form.find("div.selectControl[data-field=maxuser] button").attr("disabled",false);
						iModule.initSelectControl(Minitalk.hosting.form.find("div.selectControl[data-field=maxuser]"));
					}
				}
			});
		},
		getSelectTime:function() {
			Minitalk.hosting.form.find("input[name=time]").val("");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getSelectTime"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Minitalk.hosting.form.find("div.selectControl[data-field=time] ul").empty();
						for (var i=0, loop=result.lists.length;i<loop;i++) {
							Minitalk.hosting.form.find("div.selectControl[data-field=time] ul").append($("<li>").attr("data-value",result.lists[i].time).html(result.lists[i].html));
							if (result.lists[i].selected == true) {
								Minitalk.hosting.form.find("input[name=time]").val(result.lists[i].time);
							}
						}
						
						Minitalk.hosting.form.find("input[name=time]").triggerHandler("change");
						Minitalk.hosting.form.find("div.selectControl[data-field=time] button").html('신청기간 선택 <span class="arrow"></span>');
						Minitalk.hosting.form.find("div.selectControl[data-field=time] button").attr("disabled",false);
						iModule.initSelectControl(Minitalk.hosting.form.find("div.selectControl[data-field=time]"));
					}
				}
			});
		},
		getPrice:function() {
			if (Minitalk.hosting.form.find("input[name=maxuser]").val().length == 0 || Minitalk.hosting.form.find("input[name=time]").val().length == 0) {
				Minitalk.hosting.form.find("span[data-name]").html(0);
				return;
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getPrice"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success = true) {
						for (name in result.price) {
							Minitalk.hosting.form.find("span[data-name="+name+"]").html(iModule.getNumberFormat(result.price[name]));
						}
					} else {
						iModule.alertMessage.show("error","선택사항에 오류가 있습니다.",5);
					}
				}
			});
		},
		getExpireDate:function() {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getExpireDate"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					Minitalk.hosting.form.find("div[data-name=expire_date]").html(result.expire_date);
				}
			});
		},
		submit:function() {
			iModule.buttonStatus(Minitalk.hosting.form,"loading");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","hostingSubmit"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.alertMessage.show("success","성공적으로 신청하였습니다.",5);
						Minitalk.hosting.getSelectType("NEW");
						Minitalk.hosting.getMyHosting();
					} else {
						iModule.alertMessage.show("error",result.message,5);
					}
					iModule.buttonStatus(Minitalk.hosting.form,"reset");
				}
			});
			
			return false;
		},
		getMyHosting:function() {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","getMyHosting"),
				data:Minitalk.hosting.form.serialize(),
				dataType:"json",
				success:function(result) {
					$("#ModuleMinitalkMyHosting").replaceWith(result.html);
				}
			});
		},
		extend:function(idx) {
			Minitalk.hosting.getSelectType("EXTEND");
			Minitalk.hosting.setSelectForm(idx);
			
			if (iModule.isInScroll(Minitalk.hosting.form) == false) {
				$("html, body").animate({scrollTop:Minitalk.hosting.form.offset().top - 100},"fast");
			}
		},
		disconnect:function(idx,confirm) {
			var confirm = confirm === true ? "TRUE" : "FALSE";
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("minitalk","disconnect"),
				data:{idx:idx,confirm:confirm},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.modalHtml) {
							iModule.modal.showHtml(result.modalHtml);
						} else {
							iModule.alertMessage.show("success",result.message,5);
							Minitalk.hosting.getMyHosting();
							iModule.modal.close();
						}
					} else {
						iModule.alertMessage.show("error",result.message,5);
					}
				}
			});
			
			return false;
		}
	},
	getServerList:function() {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("minitalk","getServerList"),
			dataType:"json",
			success:function(result) {
				$("#ModuleMinitalkServerList").replaceWith(result.html);
			}
		});
	}
};