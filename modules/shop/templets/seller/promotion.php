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

<div class="blankSpace"></div>
<div class="gridWrapper">
	<aside class="gridFixedSide">
		으앙;;<br>
		으앙2<br>
		으앙3<br>
	</aside>
	
	<div class="gridPanel">
		<div id="ModuleShopSellerPromotionList" class="gridBox"></div>
	</div>
</div>

<script>
function cellRenderer(value,displayValue,record,rowIndex,colIndex) {
	var temp = value.split("@");
	var sHTML = "";
	var buttonText = "";
	sHTML+= '<button type="button"';
	if (temp[0] == "EMPTY") {
		sHTML+= ' onclick="Shop.seller.promotion.add(\''+record["day"+colIndex+"_time"]+'\');"';
		buttonText = "등록하기";
	} else {
		buttonText = "등록불가";
	}
	
	if (temp[0] == "EMPTY") {
		if (temp[1] == "0") {
			sHTML+= ' class="button"';
		} else {
			sHTML+= ' class="primary"';
		}
	} else {
		sHTML+= ' class="danger"';
	}
	
	if (temp[1] != "0") buttonText+= "("+temp[1]+")";
	
	if (temp[0] == "FULL") sHTML+= ' disabled';
	sHTML+= '>';
	sHTML+= buttonText;
	sHTML+= '</button>';
	return sHTML;
}

$("#ModuleShopSellerPromotionList").grid({
	store:{
		url:ENV.getProcessUrl("shop","sellerPromotion"),
		params:{}
	},
	columns:[{
		title:"시간",
		dataIndex:"start_time",
		headerAlign:"center",
		align:"center",
		width:90,
		renderer:function(value,displayValue,record) {
			var localDay = moment(record.start_date).format("DDD");
			var serverDay = moment.unix(value).format("DDD");
			
			var sHTML = "";
			if (serverDay != localDay) {
				sHTML+= "D"+(serverDay - localDay > 0 ? "+" : "");
				sHTML+= serverDay - localDay;
				sHTML+= ", ";
			}
			sHTML+= moment.unix(value).format("HH:mm");
			return sHTML;
		}
	},{
		title:"날짜1",
		dataIndex:"day1",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day2",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day3",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day4",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day5",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day6",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	},{
		title:"날짜1",
		dataIndex:"day7",
		headerAlign:"center",
		minWidth:90,
		flex:true,
		type:"button",
		renderer:cellRenderer
	}],
	listeners:{
		beforeload:function(grid) {
			grid.grid("getHeader",{colIndex:1}).html("Loading...");
			grid.grid("getHeader",{colIndex:2}).html("Loading...");
			grid.grid("getHeader",{colIndex:3}).html("Loading...");
			grid.grid("getHeader",{colIndex:4}).html("Loading...");
			grid.grid("getHeader",{colIndex:5}).html("Loading...");
			grid.grid("getHeader",{colIndex:6}).html("Loading...");
			grid.grid("getHeader",{colIndex:7}).html("Loading...");
		},
		load:function(grid,store) {
			grid.grid("getHeader",{colIndex:1}).html(store.lists[0].day1_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:2}).html(store.lists[0].day2_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:3}).html(store.lists[0].day3_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:4}).html(store.lists[0].day4_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:5}).html(store.lists[0].day5_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:6}).html(store.lists[0].day6_date.split(" ").shift());
			grid.grid("getHeader",{colIndex:7}).html(store.lists[0].day7_date.split(" ").shift());
		}
	}
});
</script>

<button onclick="Shop.seller.item.add();">상품등록</button>