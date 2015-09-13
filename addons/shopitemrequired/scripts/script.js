$(document).on("Shop.seller.item.post.init",function(target,form) {
	var contentBox = form.find("textarea[name=content]").parents(".inputBox");
	
	var inputBox = $("<div>").addClass("inputBox");
	inputBox.append($("<div>").addClass("label").html("상품요약정보"));
	
	var inputBlock = $("<div>").addClass("inputBlock");
	inputBlock.append($("<input>").attr("type","hidden").attr("name","required_selection"));
	var selectControl = $("<div>").addClass("selectControl").attr("data-field","required_selection");
	selectControl.append($("<button>").attr("type","button").html('선택 <span class="arrow"></span>'));
	var list = $("<ul>");
	
	for (key in _SHOP_REQUIRED_CATEGORYS) {
		list.append($("<li>").attr("data-value",key).html(_SHOP_REQUIRED_CATEGORYS[key]));
	}
	selectControl.append(list);
	inputBlock.append(selectControl);
	
	inputBlock.append($("<div>").addClass("helpBlock").attr("data-default","전자상거래 등에서의 상품 등의 정보제공에 관한 고시에 따라 총 35개 상품군에 대해 상품 특성 등을 양식에 따라 입력합니다.").html("전자상거래 등에서의 상품 등의 정보제공에 관한 고시에 따라 총 35개 상품군에 대해 상품 특성 등을 양식에 따라 입력합니다."));
	
	inputBox.append(inputBlock);
	
	contentBox.before($("<div>").addClass("splitLine"));
	contentBox.before(inputBox);
	contentBox.before($("<div>").attr("data-required-input","true"));
	
	iModule.initSelectControl(selectControl);
	
	form.find("input[name=required_selection]").on("change",function() {
		form.find("div[data-required-input=true]").empty();
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("shop","getRequiredFields"),
			dataType:"json",
			data:{category:$(this).val()},
			success:function(result) {
				if (result.success == true) {
					for (var i=0, loop=result.fields.length;i<loop;i++) {
						var inputBox = $("<div>").addClass("inputBox");
						inputBox.append($("<div>").addClass("label").html(result.fields[i].title));
						
						var inputBlock = $("<div>").addClass("inputBlock");
						inputBlock.append($("<input>").attr("type","text").attr("name","required_"+result.fields[i].name).addClass("inputControl").val(result.fields[i].value));
						inputBlock.append($("<div>").addClass("helpBlock").attr("data-default",result.fields[i].help).html(result.fields[i].help));
						
						inputBox.append(inputBlock);
						
						form.find("div[data-required-input=true]").append(inputBox);
					}
				}
			}
		});
	});
});