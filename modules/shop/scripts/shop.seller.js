Shop.seller = {
	modal:{
		init:function(formName) {
			if (formName == "ModuleShopSellerItemAddForm") Shop.seller.item.post.init(formName);
			else if (formName == "ModuleShopSellerPromotionAddForm") Shop.seller.promotion.post.init(formName);
		},
		submit:function(form) {
			var formName = $(form).attr("name");
			if (formName == "ModuleShopSellerItemAddForm") return Shop.seller.item.post.submit(form);
			else if (formName == "ModuleShopSellerPromotionAddForm") return Shop.seller.promotion.post.submit(form);
		}
	}
};