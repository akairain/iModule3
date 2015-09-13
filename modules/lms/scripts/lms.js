var Lms = {
	getListUrl:function(form) {
		var form = $(form);
		
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("lms","listUrl"),
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
	create:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input, textarea");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Lms.create.check($(this));
					});
				}
			}
			
			form.find("input[name=type]").on("change",function() {
				var input = $(this).parents("form").find(".selectControl[data-field=attend]");
				var helpBlock = $(this).parents("form").find(".selectControl[data-field=attend]").parents(".inputBlock, .inputInline").find(".helpBlock");
				if ($(this).val() == "public") {
					input.show();
					helpBlock.html(helpBlock.attr("data-default"));
				} else {
					input.hide();
					helpBlock.html(helpBlock.attr("data-private"));
				}
			});
			
			$(document).triggerHandler("Lms.create.init",[form]);
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
			
			var imageData = $(".photo-editor").cropit("export");
			if (imageData != null) {
				form.find("input[name=cover]").val(imageData);
			}

			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","create"),
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
	subject:{
		add:function(parent) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","postSubject"),
				data:{type:"add",parent:parent},
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
		config:function(button) {
			if ($(button).parents("[data-subject-idx]").length == 0) return;
			
			var idx = $(button).parents("[data-subject-idx]").attr("data-subject-idx");
			var data = $(button).parents("[data-subject-idx]").data("data");
			
			if ($(button).parents("[data-subject-idx]").find("ul.menu").is(":visible") == true) {
				$(button).parents("[data-subject-idx]").find("ul.menu").hide();
				return;
			} else {
				$("ul[data-role=config]").hide();
			}
			
			if (data == undefined) {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("lms","getConfig"),
					data:{idx:idx,type:"subject"},
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							$(button).parents("[data-subject-idx]").data("data",true);
							
							var menu = $(button).parents("[data-subject-idx]").find("ul.menu");
							var sort = menu.find("li[data-role=sort] ul.submenu");
							sort.empty();
							
							for (var i=0, loop=result.subjects.length;i<loop;i++) {
								var item = $("<li>").html(sort.attr("data-title").replace("{title}",result.subjects[i].title));
								sort.append(item);
							}
							
							if (sort.find("li").length == 0) sort.find("li[data-role=move]").remove();
							
							Lms.subject.config(button);
						} else {
							iModule.alertMessage.show("error",result.message,5);
						}
					},
					error:function() {
						iModule.alertMessage.show("Server Connect Error!");
					}
				});
			} else {
				$(button).parents("[data-subject-idx]").find("ul.menu").show();
			}
			
			window.event.stopPropagation();
		},
		submit:function(form) {
			var form = $(form);
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","postSubject"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						location.href = location.href;
					} else {
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
	},
	attend:{
		register:function(form) {
			var form = $(form);
			
			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","attendClass"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.redirect) location.href = result.redirect;
						if (result.post) {
							iModule.modal.close();
							Lms.post.view(result.post);
						}
					} else {
						if (result.message) {
							iModule.alertMessage.show("error",result.message,5);
						}
					}
					iModule.buttonStatus(form,"reset");
				},
				error:function() {
					iModule.alertMessage.show("Server Connect Error!");
				}
			});
			
			return false;
		}
	},
	item:{
		add:function(parent) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","addItem"),
				data:{parent:parent},
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
		select:function(select) {
			if ($(select).attr("data-type") != "video" && $(select).attr("data-type") != "youtube") {
				alert("아직 지원되지 않습니다.");
				return;
			}
			
			$(select).parents(".addItemModal").find(".box[data-type]").parents(".item").removeClass("selected");
			$(select).parents(".item").addClass("selected");
			$(select).parents("form").find("input[name=type]").val($(select).attr("data-type"));
		},
		post:function(form) {
			if ($(form).find("input[name=type]").val() == "") {
				iModule.alertMessage.show("error",$(form).find("input[name=type]").attr("data-error"),5);
				return false;
			}
			
			$(form).attr("action",ENV.getUrl(null,null,"write",null));
			$(form).submit();
		}
	},
	post:{
		init:function(form) {
			var form = $("form[name="+form+"]");
			
			iModule.inputStatus(form,"default");
			
			var inputControl = form.find("input");
			for (var i=0, loop=inputControl.length;i<loop;i++) {
				if ($(inputControl[i]).attr("required") == "required") {
					$(inputControl[i]).on("blur",function() {
						Lms.post.check($(this));
					});
				}
			}
			
			$(document).triggerHandler("Lms.post.init",[form]);
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
				url:ENV.getProcessUrl("lms","postWrite"),
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
		},
		view:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","postView"),
				data:{idx:idx},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.modalHtml) {
							iModule.modal.showHtml(result.modalHtml);
						} else if (result.redirect) {
							location.href = result.redirect;
						}
					} else {
						iModule.alertMessage.show("error",result.message,5);
					}
				}
			});
		},
		config:function(button) {
			if ($(button).parents("[data-post-idx]").length == 0) return;
			
			var idx = $(button).parents("[data-post-idx]").attr("data-post-idx");
			var data = $(button).parents("[data-post-idx]").data("data");
			
			if ($(button).parents("[data-post-idx]").find("ul.menu").is(":visible") == true) {
				$(button).parents("[data-post-idx]").find("ul.menu").hide();
				return;
			} else {
				$("ul[data-role=config]").hide();
			}
			
			if (data == undefined) {
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("lms","getConfig"),
					data:{idx:idx,type:"post"},
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							$(button).parents("[data-post-idx]").data("data",true);
							
							var menu = $(button).parents("[data-post-idx]").find("ul.menu");
							var sort = menu.find("li[data-role=sort] ul.submenu");
							sort.empty();
							
							for (var i=0, loop=result.posts.length;i<loop;i++) {
								var item = $("<li>").html(sort.attr("data-title").replace("{title}",result.posts[i].title));
								sort.append(item);
							}
							
							if (sort.find("li").length == 0) menu.find("li[data-role=sort]").remove();
							
							var move = menu.find("li[data-role=move] ul.submenu");
							move.empty();
							
							for (var i=0, loop=result.subjects.length;i<loop;i++) {
								var item = $("<li>").html(move.attr("data-title").replace("{title}",result.subjects[i].title));
								move.append(item);
							}
							
							if (move.find("li").length == 0) menu.find("li[data-role=move]").remove();
							
							Lms.post.config(button);
						} else {
							iModule.alertMessage.show("error",result.message,5);
						}
					},
					error:function() {
						iModule.alertMessage.show("Server Connect Error!");
					}
				});
			} else {
				console.log("olleh!",data);
				
				$(button).parents("[data-post-idx]").find("ul.menu").show();
			}
			
			window.event.stopPropagation();
		},
		toggleStatus:function(type) {
			if ($("#ModuleLmsStatusBar").is(":visible") == true) {
				$("#ModuleLmsStatusBar").hide();
				$("#ModuleLmsInfoBar .statusToggle").addClass("off");
			} else {
				$("#ModuleLmsStatusBar").show();
				$("#ModuleLmsInfoBar .statusToggle").addClass("on");
				if (typeof Lms[type] == "object" && typeof Lms[type].drawStatus == "function") Lms[type].drawStatus("init");
			}
		},
		modify:function(idx,form) {
			var form = form ? $(form) : null;
			
			if (form !== null) {
				iModule.buttonStatus(form,"loading");
				iModule.inputStatus(form,"default");
			}
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","postModify"),
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
				url:ENV.getProcessUrl("lms","postDelete"),
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
			
			$(document).triggerHandler("Lms.ment.init",[form]);
		},
		getMent:function(parent) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","getMent"),
				data:{parent:parent},
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						for (var i=0, loop=result.ments.length;i<loop;i++) {
							Lms.ment.print(result.ments[i]);
						}
					}
				}
			});
		},
		getStatus:function(parent) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","getMentStatus"),
				data:{parent:parent},
				dataType:"json",
				success:function(result) {
					console.log(result);
					/*
					if (result.success == true) {
						for (var i=0, loop=result.ments.length;i<loop;i++) {
							Lms.ment.print(result.ments[i]);
						}
					}
					*/
				}
			});
		},
		print:function(ment) {
			if ($("ModuleLmsProgressBar").find(".mentIcon[data-idx="+ment.idx+"]").length == 0) {
				var mentIcon = $("<div>").addClass("mentIcon").attr("data-idx",ment.idx);
				var mentBox = $("<div>").addClass("ment");
				var photo = $("<div>").addClass("photo").css("backgroundImage","url("+ment.photo+")");
				var text = $("<div>").addClass("content").html(ment.content);
				
				if (ment.position > 50) {
					text.css("right",20).addClass("right");
				} else {
					text.css("left",20).addClass("left");
				}
				
				mentBox.append(photo);
				mentBox.append(text);
				
				
				
				mentIcon.append(mentBox);
				mentIcon.css("left",ment.position+"%");
				
				$("#ModuleLmsProgressBar").append(mentIcon);
			}
			/*
			for (var i=0, loop=Lms.youtube.ments.length;i<loop;i++) {
				var ment = $("<div>").addClass("mentIcon").css("backgroundImage","url("+Lms.youtube.ments[i].photo+")").css("left",(Lms.youtube.ments[i].time / Lms.youtube.trackerTotalTime * 100)+"%");
				
				//.css("backgroundImage","url("+Lms.youtube.ments[i].photo+")");
				
				$("#ModuleLmsProgressBar").append(ment);
			}*/
		},
		submit:function() {
			if ($("#ModuleLmsMentInput").val().length > 0) {
				var content = $("#ModuleLmsMentInput").val();
				var parent = $("#ModuleLmsMentInput").attr("data-idx");
				var position = $("#ModuleLmsMentInput").data("position");
				
				$("#ModuleLmsMentInput").attr("disabled",true);
				
				$.ajax({
					type:"POST",
					url:ENV.getProcessUrl("lms","mentWrite"),
					data:{parent:parent,content:content,position:position},
					dataType:"json",
					success:function(result) {
						if (result.success == true) {
							iModule.alertMessage.show("success",result.message,5);
							Lms.ment.getMent(parent);
						} else {
							iModule.alertMessage.show("error",result.message,5);
						}
						
						$("#ModuleLmsMentInput").val("");
						$("#ModuleLmsMentInput").attr("disabled",false);
						console.log(result);
						
						/*
						if (result.success == true) {
							if (result.message) iModule.alertMessage.show("success",result.message,5);
							Lms.ment.loadIdx(result.idx);
							iModule.buttonStatus(form,"reset");
							Lms.ment.reset(form);
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
						*/
					}
				});
			}
			
			
			/*
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
				url:ENV.getProcessUrl("lms","mentWrite"),
				data:form.serialize(),
				dataType:"json",
				success:function(result) {
					if (result.success == true) {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						Lms.ment.loadIdx(result.idx);
						iModule.buttonStatus(form,"reset");
						Lms.ment.reset(form);
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
			*/
			
			return false;
		},
		delete:function(idx) {
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("lms","mentDelete"),
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
				url:ENV.getProcessUrl("lms","vote"),
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
				url:ENV.getProcessUrl("lms","vote"),
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
			url:ENV.getProcessUrl("lms","delete"),
			data:form.serialize(),
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					if (result.type == 'post') {
						$("form[name=ModuleLmsListForm]").submit();
					} else {
						if (result.message) iModule.alertMessage.show("success",result.message,5);
						
						if (result.position) {
							Lms.ment.loadIdx(result.position);
						} else {
							Lms.ment.loadPage(1,result.parent);
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
			Lms.ment.loadIdx(ment);
		}
	}
	
	$("a").on("click",function() {
		var temp = $(this).attr("href").split("#");
		if (temp.length == 2 && location.href.split("#").shift().search(new RegExp(temp[0]+"$")) != -1 && temp[1].indexOf("ment") == 0) {
			var ment = temp[1].replace("ment","");
			if ($("#ModuleBoatdMentItem-"+ment).length == 1) {
				$("html, body").animate({scrollTop:$("#ModuleBoatdMentItem-"+ment).offset().top - 100},"fast");
			} else {
				Lms.ment.loadIdx(ment);
			}
		}
	});
	
	$(document).on("click",function(e) {
		$("ul[data-role=config]").hide();
		e.stopPropagation();
	});
});