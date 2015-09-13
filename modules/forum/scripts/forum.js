var Forum = {
	getListUrl:function(form) {
		var form = $(form);
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("forum","listUrl"),
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
						Forum.post.check($(this));
					});
				}
			}
			
			$(document).triggerHandler("Forum.post.init",[form]);
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
				url:ENV.getProcessUrl("forum","postWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						location.href = result.redirect;
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
		},
		modify:function(idx,form) {
			var form = form ? $(form) : null;
			
			if (form !== null) {
				iModule.buttonStatus(form,"loading");
				iModule.inputStatus(form,"default");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","postModify"),
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
				url:ENV.getProcessUrl("forum","postDelete"),
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
	ment:{
		selectedButton:null,
		init:function(form,type) {
			var form = $("form[name="+form+"]");
			var type = type ? type : "write";
			iModule.inputStatus(form,"default");
			form.find("span[data-title-"+type+"]").html(form.find("span[data-title-"+type+"]").attr("data-title-"+type));
			
			$(document).triggerHandler("Forum.ment.init",[form]);
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
				url:ENV.getProcessUrl("forum","mentWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Forum.ment.loadIdx(result.idx);
						iModule.buttonStatus(form,"reset");
						Forum.ment.reset(form);
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
		reset:function(form) {
			var idx = form.find("input[name=idx]").val();
			var parent = form.find("input[name=parent]").val();
			var source = form.find("input[name=source]").val();
			
			if (idx) $("#ModuleDataroomMentItem-"+idx).find(".mentContext").show();
			
			form.find("input, textarea").val("");
			form.find("input[name=parent]").val(parent);
			
			if (idx || source) {
				form = form.clone();
				$("form[name=ModuleForumMentForm-"+parent+"]").remove();
				$("#ModuleForumMentWrite-"+parent).append(form);
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
			
			if (Forum.ment.selectedButton != null) {
				$(Forum.ment.selectedButton).removeClass("selected");
				if ($(Forum.ment.selectedButton).attr("data-cancel") !== undefined) {
					$(Forum.ment.selectedButton).html($(Forum.ment.selectedButton).data("default"));
				}
			}
			
			form.find("input[name=is_secret]").prop("checked",false);
			form.find("input[name=is_reply]").prop("checked",true);
			form.find("input[name=is_push]").prop("checked",true);
			form.find("input[name=is_hidename]").prop("checked",false);
			
			Forum.ment.init(form.attr("name"));
		},
		reply:function(idx,button) {
			if ($(button).hasClass("selected") == true) {
				if (Forum.ment.selectedButton !== null) {
					$(Forum.ment.selectedButton).removeClass("selected");
					$(Forum.ment.selectedButton).html($(Forum.ment.selectedButton).data("default"));
				}
				Forum.ment.selectedButton = null;
				
				var parent = $(button).parents(".mentItem").attr("data-parent");
				var form = $("form[name=ModuleForumMentForm-"+parent+"]").clone();
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
				$("form[name=ModuleForumMentForm-"+parent+"]").remove();
				$("#ModuleForumMentWrite-"+parent).append(form);
				
				for (var i=0, loop=wysiwygs.length;i<loop;i++) {
					$("#"+$(wysiwygs[i]).attr("id")).redactor({minHeight:100,toolbarFixedTopOffset:50});
					Attachment.initEvent($(wysiwygs[i]).attr("id")+"-attachment");
					Attachment.setFiles($(wysiwygs[i]).attr("id")+"-attachment",attachments[$(wysiwygs[i]).attr("id")]);
				}
				
				form.find("input[name=parent]").val(parent);
				form.find("input[name=source]").val("");
				
				Forum.ment.init(form.attr("name"),"write");
				
				$(button).removeClass("selected");
				if ($(button).attr("data-cancel") !== undefined) {
					$(button).html($(button).data("default"));
				}
				
				if (iModule.isInScroll(form) == false) {
					$("html, body").animate({scrollTop:form.offset().top - 100},"fast");
				}
			} else {
				if (Forum.ment.selectedButton != null) {
					var parent = $(Forum.ment.selectedButton).parents(".mentItem").attr("data-parent");
					Forum.ment.reset($("form[name=ModuleForumMentForm-"+parent+"]"));
				}
				
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("forum","getMentDepth"),
					data:{idx:idx},
					dataType:"json",
					success:function(result) {
						if (result.success == false) {
							iModule.alertMessage.show("error",result.message,5);
						} else {
							if (Forum.ment.selectedButton !== null) {
								$(Forum.ment.selectedButton).removeClass("selected");
								$(Forum.ment.selectedButton).html($(Forum.ment.selectedButton).data("default"));
							}
							Forum.ment.selectedButton = button;
							var form = $("form[name=ModuleForumMentForm-"+result.parent+"]").clone();
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
							$("form[name=ModuleForumMentForm-"+result.parent+"]").remove();
							$("#ModuleForumMentItem-"+result.source).append(form);
							
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
							
							Forum.ment.init(form.attr("name"),"reply");
							
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
				$("#ModuleForumMentItem-"+idx).find(".mentContext").show();
				Forum.ment.reset($("form[name=ModuleForumMentForm-"+parent+"]"));
				return;
			}
			
			if (Forum.ment.selectedButton != null) {
				var parent = $(Forum.ment.selectedButton).parents(".mentItem").attr("data-parent");
				Forum.ment.reset($("form[name=ModuleForumMentForm-"+parent+"]"));
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","mentModify"),
				data:{idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (Forum.ment.selectedButton !== null) {
							$(Forum.ment.selectedButton).removeClass("selected");
							$(Forum.ment.selectedButton).html($(Forum.ment.selectedButton).data("default"));
						}
						
						if (iModule.modal.modal !== null) {
							Forum.ment.selectedButton = iModule.modal.modal.data("button");
							iModule.modal.close();
						} else {
							Forum.ment.selectedButton = button;
						}
						
						(function(result) {
							$("#ModuleForumMentItem-"+result.data.idx).find(".mentContext").hide();
							var form = $("form[name=ModuleForumMentForm-"+result.data.parent+"]").clone();
							
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
							$("form[name=ModuleForumMentForm-"+result.data.parent+"]").remove();
							$("#ModuleForumMentItem-"+result.data.idx).append(form);
							
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
							
							$(Forum.ment.selectedButton).addClass("selected");
							if ($(Forum.ment.selectedButton).attr("data-cancel") !== undefined) {
								$(Forum.ment.selectedButton).data("default",$(Forum.ment.selectedButton).html());
								$(Forum.ment.selectedButton).html($(Forum.ment.selectedButton).attr("data-cancel"));
							}
							
							Forum.ment.init(form.attr("name"),"modify");
							
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
		loadPage:function(page,parent) {
			if (typeof parent == "object") parent = $(parent).parents("div[id|=ModuleForumMentPagination]").attr("data-parent");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","getMent"),
				data:{get:"page",parent:parent,page:page},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Forum.ment.print(result);
						
						if (iModule.isInScroll($("#ModuleForumMentList-"+result.parent)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleForumMentList-"+result.parent).offset().top - 100},"fast");
						}
					}
				}
			});
			return false;
		},
		loadIdx:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","getMent"),
				data:{get:"idx",idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Forum.ment.print(result);
						
						if (iModule.isInScroll($("#ModuleForumMentItem-"+idx)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleForumMentItem-"+idx).offset().top - 100},"fast");
						}
					}
				}
			});
		},
		print:function(result) {
			if (result.mentHtml !== undefined) {
				$("#ModuleForumMentList-"+result.parent).html(result.mentHtml);
			} else {
				$("#ModuleForumMentList-"+result.parent+" .empty").remove();
				Forum.ment.printRemove(result.parent,result.idxs);
				for (var i=0, loop=result.ments.length;i<loop;i++) {
					var ment = $(result.ments[i].html);
					ment.find("img").on("load",function() {
						if ($(this).parents(".wrapContent").innerWidth() < $(this).width()) {
							$(this).width($(this).parents(".wrapContent").innerWidth());
						}
					});
					
					if ($("#ModuleForumMentItem-"+result.ments[i].idx).length == 0) {
						if (i == 0) {
							$("#ModuleForumMentList-"+result.parent).prepend(ment);
						} else {
							ment.insertAfter($("#ModuleForumMentList-"+result.parent+" .mentItem[data-idx]")[i-1]);
						}
					} else if (parseInt($("#ModuleForumMentItem-"+result.ments[i].idx).attr("data-modify")) != result.ments[i].modify_date) {
						$("#ModuleForumMentItem-"+result.ments[i].idx).replaceWith(ment);
					}
				}
			}
			$(".liveUpdateForumMent"+result.parent).text(result.mentCount);
			$("#ModuleForumMentPagination-"+result.parent).replaceWith(result.pagination);
			
			$(document).triggerHandler("Forum.ment.print",[result]);
		},
		printRemove:function(parent,ments) {
			var lists = $("#ModuleForumMentList-"+parent+" .mentItem[data-idx]");
			for (var i=0, loop=lists.length;i<loop;i++) {
				if ($.inArray(parseInt($(lists[i]).attr("data-idx")),ments) == -1) {
					$(lists[i]).remove();
				}
			}
		},
		delete:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","mentDelete"),
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
	vote:{
		good:function(idx,button) {
			if (button) {
				$(button).addClass("selected").attr("disabled",true);
				$(button).find(".fa").data("class",$(button).find(".fa").attr("class")).removeClass().addClass("fa fa-spinner fa-spin");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("forum","vote"),
				data:{idx:idx,vote:"good"},
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
				url:ENV.getProcessUrl("forum","vote"),
				data:{idx:idx,vote:"bad"},
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
	delete:function(form) {
		var form = $(form);
		
		iModule.buttonStatus(form,"loading");
		iModule.inputStatus(form,"default");
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("forum","delete"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					if (result.type == 'post') {
						$("form[name=ModuleForumListForm]").submit();
					} else {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						
						if (result.position) {
							Forum.ment.loadIdx(result.position);
						} else {
							Forum.ment.loadPage(1,result.parent);
						}
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
	}
};

$(document).ready(function() {
	var temp = location.href.split("#");
	if (temp.length == 2 && temp[1].indexOf("ment") == 0) {
		var ment = temp[1].replace("ment","");
		if ($("#ModuleBoatdMentItem-"+ment).length == 1) {
			$("html, body").animate({scrollTop:$("#ModuleBoatdMentItem-"+ment).offset().top - 100},"fast");
		} else {
			Forum.ment.loadIdx(ment);
		}
	}
	
	$("a").on("click",function() {
		var temp = $(this).attr("href").split("#");
		if (temp.length == 2 && location.href.split("#").shift().search(new RegExp(temp[0]+"$")) != -1 && temp[1].indexOf("ment") == 0) {
			var ment = temp[1].replace("ment","");
			if ($("#ModuleBoatdMentItem-"+ment).length == 1) {
				$("html, body").animate({scrollTop:$("#ModuleBoatdMentItem-"+ment).offset().top - 100},"fast");
			} else {
				Forum.ment.loadIdx(ment);
			}
		}
	});
});