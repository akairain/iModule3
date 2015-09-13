var Member = {
	signup:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				$(inputControl[i]).on("blur",function() {
					Member.signup.check($(this));
				});
			}
		},
		check:function(input) {
			if (input.attr("name") == "password" || input.attr("name") == "password_confirm") {
				if (input.val().length < 4) {
					iModule.inputStatus(input,"error");
					return;
				}
				
				if (input.attr("name") == "password") {
					iModule.inputStatus(input,"success");
					if (input.parents("form").find("input[name=password_confirm]").val().length > 0) Member.signup.check(input.parents("form").find("input[name=password_confirm]"));
				}
				
				if (input.attr("name") == "password_confirm") {
					if (input.val() == input.parents("form").find("input[name=password]").val()) {
						iModule.inputStatus(input,"success");
					} else {
						iModule.inputStatus(input,"error");
					}
				}
			} else if (input.attr("name") == "email" || input.attr("name") == "nickname") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					$.ajax({
						type:"POST",
						url:ENV.getProcessUrl("member","check"),
						data:{name:input.attr("name"),value:input.val()},
						dataType:"json",
						success:function(result) {
							if (result.success == true) {
								iModule.inputStatus(input,"success",result.message);
							} else {
								iModule.inputStatus(input,"error",result.message);
							}
						},
						error:function() {
							iModule.alertMessage.show("Server Connect Error!");
						}
					});
				}
			} else if (input.attr("required") == "required") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					iModule.inputStatus(input,"success");
				}
			}
		},
		submit:function(form) {
			var form = $(form);
			var step = form.find("input[name=step]").val();
			var next = form.find("input[name='next']").val();
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			if (step == "agreement") {
				location.href = next;
			}
			
			if (step == "cert") {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","cert"),
					data:form.serialize(),
					dataType:"json",
					success:function(result) {
						iModule.inputStatus(form,"success");
						
						if (result.success == true) {
							location.href = next;
						} else {
							for (error in result.errors) {
								iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
							}
							
							iModule.buttonStatus(form,"reset");
						}
					}
				});
			} else if (step == "insert") {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","signup"),
					data:form.serialize(),
					dataType:"json",
					success:function(result) {
						iModule.inputStatus(form,"success");
						
						if (result.success == true) {
							location.href = next;
						} else {
							for (error in result.errors) {
								iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
							}
							
							iModule.buttonStatus(form,"reset");
						}
					}
				});
			} else if (step == "verify") {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","verifyEmail"),
					data:form.serialize(),
					dataType:"json",
					success:function(result) {
						iModule.inputStatus(form,"success");
						
						if (result.success == true) {
							location.href = next;
						} else {
							for (error in result.errors) {
								iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
							}
							
							iModule.buttonStatus(form,"reset");
						}
					}
				});
			}
			
			return false;
		},
		resendVerifyEmail:function(button) {
			var form = $("form[name=ModuleMemberSignUpForm]");
			
			iModule.buttonStatus($(button),"loading");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("member","sendVerifyEmail"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.alertMessage.show("success",result.message,5);
					} else {
						for (error in result.errors) {
							iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
						}
						
						if (result.message) iModule.alertMessage.show("error",result.message,5);
					}
					
					iModule.buttonStatus($(button),"reset");
				}
			});
		}
	},
	modify:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				$(inputControl[i]).on("blur",function() {
					Member.modify.check($(this));
				});
			}
		},
		check:function(input) {
			if (input.attr("name") == "nickname") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					$.ajax({
						type:"POST",
						url:ENV.getProcessUrl("member","check"),
						data:{name:input.attr("name"),value:input.val()},
						dataType:"json",
						success:function(result) {
							if (result.success == true) {
								iModule.inputStatus(input,"success",result.message);
							} else {
								iModule.inputStatus(input,"error",result.message);
							}
						},
						error:function() {
							iModule.alertMessage.show("Server Connect Error!");
						}
					});
				}
			} else if (input.attr("required") == "required") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					iModule.inputStatus(input,"success");
				}
			}
		},
		photoEdit:function() {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("member","photoEdit"),
				data:{templet:$("form[name=ModuleMemberModifyForm] input[name=templet]").val()},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.modal.showHtml(result.modalHtml);
						
						$(function() {
							$(".photo-editor").cropit({
								exportZoom:1,
								imageBackground:true,
								imageBackgroundBorderWidth:30,
								imageState:{
									src:result.photo
								}
							});

							$(".export").click(function() {
								var imageData = $('.image-editor').cropit('export');
								window.open(imageData);
							});
						});
					} else {
						iModule.alertMessage.show("error",result.message,5);
					}
				}
			});
		},
		photoSubmit:function(form) {
			iModule.buttonStatus($(form),"loading");
			
			var photoData = $(".photo-editor").cropit("export");
			$("#ModuleMemberPhotoPreview").attr("src",photoData);
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("member","photoUpload"),
				data:{photo:photoData},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.alertMessage.show("success",result.message,5);
						iModule.modal.close();
					} else {
						iModule.buttonStatus(form,"reset");
						iModule.alertMessage.show("error",result.message,5);
					}
				}
			});
			
			return false;
		},
		modifyEmail:function(form) {
			if (form && $(form).is("form") == true) {
				var form = $(form);
				
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","modifyEmail"),
					data:form.serialize(),
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							iModule.alertMessage.show("success",result.message,5);
							$("form[name=ModuleMemberModifyForm] input[name=email]").val(form.find("input[name=email]").val());
							$("*[data-name=email]").val(form.find("input[name=email]").val());
							iModule.modal.close();
						} else {
							for (error in result.errors) {
								iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
							}
						}
					}
				});
				
				return false;
			} else {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","modifyEmail"),
					data:{templet:$("form[name=ModuleMemberModifyForm] input[name=templet]").val()},
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							iModule.modal.showHtml(result.modalHtml);
						} else {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
				});
			}
		},
		sendVerifyEmail:function(button) {
			var form = $("form[name=ModuleMemberModifyEmailForm]");
			
			iModule.buttonStatus($(button),"loading");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("member","sendVerifyEmail"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.inputStatus(form.find("input[name=email]"),"success","");
						iModule.alertMessage.show("success",result.message,5);
					} else {
						for (error in result.errors) {
							iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
						}
						
						if (result.message) iModule.alertMessage.show("error",result.message,5);
					}
					
					iModule.buttonStatus($(button),"reset");
				}
			});
		},
		submit:function(form) {
			var form = $(form);
			var step = form.find("input[name=step]").val();
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			if (step == "verifying") {
				return true;
			} else {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("member","modify"),
					data:form.serialize(),
					dataType:"json",
					success:function(result) {
						iModule.inputStatus(form,"success");
						
						if (result.success == true) {
							if (step == "verify") {
								form.find("input[name=step]").val("verifying");
								form.find("input[name=password]").val(result.password);
								form.submit();
							} else {
								iModule.alertMessage.show("success",result.message,5);
								iModule.inputStatus(form,"default");
								iModule.buttonStatus(form,"reset");
							}
						} else {
							for (error in result.errors) {
								iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
							}
							
							iModule.buttonStatus(form,"reset");
						}
					}
				});
				
				return false;
			}
		}
	},
	password:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				$(inputControl[i]).on("blur",function() {
					Member.password.check($(this));
				});
			}
		},
		check:function(input) {
			if (input.attr("name") == "password" || input.attr("name") == "password_confirm") {
				if (input.val().length < 4) {
					iModule.inputStatus(input,"error");
					return;
				}
				
				if (input.attr("name") == "password") {
					iModule.inputStatus(input,"success");
					if (input.parents("form").find("input[name=password_confirm]").val().length > 0) Member.signup.check(input.parents("form").find("input[name=password_confirm]"));
				}
				
				if (input.attr("name") == "password_confirm") {
					if (input.val() == input.parents("form").find("input[name=password]").val()) {
						iModule.inputStatus(input,"success");
					} else {
						iModule.inputStatus(input,"error");
					}
				}
			} else if (input.attr("name") == "old_password") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					$.ajax({
						type:"POST",
						url:ENV.getProcessUrl("member","check"),
						data:{name:input.attr("name"),value:input.val()},
						dataType:"json",
						success:function(result) {
							if (result.success == true) {
								iModule.inputStatus(input,"success",result.message);
							} else {
								iModule.inputStatus(input,"error",result.message);
							}
						},
						error:function() {
							iModule.alertMessage.show("Server Connect Error!");
						}
					});
				}
			} else if (input.attr("required") == "required") {
				if (input.val().length == 0) {
					iModule.inputStatus(input,"error");
				} else {
					iModule.inputStatus(input,"success");
				}
			}
		},
		submit:function(form,is_redirect) {
			var form = $(form);
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("member","password"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					iModule.inputStatus(form,"success");
					
					if (result.success == true) {
						if (is_redirect == true) {
							location.href = location.href;
						} else {
							iModule.alertMessage.show("success",result.message,5);
							form.find("input").val("");
							iModule.inputStatus(form,"default");
							iModule.buttonStatus(form,"reset");
						}
					} else {
						for (error in result.errors) {
							iModule.inputStatus(form.find("input[name="+error+"]"),"error",result.errors[error]);
						}
						
						iModule.buttonStatus(form,"reset");
					}
				}
			});
			
			return false;
		}
	},
	forceLogin:function(code,redirectUrl) {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("member","forceLogin"),
			data:{code:code},
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					location.href = redirectUrl ? redirectUrl : location.href.split("#").shift();
				} else {
					iModule.alertMessage.show("error",result.message,5);
				}
			},
			error:function() {
				iModule.alertMessage.show("Server Connect Error!");
			}
		});
	},
	login:function(form) {
		var form = $(form);
		iModule.buttonStatus(form,"loading");
		iModule.inputStatus(form,"default");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("member","login"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					location.href = location.href.split("#").shift();
				} else {
					if (result.redirect) {
						location.href = result.redirect;
					} else {
						for (var field in result.errors) {
							iModule.inputStatus(form.find("input[name="+field+"]"),"error",result.errors[field]);
						}
						
						if (result.message) iModule.alertMessage.show("error",result.message,5);
						
						iModule.buttonStatus(form,"reset");
					}
				}
			},
			error:function() {
				iModule.alertMessage.show("Server Connect Error!");
			}
		});
		
		return false;
	},
	logout:function(button) {
		iModule.buttonStatus($(button),"loading");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("member","logout"),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					location.href = location.href.split("#").shift();
				} else {
					if (result.message) iModule.alertMessage.show("error",result.message,5);
					
					iModule.buttonStatus($(button),"reset");
				}
			},
			error:function() {
				iModule.alertMessage.show("Server Connect Error!");
			}
		});
	}
};