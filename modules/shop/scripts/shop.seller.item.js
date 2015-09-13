Shop.seller.item = {
	add:function() {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("shop","sellerItemAddModal"),
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
	status:function(idx,status) {
		if (confirm("해당 상품을 판매신청하시겠습니까?") == true) {
			alert(idx);
		}
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
			
			form.find("input[name=image]").on("change",function(e) {
				for (var i=0, loop=e.target.files.length;i<loop;i++) {
					var file = e.target.files[i];
					file.imageType = $(this).attr("data-type");
					if (file.imageType == "default") file.imageIdx = form.find("input[name=image_default]").val();
					form.data("uploading").push(file);
				}
				
				Shop.seller.item.post.uploadImage();
			});
			
			iModule.initSelectControl(form.find(".selectControl[data-field=category1]"));
			iModule.initSelectControl(form.find(".selectControl[data-field=category2]"));
			iModule.initSelectControl(form.find(".selectControl[data-field=category3]"));
			iModule.initSelectControl(form.find(".selectControl[data-field=allow_youth]"));
			
			form.find(".selectControl[data-field=category1] > button").attr("disabled",true);
			form.find(".selectControl[data-field=category2] > button").attr("disabled",true);
			form.find(".selectControl[data-field=category3] > button").attr("disabled",true);
			
			form.find("input[name=category1], input[name=category2], input[name=category3]").on("change",function() {
				var depth = parseInt($(this).attr("name").replace("category",""));
				if (depth < 3) {
					form.find("input[name=category"+(depth + 1)+"]").val("");
					form.find(".selectControl[data-field="+(depth + 1)+"] > button").attr("disabled",true);
					Shop.getCategory($(this).val(),Shop.seller.item.post.loadCategory);
				}
			});
			
			Shop.getCategory(0,Shop.seller.item.post.loadCategory);
			$("#ModuleShopSellerItemOptionList").hide();
			$("#ModuleShopSellerItemOption").hide();
			
			form.find("input[name=option_enable]").on("change",function() {
				if ($(this).is(":checked") == true) {
					$("#ModuleShopSellerItemOption").show();
					if ($("#ModuleShopSellerItemOptionList .list .row").length > 0) {
						$("#ModuleShopSellerItemOptionList").show();
					}
					$("#ModuleShopSellerItemEa").hide();
				} else {
					$("#ModuleShopSellerItemOption").hide();
					$("#ModuleShopSellerItemOptionList").hide();
					$("#ModuleShopSellerItemEa").show();
				}
			});
			
			$(document).triggerHandler("Shop.seller.item.post.init",[form]);
		},
		loadCategory:function(parent,categorys) {
			var form = $("form[name=ModuleShopSellerItemAddForm]");
			var input = parent == null ? form.find(".selectControl[data-field=category1]") : form.find(".selectControl[data-field=category"+(parent.depth + 1)+"]");
			var list = input.find("ul");
			
			list.empty();
			for (var i=0, loop=categorys.length;i<loop;i++) {
				var item = $("<li>").attr("data-value",categorys[i].idx).html(categorys[i].title);
				list.append(item);
			}
			
			if (categorys.length > 0) {
				input.find("button").attr("disabled",false);
			} else {
				input.find("button").attr("disabled",true);
			}
			
			input.find("> button").html(input.attr("data-default")+' <span class="arrow"></span>');
			
			iModule.initSelectControl(input);
			console.log("loadCategory",parent,categorys);
		},
		addImage:function(button) {
			var button = $(button);
			var form = button.parents("form");
			
			if (button.attr("data-type") == "default") {
				form.find("input[name=image]").attr("multiple",false);
			} else {
				form.find("input[name=image]").attr("multiple",true);
			}
			form.find("input[name=image]").attr("data-type",button.attr("data-type")).trigger("click");
		},
		uploadImage:function(file) {
			console.log(file);
			var form = $("form[name=ModuleShopSellerItemAddForm]");
			if (file == undefined) {
				var files = form.data("uploading");
				if (files.length == 0) {
					form.find("input[name=image]").val("");
					return;
				} else {
					Shop.seller.item.post.uploadImage(files.shift());
				}
			} else {
				$.ajax({
					url:ENV.getProcessUrl("shop","sellerItemImage"),
					method:"PUT",
					contentType:file.type,
					headers:{
						"IMAGE-META":JSON.stringify(file)
					},
					xhr:function() {
						var xhr = $.ajaxSettings.xhr();
						/*
						if (xhr.upload) {
							xhr.upload.addEventListener("progress",function(e) {
								if (e.lengthComputable) {
									$(document).triggerHandler("Attachment.progress",[id,{id:file.id,loaded:file.loaded + e.loaded,total:file.size}]);
									$(document).triggerHandler("Attachment.progressAll",[id,{loaded:Attachment.progress[id].loaded + file.loaded + e.loaded,total:Attachment.progress[id].total}]);
								}
							},false);
						}
						*/
		
						return xhr;
					},
					processData:false,
					data:file
				}).complete(function(xhr) {
					if (xhr.status == 200) {
						var result = xhr.responseJSON;
						
						if (result.success == true) {
							if (result.imageType == "default") {
								form.find(".itemImage button[data-type=default]").css("backgroundImage","url("+result.imageUrl+")");
								form.find("input[name=image_default]").val(result.imageIdx);
							} else {
								var preview = $("<button>").addClass("image").css("backgroundImage","url("+result.imageUrl+")").data("idx",result.imageIdx);
								form.find(".itemImage button[data-type=addition]").before(preview);
							}
							Shop.seller.item.post.uploadImage();
						}
					}
				})
			}
		},
		optionList:function() {
			var form = $("form[name=ModuleShopSellerItemAddForm]");
			
			var options = new Array();
			for (var i=1;i<=3;i++) {
				if (form.find("input[name=option"+i+"]").val().length > 0 || form.find("input[name=option"+i+"_select]").val().length > 0) {
					if (form.find("input[name=option"+i+"]").val().length == 0) {
						alert(form.find("input[name=option"+i+"]").attr("data-error"));
						return;
					}
					
					if (form.find("input[name=option"+i+"_select]").val().length == 0 || form.find("input[name=option"+i+"_select]").val().split(",") <= 1) {
						alert(form.find("input[name=option"+i+"_select]").attr("data-error"));
						return;
					}
					
					var selects = new Array();
					var temp = form.find("input[name=option"+i+"_select]").val().split(",");
					for (var j=0, loopj=temp.length;j<loopj;j++) {
						if ($.trim(temp[j]).length > 0 && $.inArray($.trim(temp[j]),selects) == -1) {
							selects.push($.trim(temp[j]));
						}
					}
					
					if (selects.length <= 1) {
						alert(form.find("input[name=option"+i+"_select]").attr("data-error"));
						return;
					}
					
					options.push({name:$.trim(form.find("input[name=option"+i+"]").val()),selects:selects});
				}
			}
			
			for (var i=options.length;i<3;i++) {
				options[i] = {name:null,selects:[null]};
			}
			
			var selects = new Array();
			for (var i=0, loop=options[0].selects.length;i<loop;i++) {
				for (var j=0, loopj=options[1].selects.length;j<loopj;j++) {
					for (var k=0, loopk=options[2].selects.length;k<loopk;k++) {
						selects.push({option1:options[0].selects[i],option2:options[1].selects[j],option3:options[2].selects[k]});
					}
				}
			}
			
			var list = $("#ModuleShopSellerItemOptionList");
			list.data("options",options);
			list.find(".list").empty();
			
			for (var i=0, loop=selects.length;i<loop;i++) {
				var optionName = selects[i].option1 + (selects[i].option2 !== null ? ' <i class="fa fa-angle-right"></i> '+selects[i].option2 : "") + (selects[i].option3 !== null ? ' <i class="fa fa-angle-right"></i> '+selects[i].option3 : "");
				var title = $("<label>").append($("<input>").attr("type","checkbox")).append(optionName);
				var price = $("<input>").attr("type","number").attr("name","option_price").addClass("inputControl").val("0");
				var ea = $("<input>").attr("type","number").attr("name","option_ea").addClass("inputControl").val("-1");
				
				var item = $("<div>").data("selects",selects[i]).addClass("row");
				item.append($("<div>").addClass("col-xs-6 inputInline").append(title));
				item.append($("<div>").addClass("col-xs-3 inputInline").append(price));
				item.append($("<div>").addClass("col-xs-3 inputInline").append(ea));
				
				list.find(".list").append(item);
			}
			
			if (selects.length > 0) {
				$("#ModuleShopSellerItemOptionList").show();
			} else {
				$("#ModuleShopSellerItemOptionList").hide();
			}
		},
		submit:function(form) {
			var form = $(form);
			
//			iModule.buttonStatus(form,"loading");
			iModule.inputStatus(form,"default");
			
			var wysiwygControl = form.find("textarea[data-wysiwyg=true]");
			for (var i=0, loop=wysiwygControl.length;i<loop;i++) {
				$(wysiwygControl[i]).redactor("code.showVisual");
				$(wysiwygControl[i]).redactor("code.sync");
			}
			
			if (form.find("input[name=option_enable]").is(":checked") == true) {
				var list = $("#ModuleShopSellerItemOptionList");
				var names = [];
				for (var i=0, loop=list.data("options").length;i<loop;i++) {
					if (list.data("options")[i].name === null) break;
					names[i] = list.data("options")[i].name;
				}
				var selects = [];
				var selectItems = $("#ModuleShopSellerItemOptionList .list .row");
				for (var i=0, loop=selectItems.length;i<loop;i++) {
					var selectItem = $(selectItems[i]);
					selects[i] = selectItem.data("selects");
					selects[i].ea = selectItem.find("input[name=option_ea]").val();
					selects[i].price = selectItem.find("input[name=option_price]").val();
				}
				var options = {names:names,selects:selects};
				form.find("input[name=options]").val(JSON.stringify(options));
			} else {
				form.find("input[name=options]").val("");
			}
			
			var imageAdditions = [];
			var imageItems = form.find(".itemImage button.image");
			for (var i=0, loop=imageItems.length;i<loop;i++) {
				imageAdditions[i] = $(imageItems[i]).data("idx");
			}
			form.find("input[name=image_addition]").val(JSON.stringify(imageAdditions));
			
			$.ajax({
				type:"POST",
				url:ENV.getProcessUrl("shop","sellerItemPost"),
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
}