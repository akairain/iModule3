var G = null;
$.fn.grid = function(options,values) {
	var $grid = $(this.get(0));
	
	if (typeof options == "object") {
		options.width = options.width ? options.width : "100%";
		options.height = options.height ? options.height : "auto";
		options.listeners = options.listeners ? options.listeners : {};
		
		$grid.addClass("grid").attr("data-grid","true").data("configs",options);
		$headerPanel = $("<div>").addClass("headerPanel");
		$bodyPanel = $("<div>").addClass("bodyPanel");
		
//		$headerPanel.append($("<div>").addClass("scrollWidthPanel").append($("<div>").addClass("tablePanel")));
		$headerPanel.append($("<div>").addClass("tablePanel"));
		$bodyPanel.append($("<div>").addClass("tablePanel")).append($("<div>").addClass("listEnd"));
		
		$grid.append($headerPanel).append($bodyPanel);
		
		var $header = $("<ul>").addClass("row");
		var minWidth = 0;
		for (var i=0, loop=options.columns.length;i<loop;i++) {
			
			var $column = $("<li>").addClass("col").data("colIndex",i);
			if (options.columns[i].flex == true) $column.width("100%");
			if (options.columns[i].width) $column.width(options.columns[i].width);
			if (options.columns[i].minWidth) $column.css("minWidth",options.columns[i].minWidth);
			if (options.columns[i].headerAlign) $column.css("textAlign",options.columns[i].headerAlign);
			
			if (options.columns[i].sortable === true) {
				$column.append($("<i>").addClass("fa fa-sort"));
				$column.attr("data-sortable","true");
			}
			$column.data("renderer",options.columns[i].renderer ? options.columns[i].renderer : null);
			$column.data("type",options.columns[i].type ? options.columns[i].type : "text");
			$column.append(options.columns[i].title);
			
			if (options.columns[i].width) minWidth+= options.columns[i].width;
			else if (options.columns[i].minWidth) minWidth+= options.columns[i].minWidth;
			
			if (options.columns[i].store) {
				$column.data("store",options.columns[i].store);
				(function(grid,column,configs,store) {
					$.ajax({
						type:"POST",
						url:store.url,
						data:store.params ? store.params : {},
						dataType:"json",
						success:function(result) {
							if (result.success == true) {
								if (store.listsField) result.lists = result[store.listsField];
								store.lists = result.lists;
							}
							
							if (grid.grid("getStore").lists && grid.grid("getStore").lists.length > 0) {
								for (var i=0, loop=grid.grid("getStore").lists.length;i<loop;i++) {
									var $dataColumn = grid.grid("getColumn",{rowIndex:i,colIndex:column.data("colIndex")});
									var value = grid.grid("getStore").lists[i][configs.dataIndex] !== undefined ? grid.grid("getStore").lists[i][configs.dataIndex] : null;
									$dataColumn.triggerHandler("change",[value,grid.grid("getStore").lists[i],i,column.data("colIndex"),grid]);
								}
							}
						}
					});
				})($grid,$column,options.columns[i],options.columns[i].store);
			}
			
			$header.append($column);
		}
//		$header.append($("<li>").addClass("colEnd"));
		$headerPanel.find(".tablePanel").append($header);
		$grid.data("minWidth",minWidth);
		if ($grid.width() < minWidth) {
//			$grid.find(".tablePanel, .scrollWidthPanel").width(minWidth);
//			$grid.find(".tablePanel").width(minWidth);
		}
		
		if (minWidth > 1100) {
			$grid.parents(".panelWrapper").css("maxWidth",minWidth);
		} else {
			$grid.parents(".panelWrapper").css("maxWidth",1100);
		}
		
		$bodyPanel.height(options.height);
		
		$grid.grid("resize");
		$grid.grid("load",{start:0,limit:30});
		
		$bodyPanel.on("scroll",function() {
			$(this).parents(".grid[data-grid=true]").find(".headerPanel").scrollLeft($(this).scrollLeft());
		});
	} else if (options == "load") {
		$grid.triggerHandler("beforeload",[]);
		
		var configs = $grid.data("configs");
		var $headerPanel = $grid.find(".headerPanel");
		var $bodyPanel = $grid.find(".bodyPanel");
		var $listPanel = $grid.find(".bodyPanel > .tablePanel");
		
		if (typeof configs.listeners.beforeload == "function") {
			configs.listeners.beforeload($grid);
		}
		
		$.ajax({
			type:"POST",
			url:configs.store.url,
			data:configs.store.params ? configs.store.params : {},
			dataType:"json",
			success:function(result) {
				if (result.success == true) {
					configs.store.lists = result.lists;
					for (var i=0, loop=result.lists.length;i<loop;i++) {
						$row = $("<ul>").addClass("row");
						for (var j=0, loopj=configs.columns.length;j<loopj;j++) {
							var $column = $("<li>").addClass("col").data("dataIndex",configs.columns[j].dataIndex);
//							$column.data("renderer",configs.columns[j].renderer ? configs.columns[j].renderer : null);
							$column.data("colIndex",j);
							if (configs.columns[j].flex == true) $column.width("100%");
							if (configs.columns[j].width) $column.width(configs.columns[j].width);
							if (configs.columns[j].minWidth) $column.css("minWidth",configs.columns[j].minWidth);
							if (configs.columns[j].align) $column.css("textAlign",configs.columns[j].align);
							
							if (configs.columns[j].width) minWidth+= configs.columns[j].width;
							else if (configs.columns[j].minWidth) minWidth+= configs.columns[j].minWidth;
							
							$column.on("change",function(col,value,record,rowIndex,colIndex,grid) {
								var $header = grid.grid("getHeader",{colIndex:colIndex});
								
								$(this).addClass($header.data("type"));
								var displayValue = value;
								if ($header.data("store") && $header.data("store").lists && $header.data("store").lists.length > 0) {
									for (var i=0, loop=$header.data("store").lists.length;i<loop;i++) {
										if (value == $header.data("store").lists[i][$header.data("store").valueField]) {
											displayValue = $header.data("store").lists[i][$header.data("store").displayField];
											break;
										}
									}
								}
								
								if (typeof $header.data("renderer") == "function") {
									var display = $header.data("renderer")(value,displayValue,record,rowIndex,colIndex);
									$(this).empty().append($("<div>").html(display));
								} else {
									$(this).empty().append($("<div>").html(displayValue));
								}
							});
							
							$row.append($column);
						}
						$row.data("rowIndex",i).data("record",result.lists[i]);
						$row.on("change",function() {
							$(this).find("li.col").each(function() {
								$row = $(this).parent();
								$grid = $row.parents(".grid[data-grid=true]");
								
var value = $row.data("record")[$(this).data("dataIndex")] !== undefined ? $row.data("record")[$(this).data("dataIndex")] : null;

								$(this).triggerHandler("change",[value,$row.data("record"),$row.data("rowIndex"),$(this).data("colIndex"),$grid]);
								/*
								if (typeof $(this).data("renderer") == "function") {
									var value = $row.data("record")[$(this).data("dataIndex")] !== undefined ? $row.data("record")[$(this).data("dataIndex")] : null;
									var display = $(this).data("renderer")(value,$row.data("record"),$row.data("rowIndex"),$(this).data("colIndex"));
									$(this).empty().append($("<div>").addClass("text").html(display));
								}
								*/
							});
						});
						
						$listPanel.append($row);
						$row.triggerHandler("change");
					}
					if ($bodyPanel.width() - $bodyPanel.find(".listEnd").width() > 0) {
					//	alert("yes");
						$headerPanel.find(".tablePanel").css("paddingRight",$bodyPanel.width() - $bodyPanel.find(".listEnd").width());
					}
				}
				
				$grid.grid("resize");
				
				if (typeof configs.listeners.load == "function") {
					configs.listeners.load($grid,configs.store);
				}
				/*
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
				*/
			},
			error:function() {
				iModule.alertMessage.show("Server Connect Error!");
			}
		});
	} else if (options == "getStore") {
		return $grid.data("configs").store;
	} else if (options == "getHeader") {
		$headerColumns = $grid.find(".headerPanel li");
		if (values.colIndex !== undefined) {
			return $($headerColumns[values.colIndex]);
		}
	} else if (options == "getColumn") {
		var $row = $($grid.find(".bodyPanel .row")[values.rowIndex]);
		if (values.colIndex !== undefined) {
			return $($row.find("li")[values.colIndex]);
		}
	} else if (options == "resize") {
		if ($(this).find(".bodyPanel").width() - $(this).find(".listEnd").width() > 0) {
//			$(this).find(".scrollWidthPanel").css("paddingRight",$(this).find(".bodyPanel").width() - $(this).find(".listEnd").width());
			$(this).find(".headerPanel .tablePanel").css("paddingRight",$(this).find(".bodyPanel").width() - $(this).find(".listEnd").width());
		}
		
		if ($(this).width() > $(this).data("minWidth")) {
			$(this).find(".tablePanel").css("boxSizing","border-box").css("width","100%");
//			$(this).find(".tablePanel").width("100%");
		} else {
//			$(this).find(".tablePanel, .scrollWidthPanel").width($(this).data("minWidth"));
			$(this).find(".tablePanel").width($(this).data("minWidth"));
		}
	}
};

$(window).on("resize",function() {
	$(".grid[data-grid=true]").each(function() {
		$(this).grid("resize");
	});
});