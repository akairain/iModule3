	<div class="contextTitle">
		<i class="fa fa-university"></i> <?php echo $class->title; ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td class="label">수강상태</td>
		<td class="input">
			<div class="inputBlock">
				<div class="helpBlock">수강전</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	</table>
	
	<div class="viewBox">
		<h5><i class="fa fa-file-text-o"></i> <?php echo $post->title; ?></h5>
		
		<?php echo $typeContext; ?>
	</div>
	
	<?php echo $attendContext; ?>
	
	<div id="ModuleLmsViewBar">
		<div id="ModuleLmsProgressBar" class="progressBar">
			<div id="ModuleLmsPostionPin"></div>
			<div id="ModuleLmsPositionBar"></div>
			<div id="ModuleLmsTrackingBar"></div>
			<div id="ModuleLmsStatusBar"></div>
		</div>
		
		<div id="ModuleLmsInfoBar">
			<button type="button" class="statusToggle on" onclick="Lms.post.toggleStatus('<?php echo $post->type; ?>');"><i class="fa fa-bar-chart"></i> 학습통계보기</button>
		</div>
		<div id="ModuleLmsActionBar">
			<div class="mentWrite">
				<div class="mentInput">
					<div class="photo"><img src="<?php echo $IM->getModule('member')->getMember()->photo; ?>"></div>
					
					<div class="button">
						<button type="button" class="btn btnRed"><i class="fa fa-smile-o"></i></button>
						<button type="button" class="btn btnWhite"><i class="fa fa-frown-o"></i></button>
						<button type="button" class="btn btnWhite"><i class="fa fa-question"></i></button>
						<button type="button" class="btn btnWhite"><i class="fa fa-thumbs-o-up"></i></button>
						<button type="button" class="btn btnWhite"><i class="fa fa-thumbs-o-down"></i></button>
					</div>
					
					<div class="textInput"><input id="ModuleLmsMentInput" type="text" class="inputControl" data-idx="<?php echo $post->idx; ?>" placeholder="댓글입력..."></div>
					
					<div class="actionButton">
						<button type="button" class="btn btnWhite"><i class="fa fa-comments-o"></i> 전체댓글보기</button>
						<button type="button" class="btn btnWhite"><i class="fa fa-arrow-right"></i> 다음강의보기</button>
						<button type="button" class="btn btnWhite"><i class="fa fa-university"></i> 강좌보기</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<script>
	var idx = "<?php echo $post->idx; ?>";
	var type = "<?php echo $post->type; ?>";
	
	$(document).ready(function() {
		Lms.ment.getMent(idx);
		
		$("#ModuleLmsActionBar .mentInput .button button").on("click",function() {
			$(this).parent().find("button").removeClass("btnRed btnWhite").addClass("btnWhite");
			$(this).removeClass("btnWhite").addClass("btnRed");
		});
		
		$("#ModuleLmsProgressBar").on("mousedown",function(event) {
			$("#ModuleLmsProgressBar").data("isDrag",true);
			var percent = (event.clientX - $("#ModuleLmsProgressBar").offset().left) / $("#ModuleLmsProgressBar").width() * 100;
			$("#ModuleLmsProgressBar").data("percent",percent);
			$("#ModuleLmsPostionPin").css("left",percent+"%");
			$("#ModuleLmsPositionBar").css("width",percent+"%");
			
			event.stopPropagation();
			event.preventDefault();
		});
		
		$(document).on("mousemove",function(event) {
			if ($("#ModuleLmsProgressBar").data("isDrag") == true) {
				var percent = (event.clientX - $("#ModuleLmsProgressBar").offset().left) / $("#ModuleLmsProgressBar").width() * 100;
				percent = percent < 0 ? 0 : (percent > 100 ? 100 : percent);
				$("#ModuleLmsProgressBar").data("percent",percent);
				$("#ModuleLmsPostionPin").css("left",percent+"%");
				$("#ModuleLmsPositionBar").css("width",percent+"%");
				
				event.stopPropagation();
				event.preventDefault();
			}
		});
		
		$(document).on("mouseup",function() {
			if ($("#ModuleLmsProgressBar").data("isDrag") == true) {
				$("#ModuleLmsProgressBar").data("isDrag",false);
				var percent = $("#ModuleLmsProgressBar").data("percent");
				if (typeof Lms[type] == "object" && typeof Lms[type].setPosition == "function") {
					Lms[type].setPosition(percent);
				}
			}
		});
		/*
		$("#ModuleLmsMentInput").on("focus",function(event) {
			if (typeof Lms[type] == "object" && typeof Lms[type].getPosition == "function") {
				$(this).data("position",Lms[type].getPosition());
			} else {
				$(this).data("position",0);
			}
		});
		
		$("#ModuleLmsMentInput").on("focus",function(event) {
			if (typeof Lms[type] == "object" && typeof Lms[type].getPosition == "function") {
				$(this).data("position",Lms[type].getPosition());
			} else {
				$(this).data("position",0);
			}
		});
		*/
		
		$("#ModuleLmsMentInput").on("keydown",function(event) {
			if ($(this).val().length == 0) {
				if (typeof Lms[type] == "object" && typeof Lms[type].getPosition == "function") {
					$(this).data("position",Lms[type].getPosition());
				} else {
					$(this).data("position",0);
				}
			}
			
			if (event.keyCode == 13) {
				Lms.ment.submit();
				event.preventDefault();
			}
		});
	});
	
	</script>