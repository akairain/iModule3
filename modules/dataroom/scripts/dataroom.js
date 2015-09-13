var Dataroom = {
	getListUrl:function(form) {
		var form = $(form);
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("dataroom","listUrl"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					location.href = result.url;
				}
			},
			error:function() {
				iModule.alertMessage.show("Server Connect Error!");
			}
		});
		
		return false;
	},
	post:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Dataroom.post.check($(this));
					});
				}
			}
			
			form.find("input[name=logo]").on("change",function(e) {
				if (e.target.files.length > 0) {
					$(this).parents(".inputBlock, .inputInline").find(".helpBlock").attr("data-default",null).html('<i class="fa fa-file-image-o"></i> '+e.target.files[0].name);
				}
			});
			
			$(document).triggerHandler("Dataroom.post.init",[form]);
		},
		check:function(form) {
			if (form.attr("required") == "required") {
				var inputBlock = form.parents(".inputBlock, .inputInline");
				var helpBlock = inputBlock.find(".helpBlock")
				
				if (form.val().length == 0) {
					inputBlock.addClass("hasError");
					if (helpBlock.attr("data-error") !== undefined) {
						helpBlock.html(helpBlock.attr("data-error"));
					}
				} else {
					inputBlock.removeClass("hasError");
					if (helpBlock.attr("data-default") !== undefined) {
						helpBlock.html(helpBlock.attr("data-default"));
					} else {
						helpBlock.empty();
					}
				}
			}
		},
		selectLogo:function(button) {
			var form = $(button).parents("form").find("input[name=logo]").trigger("click");
		},
		submit:function(form) {
			var formData = new FormData(form);
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
				url:ENV.getProcessUrl("dataroom","postWrite"),
				data:formData,
				dataType:"json",
				processData:false,
				contentType:false,
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
		},
		delete:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","postDelete"),
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
		}
	},
	version:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Dataroom.post.check($(this));
					});
				}
			}
			
			form.find("input[name=file]").on("change",function(e) {
				if (e.target.files.length > 0) {
					$(this).parents(".inputBlock, .inputInline").find(".helpBlock").attr("data-default",null).html('<i class="fa fa-file-archive-o"></i> '+e.target.files[0].name);
				}
			});
			
			$(document).triggerHandler("Dataroom.post.init",[form]);
		},
		check:function(form) {
			if (form.attr("required") == "required") {
				var inputBlock = form.parents(".inputBlock, .inputInline");
				var helpBlock = inputBlock.find(".helpBlock")
				
				if (form.val().length == 0) {
					inputBlock.addClass("hasError");
					if (helpBlock.attr("data-error") !== undefined) {
						helpBlock.html(helpBlock.attr("data-error"));
					}
				} else {
					inputBlock.removeClass("hasError");
					if (helpBlock.attr("data-default") !== undefined) {
						helpBlock.html(helpBlock.attr("data-default"));
					} else {
						helpBlock.empty();
					}
				}
			}
		},
		selectFile:function(button) {
			var form = $(button).parents("form").find("input[name=file]").trigger("click");
		},
		submit:function(form) {
			var formData = new FormData(form);
			var form = $(form);
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","versionWrite"),
				data:formData,
				processData:false,
				contentType:false,
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
		},
		modify:function(idx,form) {
			var form = form ? $(form) : null;
			
			if (form !== null) {
				iModule.buttonStatus(form,"loading");
				iModule.inputStatus(form,"default");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","postModify"),
				data:form === null ? {idx:idx} : $(form).serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.permission == true) {
							if (form === null) {
								location.href = ENV.getUrl(null,null,"write",idx);
							} else {
								form.attr("method","post");
								form.attr("action",ENV.getUrl(null,null,"write",idx));
								form.attr("onsubmit","");
								form.submit();
							}
						} else {
							iModule.modal.showHtml(result.modalHtml);
						}
					} else {
						if (result.message) iModule.alertMessage.show("error",result.message,5);
						
						if (form !== null) {
							var errorMsg = "";
							for (field in result.errors) {
								iModule.inputStatus(form.find("input[name="+field+"], textarea[name="+field+"]"),"error",result.errors[field]);
							}
							
							if (result.message) {
								iModule.alertMessage.show("error",result.message,5);
							}
							
							iModule.buttonStatus(form,"reset");
						}
					}
				}
			});
			
			return false;
		},
		delete:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","postDelete"),
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
		}
	},
	qna:{
		init:function(form,type) {
			var form = $("form[name="+form+"]");
			var type = type ? type : "write";
			iModule.inputStatus(form,"default");
			
			form.find("span[data-title-"+type+"]").html(form.find("span[data-title-"+type+"]").attr("data-title-"+type));
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Dataroom.post.check($(this));
					});
				}
			}
			
			form.find("input[name=file]").on("change",function(e) {
				if (e.target.files.length > 0) {
					$(this).parents(".inputBlock, .inputInline").find(".helpBlock").html('<i class="fa fa-file-archive-o"></i> '+e.target.files[0].name);
				}
			});
			
			$(document).triggerHandler("Dataroom.post.init",[form]);
		},
		check:function(form) {
			if (form.attr("required") == "required") {
				var inputBlock = form.parents(".inputBlock, .inputInline");
				var helpBlock = inputBlock.find(".helpBlock")
				
				if (form.val().length == 0) {
					inputBlock.addClass("hasError");
					if (helpBlock.attr("data-error") !== undefined) {
						helpBlock.html(helpBlock.attr("data-error"));
					}
				} else {
					inputBlock.removeClass("hasError");
					if (helpBlock.attr("data-default") !== undefined) {
						helpBlock.html(helpBlock.attr("data-default"));
					} else {
						helpBlock.empty();
					}
				}
			}
		},
		view:function(idx) {
			if ($("#ModuleDataroomQnaView-"+idx).is(":visible") == true) {
				$("#ModuleDataroomQnaView-"+idx).empty();
				$("#ModuleDataroomQnaView-"+idx).hide();
			} else {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("dataroom","getQnaView"),
					data:{idx:idx},
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							$("#ModuleDataroomQnaView-"+result.idx).html(result.qnaHtml);
							$("#ModuleDataroomQnaView-"+result.idx).show();
							
							if (iModule.isInScroll($("#ModuleDataroomQnaView-"+result.idx)) == false) {
								$("html,body").animate({scrollTop:$("#ModuleDataroomQnaView-"+result.idx).offset().top - 100},"fast");
							}
						} else {
							if (result.message) {
								iModule.alertMessage.show("error",result.message,5);
							}
						}
					}
				});
			}
		},
		write:function(parent) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","getQnaWrite"),
				data:{parent:parent},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						$("#ModuleDataroomQnaList-"+result.parent).html(result.qnaHtml);
						$("#ModuleDataroomQnaPagination-"+result.parent).hide();
						if (iModule.isInScroll($("#ModuleDataroomQnaList-"+result.parent)) == false) {
							$("html, body").animate({scrollTop:$("#ModuleDataroomQnaList-"+result.parent).offset().top - 100},"fast");
						}
					} else {
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
				}
			});
		},
		submit:function(form) {
			var formData = new FormData(form);
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
				url:ENV.getProcessUrl("dataroom","qnaWrite"),
				data:formData,
				dataType:"json",
				processData:false,
				contentType:false,
				success:function(result) {
					if (result.success == true) {
						Dataroom.qna.loadIdx(result.idx);
						
						
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
		},
		answer:function(form) {
			var formData = new FormData(form);
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
				url:ENV.getProcessUrl("dataroom","qnaAnswer"),
				data:formData,
				dataType:"json",
				processData:false,
				contentType:false,
				success:function(result) {
					if (result.success == true) {
						Dataroom.qna.loadIdx(result.parent);
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
		},
		loadPage:function(page,parent) {
			if (typeof parent == "object") parent = $(parent).parents("div[id|=ModuleDataroomQnaPagination]").attr("data-parent");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","getQna"),
				data:{get:"page",parent:parent,page:page},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						$("#ModuleDataroomQnaList-"+result.parent).html(result.qnaHtml);
						$("#ModuleDataroomQnaPagination-"+result.parent).replaceWith(result.pagination);
						$("#ModuleDataroomQnaPagination-"+result.parent).show();
						$(".liveUpdateDataroomPostQna"+result.parent).text(result.qnaCount);
						
						if (iModule.isInScroll($("#ModuleDataroomQnaList-"+result.parent)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleDataroomQnaList-"+result.parent).offset().top - 100},"fast");
						}
					}
				}
			});
			return false;
		},
		loadIdx:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","getQna"),
				data:{get:"idx",idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						$("#ModuleDataroomQnaList-"+result.parent).html(result.qnaHtml);
						$("#ModuleDataroomQnaPagination-"+result.parent).replaceWith(result.pagination);
						$("#ModuleDataroomQnaPagination-"+result.parent).show();
						$(".liveUpdateDataroomPostQna"+result.parent).text(result.qnaCount);
						
						Dataroom.qna.view(idx);
					}
				}
			});
		}
	},
	ment:{
		selectedButton:null,
		init:function(form,type) {
			var form = $("form[name="+form+"]");
			var type = type ? type : "write";
			iModule.inputStatus(form,"default");
			
			form.find("span[data-title-"+type+"]").html(form.find("span[data-title-"+type+"]").attr("data-title-"+type));
			
			$(document).triggerHandler("Dataroom.ment.init",[form]);
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
				url:ENV.getProcessUrl("dataroom","mentWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Dataroom.ment.loadIdx(result.idx);
						Dataroom.ment.reset(form);
					} else {
						var errorMsg = "";
						for (field in result.errors) {
							iModule.inputStatus(form.find("input[name="+field+"], textarea[name="+field+"]"),"error",result.errors[field]);
						}
						
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
					
					iModule.buttonStatus(form,"reset");
				}
			});
			
			return false;
		},
		reset:function(form) {
			var idx = form.find("input[name=idx]").val();
			var parent = form.find("input[name=parent]").val();
			var source = form.find("input[name=source]").val();
			
			if (idx) $("#ModuleDataroomMentItem-"+idx).find(".mentContext").show();
			
			form.find("input, textarea").val("");
			form.find("input[name=parent]").val(parent);
			
			if (idx || source) {
				form = form.clone();
				$("form[name=ModuleDataroomMentForm-"+parent+"]").remove();
				$("#ModuleDataroomMentWrite-"+parent).append(form);
			}
			
			var attachments = {};
			var wysiwygs = form.find("textarea[data-wysiwyg=true]");
			for (var i=0, loop=wysiwygs.length;i<loop;i++) {
				var textarea = $(wysiwygs[i]).clone();
				textarea.val("");
				var wysiwygObject = $(wysiwygs[i]).parents("div.redactor-box");
				$(wysiwygs[i]).remove();
				textarea.insertBefore(wysiwygObject);
				wysiwygObject.remove();
			}
			
			for (var i=0, loop=wysiwygs.length;i<loop;i++) {
				$("#"+$(wysiwygs[i]).attr("id")).redactor({minHeight:100,toolbarFixedTopOffset:50});
				Attachment.reset($(wysiwygs[i]).attr("id")+"-attachment");
			}
			
			if (Dataroom.ment.selectedButton != null) {
				$(Dataroom.ment.selectedButton).removeClass("selected");
				if ($(Dataroom.ment.selectedButton).attr("data-cancel") !== undefined) {
					$(Dataroom.ment.selectedButton).html($(Dataroom.ment.selectedButton).data("default"));
				}
			}
			
			form.find("input[name=is_secret]").prop("checked",false);
			form.find("input[name=is_reply]").prop("checked",true);
			form.find("input[name=is_push]").prop("checked",true);
			form.find("input[name=is_hidename]").prop("checked",false);
			
			Dataroom.ment.init(form.attr("name"));
		},
		reply:function(idx,button) {
			if ($(button).hasClass("selected") == true) {
				if (Dataroom.ment.selectedButton !== null) {
					$(Dataroom.ment.selectedButton).removeClass("selected");
					$(Dataroom.ment.selectedButton).html($(Dataroom.ment.selectedButton).data("default"));
				}
				Dataroom.ment.selectedButton = null;
				
				var parent = $(button).parents(".mentItem").attr("data-parent");
				var form = $("form[name=ModuleDataroomMentForm-"+parent+"]").clone();
				var attachments = {};
				var wysiwygs = form.find("textarea[data-wysiwyg=true]");
				for (var i=0, loop=wysiwygs.length;i<loop;i++) {
					var textarea = $(wysiwygs[i]).clone();
					textarea.val($("#"+textarea.attr("id")).redactor("code.get"));
					var wysiwygObject = $(wysiwygs[i]).parents("div.redactor-box");
					$(wysiwygs[i]).remove();
					textarea.insertBefore(wysiwygObject);
					wysiwygObject.remove();
					
					attachments[textarea.attr("id")] = Attachment.getFiles(textarea.attr("id")+"-attachment");
				}
				$("form[name=ModuleDataroomMentForm-"+parent+"]").remove();
				$("#ModuleDataroomMentWrite-"+parent).append(form);
				
				for (var i=0, loop=wysiwygs.length;i<loop;i++) {
					$("#"+$(wysiwygs[i]).attr("id")).redactor({minHeight:100,toolbarFixedTopOffset:50});
					Attachment.initEvent($(wysiwygs[i]).attr("id")+"-attachment");
					Attachment.setFiles($(wysiwygs[i]).attr("id")+"-attachment",attachments[$(wysiwygs[i]).attr("id")]);
				}
				
				form.find("input[name=parent]").val(parent);
				form.find("input[name=source]").val("");
				
				Dataroom.ment.init(form.attr("name"),"write");
				
				$(button).removeClass("selected");
				if ($(button).attr("data-cancel") !== undefined) {
					$(button).html($(button).data("default"));
				}
				
				if (iModule.isInScroll(form) == false) {
					$("html, body").animate({scrollTop:form.offset().top - 100},"fast");
				}
			} else {
				if (Dataroom.ment.selectedButton != null) {
					var parent = $(Dataroom.ment.selectedButton).parents(".mentItem").attr("data-parent");
					Dataroom.ment.reset($("form[name=ModuleDataroomMentForm-"+parent+"]"));
				}
				
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("dataroom","getMentDepth"),
					data:{idx:idx},
					dataType:"json",
					success:function(result) {
						if (result.success == false) {
							iModule.alertMessage.show("error",result.message,5);
						} else {
							if (Dataroom.ment.selectedButton !== null) {
								$(Dataroom.ment.selectedButton).removeClass("selected");
								$(Dataroom.ment.selectedButton).html($(Dataroom.ment.selectedButton).data("default"));
							}
							Dataroom.ment.selectedButton = button;
							var form = $("form[name=ModuleDataroomMentForm-"+result.parent+"]").clone();
							var attachments = {};
							var wysiwygs = form.find("textarea[data-wysiwyg=true]");
							for (var i=0, loop=wysiwygs.length;i<loop;i++) {
								var textarea = $(wysiwygs[i]).clone();
								textarea.val($("#"+textarea.attr("id")).redactor("code.get"));
								var wysiwygObject = $(wysiwygs[i]).parents("div.redactor-box");
								$(wysiwygs[i]).remove();
								textarea.insertBefore(wysiwygObject);
								wysiwygObject.remove();
								
								attachments[textarea.attr("id")] = Attachment.getFiles(textarea.attr("id")+"-attachment");
							}
							$("form[name=ModuleDataroomMentForm-"+result.parent+"]").remove();
							$("#ModuleDataroomMentItem-"+result.source).append(form);
							
							for (var i=0, loop=wysiwygs.length;i<loop;i++) {
								$("#"+$(wysiwygs[i]).attr("id")).redactor({minHeight:100,toolbarFixedTopOffset:50});
								Attachment.initEvent($(wysiwygs[i]).attr("id")+"-attachment");
								Attachment.setFiles($(wysiwygs[i]).attr("id")+"-attachment",attachments[$(wysiwygs[i]).attr("id")]);
							}
							
							form.find("input[name=parent]").val(result.parent);
							form.find("input[name=source]").val(result.source);
							
							$(button).addClass("selected");
							if ($(button).attr("data-cancel") !== undefined) {
								$(button).data("default",$(button).html());
								$(button).html($(button).attr("data-cancel"));
							}
							
							Dataroom.ment.init(form.attr("name"),"reply");
							
							if (iModule.isInScroll(form) == false) {
								$("html, body").animate({scrollTop:form.offset().top - 100},"fast");
							}
						}
					},
					error:function() {
						
					}
				});
			}
		},
		modify:function(idx,object) {
			var button = $(object).is("button") == true ? $(object) : null;
			
			if (button !== null && button.hasClass("selected") == true) {
				var parent = button.parents(".mentItem").attr("data-parent");
				$("#ModuleDataroomMentItem-"+idx).find(".mentContext").show();
				Dataroom.ment.reset($("form[name=ModuleDataroomMentForm-"+parent+"]"));
				return;
			}
			
			if (Dataroom.ment.selectedButton != null) {
				var parent = $(Dataroom.ment.selectedButton).parents(".mentItem").attr("data-parent");
				Dataroom.ment.reset($("form[name=ModuleDataroomMentForm-"+parent+"]"));
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","mentModify"),
				data:{idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Dataroom.ment.selectedButton = button;
						
						(function(result) {
							$("#ModuleDataroomMentItem-"+result.data.idx).find(".mentContext").hide();
							var form = $("form[name=ModuleDataroomMentForm-"+result.data.parent+"]").clone();
							
							var attachments = {};
							var wysiwygs = form.find("textarea[data-wysiwyg=true]");
							for (var i=0, loop=wysiwygs.length;i<loop;i++) {
								var textarea = $(wysiwygs[i]).clone();
								textarea.val(result.data.content);
								var wysiwygObject = $(wysiwygs[i]).parents("div.redactor-box");
								$(wysiwygs[i]).remove();
								textarea.insertBefore(wysiwygObject);
								wysiwygObject.remove();
								
								attachments[textarea.attr("id")] = Attachment.getFiles(textarea.attr("id")+"-attachment");
							}
							$("form[name=ModuleDataroomMentForm-"+result.data.parent+"]").remove();
							$("#ModuleDataroomMentItem-"+result.data.idx).append(form);
							
							for (var i=0, loop=wysiwygs.length;i<loop;i++) {
								$("#"+$(wysiwygs[i]).attr("id")).redactor({minHeight:100,toolbarFixedTopOffset:50});
								Attachment.initEvent($(wysiwygs[i]).attr("id")+"-attachment");
								Attachment.setFiles($(wysiwygs[i]).attr("id")+"-attachment",[]);
								Attachment.loadFile($(wysiwygs[i]).attr("id")+"-attachment",result.data.attachment);
							}
							
							form.find("input[name=parent]").val(result.data.parent);
							form.find("input[name=source]").val("");
							form.find("input[name=idx]").val(result.data.idx);
							if (form.find("input[name=name]").length > 0) form.find("input[name=name]").val(result.data.name);
							if (form.find("input[name=email]").length > 0) form.find("input[name=email]").val(result.data.email);
							form.find("input[name=is_secret]").prop("checked",result.data.is_secret == "TRUE");
							form.find("input[name=is_reply]").prop("checked",result.data.is_reply == "TRUE");
							form.find("input[name=is_push]").prop("checked",result.data.is_push == "TRUE");
							form.find("input[name=is_hidename]").prop("checked",result.data.is_hidename == "TRUE");
							
							$(Dataroom.ment.selectedButton).addClass("selected");
							if ($(Dataroom.ment.selectedButton).attr("data-cancel") !== undefined) {
								$(Dataroom.ment.selectedButton).data("default",$(Dataroom.ment.selectedButton).html());
								$(Dataroom.ment.selectedButton).html($(Dataroom.ment.selectedButton).attr("data-cancel"));
							}
							
							Dataroom.ment.init(form.attr("name"),"modify");
							
							var helpBlocks = form.find(".helpBlock[data-modify]");
							for (var i=0, loop=helpBlocks.length;i<loop;i++) {
								$(helpBlocks[i]).html($(helpBlocks[i]).attr("data-modify"));
							}
							
							if (iModule.isInScroll(form) == false) {
								$("html, body").animate({scrollTop:form.offset().top - 100},"fast");
							}
						})(result);
					} else {
						if (result.message) iModule.alertMessage.show("error",result.message,5);
					}
				}
			});
			
			return false;
		},
		delete:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","mentDelete"),
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
		loadPage:function(page,parent) {
			if (typeof parent == "object") parent = $(parent).parents("div[id|=ModuleDataroomMentPagination]").attr("data-parent");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","getMent"),
				data:{get:"page",parent:parent,page:page},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Dataroom.ment.print(result);
						
						if (iModule.isInScroll($("#ModuleDataroomMentList-"+result.parent)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleDataroomMentList-"+result.parent).offset().top - 100},"fast");
						}
					}
				}
			});
			return false;
		},
		loadIdx:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","getMent"),
				data:{get:"idx",idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Dataroom.ment.print(result);
						
						if (iModule.isInScroll($("#ModuleDataroomMentItem-"+idx)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleDataroomMentItem-"+idx).offset().top - 100},"fast");
						}
					}
				}
			});
		},
		print:function(result) {
			if (result.mentHtml !== undefined) {
				$("#ModuleDataroomMentList-"+result.parent).html(result.mentHtml);
			} else {
				$("#ModuleDataroomMentList-"+result.parent+" .empty").remove();
				Dataroom.ment.printRemove(result.parent,result.idxs);
				for (var i=0, loop=result.ments.length;i<loop;i++) {
					var ment = $(result.ments[i].html);
					ment.find("img").on("load",function() {
						if ($(this).parents(".wrapContent").innerWidth() < $(this).width()) {
							$(this).width($(this).parents(".wrapContent").innerWidth());
						}
					});
					
					if ($("#ModuleDataroomMentItem-"+result.ments[i].idx).length == 0) {
						if (i == 0) {
							$("#ModuleDataroomMentList-"+result.parent).prepend(ment);
						} else {
							ment.insertAfter($("#ModuleDataroomMentList-"+result.parent+" .mentItem[data-idx]")[i-1]);
						}
					} else if (parseInt($("#ModuleDataroomMentItem-"+result.ments[i].idx).attr("data-modify")) != result.ments[i].modify_date) {
						$("#ModuleDataroomMentItem-"+result.ments[i].idx).replaceWith(ment);
						console.log("업뎃한다!");
					}
				}
			}
			$(".liveUpdateDataroomPostMent"+result.parent).text(result.mentCount);
			$("#ModuleDataroomMentPagination-"+result.parent).replaceWith(result.pagination);
			
			$(document).triggerHandler("Dataroom.ment.print",[result]);
		},
		printRemove:function(parent,ments) {
			var lists = $("#ModuleDataroomMentList-"+parent+" .mentItem[data-idx]");
			for (var i=0, loop=lists.length;i<loop;i++) {
				if ($.inArray(parseInt($(lists[i]).attr("data-idx")),ments) == -1) {
					$(lists[i]).remove();
				}
			}
		}
	},
	vote:{
		good:function(idx,button) {
			if (button) {
				$(button).addClass("selected").attr("disabled",true);
				$(button).find(".fa").data("class",$(button).find(".fa").attr("class")).removeClass().addClass("fa fa-spinner fa-spin");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","vote"),
				data:{type:"post",idx:idx,vote:"good"},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.alertMessage.show("default",result.message,5);
						$("."+result.liveUpdate).text(result.liveValue);
					} else {
						iModule.alertMessage.show("error",result.message,5);
						if (result.result === undefined || result.result != "GOOD") {
							$(button).removeClass("selected");
						}
					}
					
					if (button) {
						$(button).attr("disabled",false);
						$(button).find(".fa").removeClass("fa-spinner fa-spin").addClass($(button).find(".fa").data("class"));
					}
				}
			});
		},
		bad:function(idx,button) {
			if (button) {
				$(button).addClass("selected").attr("disabled",true);
				$(button).find(".fa").data("class",$(button).find(".fa").attr("class")).removeClass().addClass("fa fa-spinner fa-spin");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("dataroom","vote"),
				data:{type:"post",idx:idx,vote:"bad"},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						iModule.alertMessage.show("default",result.message,5);
						$("."+result.liveUpdate).text(result.liveValue);
					} else {
						iModule.alertMessage.show("error",result.message,5);
						if (result.result === undefined || result.result != "BAD") {
							$(button).removeClass("selected");
						}
					}
					
					if (button) {
						$(button).attr("disabled",false);
						$(button).find(".fa").removeClass("fa-spinner fa-spin").addClass($(button).find(".fa").data("class"));
					}
				}
			});
		}
	},
	download:function(idx,version,confirm) {
		var version = version ? version : "latest";
		var confirm = confirm === true ? "TRUE" : "FALSE";
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("dataroom","downloadConfirm"),
			data:{idx:idx,version:version,confirm:confirm},
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					if (result.downloadUrl) {
						location.href = result.downloadUrl;
						if (confirm == "TRUE") iModule.modal.close();
					} else {
						iModule.modal.showHtml(result.modalHtml);
					}
				} else {
					iModule.alertMessage.show("error",result.message,5);
				}
			}
		});
		
		return false;
	},
	delete:function(form) {
		var form = $(form);
		
		iModule.buttonStatus(form,"loading");
		iModule.inputStatus(form,"default");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("dataroom","delete"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					if (result.type == "post") {
//						$("form[name=ModuleDataroomListForm]").submit();
					} else if (result.type == "ment") {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						
						if (result.position) {
							Dataroom.ment.loadIdx(result.position);
						} else {
							Dataroom.ment.loadPage(1,result.parent);
						}
						iModule.modal.close();
					} else if (result.type == "answer") {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Dataroom.qna.loadIdx(result.parent);
						
						iModule.modal.close();
					}
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
			}
		});
		
		return false;
	},
	sort:function(sort) {
		$("form[name=ModuleDataroomListForm]").find("input[name=sort]").val(sort);
		$("form[name=ModuleDataroomListForm]").submit();
	}
};

$(document).ready(function() {
	var temp = location.href.split("#");
	if (temp.length == 2 && temp[1].indexOf("ment") == 0) {
		var ment = temp[1].replace("ment","");
		if ($("#ModuleBoatdMentItem-"+ment).length == 1) {
			$("html, body").animate({scrollTop:$("#ModuleBoatdMentItem-"+ment).offset().top - 100},"fast");
		} else {
			Dataroom.ment.loadIdx(ment);
		}
	}
	
	$("a").on("click",function() {
		var temp = $(this).attr("href").split("#");
		if (temp.length == 2 && location.href.split("#").shift().search(new RegExp(temp[0]+"$")) != -1 && temp[1].indexOf("ment") == 0) {
			var ment = temp[1].replace("ment","");
			if ($("#ModuleBoatdMentItem-"+ment).length == 1) {
				$("html, body").animate({scrollTop:$("#ModuleBoatdMentItem-"+ment).offset().top - 100},"fast");
			} else {
				Dataroom.ment.loadIdx(ment);
			}
		}
	});
});