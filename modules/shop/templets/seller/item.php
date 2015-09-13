<?php $IM->addSiteHeader('script',__IM_DIR__.'/scripts/grid.js'); ?>
<div class="row">
	<div class="col-sm-4">
		<div class="widgetBox">
			
		</div>
	</div>
	
	<div class="col-sm-4">
		
	</div>
	
	<div class="col-sm-4">
		
	</div>
</div>

<div class="blankSpace"></div>

<div id="ModuleShopSellerItemList" class="gridBox"></div>


<script>
$("#ModuleShopSellerItemList").grid({
	store:{
		url:ENV.getProcessUrl("shop","sellerItem")
	},
	columns:[{
		title:"상품명",
		dataIndex:"title",
		minWidth:200,
		flex:true,
		renderer:function(value,displayValue,record) {
			return '<img src="'+record.image+'" style="float:left; width:28px; height:28px; margin:3px 5px 0px -2px; vertical-align:middle;">'+value;
		}
	},{
		title:"대분류",
		dataIndex:"category1",
		width:100,
		store:{
			url:ENV.getProcessUrl("shop","getCategory"),
			params:{depth:1},
			listsField:"categorys",
			valueField:"idx",
			displayField:"title"
		}
	},{
		title:"중분류",
		dataIndex:"category2",
		width:100,
		store:{
			url:ENV.getProcessUrl("shop","getCategory"),
			params:{depth:2},
			listsField:"categorys",
			valueField:"idx",
			displayField:"title"
		},
		renderer:function(value,displayValue) {
			if (value == 0) return "선택안함";
			else return displayValue;
		}
	},{
		title:"소분류",
		dataIndex:"category3",
		width:100,
		store:{
			url:ENV.getProcessUrl("shop","getCategory"),
			params:{depth:3},
			listsField:"categorys",
			valueField:"idx",
			displayField:"title"
		},
		renderer:function(value,displayValue) {
			if (value == 0) return "선택안함";
			else return displayValue;
		}
	},{
		title:"가격",
		dataIndex:"price",
		width:100,
		sortable:true,
		align:"right",
		renderer:function(value) {
			return iModule.getNumberFormat(value);
		}
	},{
		title:"승인여부",
		dataIndex:"status",
		width:100,
		align:"center",
		type:"button",
		renderer:function(value,displayValue,record) {
			if (value == "REGISTER") return '<button type="button" onclick="Shop.seller.item.status('+record.idx+',\'WAIT\');" class="button">판매신청</button>';
			else if (value == "ACTIVE") return '<button type="button" class="primary" disabled>승인완료</button>';
			else if (value == "DEACTIVE") return '<button type="button" class="danger" disabled>승인불가</button>';
			else if (value == "WAIT") return '<button type="button" class="primary" disabled>승인대기</button>';
			return value;
		}
	}]
});
</script>

<button onclick="Shop.seller.item.add();">상품등록</button>