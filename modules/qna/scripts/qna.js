var Qna = {
	getListUrl:function(form) {
		var form = $(form);
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("qna","listUrl"),
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
						Qna.post.check($(this));
					});
				}
			}
			
			$(document).triggerHandler("Qna.post.init",[form]);
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
				url:ENV.getProcessUrl("qna","postWrite"),
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
		}
	},
	answer:{
		init:function(form,type) {
			var form = $("form[name="+form+"]");
			var type = type ? type : "write";
			iModule.inputStatus(form,"default");
			
			$(document).triggerHandler("Qna.answer.init",[form]);
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
				url:ENV.getProcessUrl("qna","answerWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Qna.answer.load(result.parent,result.idx);
						Qna.answer.reset(form);
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
		select:function(idx,confirm) {
			var confirm = confirm == true ? "TRUE" : "FALSE";
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("qna","answerSelect"),
				data:{idx:idx,confirm:confirm},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.modalHtml) {
							iModule.modal.showHtml(result.modalHtml);
						} else {
							location.href = location.href.split("#").shift();
						}
					} else {
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
				}
			});
			
			return false;
		},
		load:function(parent,idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("qna","getAnswer"),
				data:{parent:parent},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Qna.answer.print(result);
						
						if (iModule.isInScroll($("#ModuleQnaAnswerItem-"+idx)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleQnaAnswerItem-"+idx).offset().top - 100},"fast");
						}
					}
				}
			});
		},
		print:function(result) {
			$("#ModuleQnaAnswerList-"+result.parent+" .empty").remove();
			for (var i=0, loop=result.answers.length;i<loop;i++) {
				var answer = $(result.answers[i].html);
				answer.find("img").on("load",function() {
					if ($(this).parents(".wrapContent").innerWidth() < $(this).width()) {
						$(this).width($(this).parents(".wrapContent").innerWidth());
					}
				});
				
				if ($("#ModuleQnaAnswerItem-"+result.answers[i].idx).length == 0) {
					$("#ModuleQnaAnswerList-"+result.parent).append(answer);
				}
			}
			$(".liveUpdateQnaAnswer"+result.parent).text(result.answerCount);
			$(document).triggerHandler("Qna.answer.print",[result]);
		},
		reset:function(form) {
			var parent = form.find("input[name=parent]").val();
			
			form.find("input, textarea").val("");
			form.find("input[name=parent]").val(parent);
			
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
			
			Qna.answer.init(form.attr("name"));
		}
	},
	ment:{
		init:function(form,type) {
			var form = $("form[name="+form+"]");
			var type = type ? type : "write";
			iModule.inputStatus(form,"default");
			
			$(document).triggerHandler("Qna.ment.init",[form]);
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
				url:ENV.getProcessUrl("qna","mentWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Qna.ment.load(result.parent,result.idx);
						Qna.ment.reset(form);
					} else {
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
					
					iModule.buttonStatus(form,"reset");
				}
			});
			
			return false;
		},
		load:function(parent,idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("qna","getMent"),
				data:{parent:parent},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						Qna.ment.print(result);
						
						if (iModule.isInScroll($("#ModuleQnaMentItem-"+idx)) == false) {
							$("html,body").animate({scrollTop:$("#ModuleQnaMentItem-"+idx).offset().top - 100},"fast");
						}
					}
				}
			});
		},
		print:function(result) {
			$("#ModuleQnaMentList-"+result.parent+" .empty").remove();
			for (var i=0, loop=result.ments.length;i<loop;i++) {
				var ment = $(result.ments[i].html);
				ment.find("img").on("load",function() {
					if ($(this).parents(".wrapContent").innerWidth() < $(this).width()) {
						$(this).width($(this).parents(".wrapContent").innerWidth());
					}
				});
				
				if ($("#ModuleQnaMentItem-"+result.ments[i].idx).length == 0) {
					$("#ModuleQnaMentList-"+result.parent).append(ment);
				}
			}
			$(".liveUpdateQnaMent"+result.parent).text(result.answerCount);
			$(document).triggerHandler("Qna.ment.print",[result]);
		},
		reset:function(form) {
			var parent = form.find("input[name=parent]").val();
			
			form.find("input, textarea").val("");
			form.find("input[name=parent]").val(parent);
			
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
			
			Qna.answer.init(form.attr("name"));
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
				url:ENV.getProcessUrl("qna","vote"),
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
				url:ENV.getProcessUrl("qna","vote"),
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
	sort:function(sort) {
		$("form[name=ModuleQnaListForm]").find("input[name=sort]").val(sort);
		$("form[name=ModuleQnaListForm]").submit();
	}
};

$(document).ready(function() {
	var temp = location.href.split("#");
	if (temp.length == 2 && temp[1].indexOf("answer") == 0) {
		var answer = temp[1].replace("answer","");
		if ($("#ModuleQnaAnswerItem-"+answer).length == 1) {
			$("html, body").animate({scrollTop:$("#ModuleQnaAnswerItem-"+answer).offset().top - 100},"fast");
		}
	}
	
	$("a").on("click",function() {
		var temp = $(this).attr("href").split("#");
		if (temp.length == 2 && location.href.split("#").shift().search(new RegExp(temp[0]+"$")) != -1 && temp[1].indexOf("answer") == 0) {
			var answer = temp[1].replace("answer","");
			if ($("#ModuleQnaAnswerItem-"+answer).length == 1) {
				$("html, body").animate({scrollTop:$("#ModuleQnaAnswerItem-"+answer).offset().top - 100},"fast");
			}
		}
	});
});