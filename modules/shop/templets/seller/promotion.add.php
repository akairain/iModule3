<div class="formBox">
	<div class="inputBox">
		<div class="label">선택시간 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="hidden" name="date" value="<?php echo $date; ?>">
			<?php echo GetTime('Y-m-d H:i:s',$date); ?>
			
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">상품선택 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="hidden" name="item">
			<div class="selectControl" data-field="item">
				<button type="button">선택 <span class="arrow"></span></button>
				
				<ul>
					<?php for ($i=0, $loop=count($items);$i<$loop;$i++) { ?>
					<li data-value="<?php echo $items[$i]->idx; ?>"><?php echo $items[$i]->title; ?></li>
					<?php } ?>
				</ul>
			</div>
			<div class="helpBlock" data-default="판매승인이 완료된 상품을 선택하여 프로모션등록을 할 수 있습니다."></div>
		</div>
	</div>
	
	<div class="boxDefault">
		프로모션은 해당 상품을 설정된 할인율로 구매할 수 있는 쿠폰을 지급하는 시스템으로 쿠폰발급수량에 따라 설정된 최소할인율 부터 최대할인율이 적용된 쿠폰을 발급합니다.
	</div>
	
	<div class="inputBox">
		<div class="label">할인율 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="hidden" name="min" value="0">
			<input type="hidden" name="max" value="100">
			<div class="row">
				<div class="col-xs-6">
					<div class="selectControl" data-field="min">
						<button type="button">최소 0% <span class="arrow"></span></button>
						
						<ul>
							<li data-value="0">최소 0%</li>
						</ul>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="selectControl" data-field="max">
						<button type="button">최대 100% <span class="arrow"></span></button>
						
						<ul>
							<li data-value="100">최대 100%</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">쿠폰발급수량 <span class="required">*</span></div>
		<div class="inputBlock">
			<div class="row">
				<div class="col-sm-3 col-xs-4">
					<input type="text" name="ea" class="inputControl">
				</div>
				<div class="col-sm-9 col-xs-8" style="line-height:30px;">
					개<span style="float:right;" class="fontRed">(0%/개)</span>
				</div>
			</div>
			
			<div class="helpBlock" data-default="발급수량이 추가될 때마다 할인율이 최소할인율에서 최대할인율까지 균일하게 증가됩니다.<br>(예 : 발급수량 10개, 할인율이 0 ~ 100% 일때, 첫번째 쿠폰의 할인율은 0%, 두번째 쿠폰의 할인율은 10%, ... 10번째 쿠폰의 할인율은 100%)<br>"></div>
		</div>
	</div>
	
	<div class="boxDefault">
		할인율 설정은 두번째 프로모션부터 수정이 가능합니다. 회원님은 현재 프로모션을 처음등록하므로 기본할인율(0% ~ 100%)로 고정되어 수정할 수 없습니다.
	</div>
	
	<div class="splitLine"></div>
	
	<div class="inputBox">
		<div class="label">등록수수료 결제</div>
		
		<div class="inputBlock boxInfo">
			<input type="hidden" name="point">
			<div class="row">
				<div class="col-xs-6">
					<div class="label">등록수수료</div>
					<div class="inputInline">
						<span><b><?php echo number_format($price); ?>원</b></span>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="label">보유링크머니</div>
					<div class="inputInline">
						<span><?php echo number_format($myPoint); ?>원</span>
					</div>
				</div>
			</div>
			
			<div class="helpBlock" data-default="링크머니를 이용해 결제합니다. 링크머니가 부족할 경우 링크머니 충전메뉴에서 충전하실 수 있습니다."></div>
		</div>
	</div>
</div>