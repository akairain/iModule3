var Shop = {
	getCategory:function(parent,callback) {
		$.ajax({
			type:"POST",
			url:ENV.getProcessUrl("shop","getCategory"),
			dataType:"json",
			data:{parent:parent},
			success:function(result) {
				console.log(result);
				if (result.success == true) {
					if (typeof callback == "function") callback(result.parent,result.categorys);
				}
			}
		});
	}
};