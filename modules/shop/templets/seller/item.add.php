<div class="formBox">
	<div class="inputBox">
		<div class="label">상품명 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="text" name="title" class="inputControl">
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">기본설명</div>
		<div class="inputBlock">
			<input type="text" name="detail" class="inputControl">
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-6">
			<div class="inputBox">
				<div class="label">판매자 <span class="required">*</span></div>
				<div class="inputBlock">
					<input type="text" name="seller" class="inputControl">
					<div class="helpBlock"></div>
				</div>
			</div>
		</div>
		
		<div class="col-sm-6">
			<div class="inputBox">
				<div class="label">판매자 홈페이지</div>
				<div class="inputBlock">
					<input type="text" name="homepage" class="inputControl">
					<div class="helpBlock"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">카테고리 <span class="required">*</span></div>
		<div class="inputBlock">
			<div class="row">
				<div class="col-sm-4">
					<div class="selectControl" data-field="category1" data-default="대분류">
						<button type="button">대분류 <span class="arrow"></span></button>
						
						<ul></ul>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="selectControl" data-field="category2" data-default="중분류">
						<button type="button">중분류 <span class="arrow"></span></button>
						
						<ul></ul>
					</div>
				</div>
				
				<div class="col-sm-4">
					<div class="selectControl" data-field="category3" data-default="소분류">
						<button type="button">소분류 <span class="arrow"></span></button>
						
						<ul></ul>
					</div>
				</div>
			</div>
			
			<input type="text" name="category1" style="display:none;"><input type="text" name="category2" style="display:none;"><input type="text" name="category3" style="display:none;">
			
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-sm-4">
			<div class="inputBox">
				<div class="label">브랜드</div>
				<div class="inputBlock">
					<input type="text" name="brand" class="inputControl">
					<div class="helpBlock"></div>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="inputBox">
				<div class="label">제조자</div>
				<div class="inputBlock">
					<input type="text" name="maker" class="inputControl">
					<div class="helpBlock"></div>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="inputBox">
				<div class="label">모델명</div>
				<div class="inputBlock">
					<input type="text" name="model" class="inputControl">
					<div class="helpBlock"></div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">가격 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="number" name="price" class="inputControl">
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">미성년자 구매가능 여부</div>
		<div class="inputBlock">
			<div class="selectControl" data-field="allow_youth">
				<button type="button">선택 <span class="arrow"></span></button>
				
				<ul>
					<li data-value="TRUE">구매가능</li>
					<li data-value="FALSE">구매불가</li>
				</ul>
			</div>
			
			<input type="text" name="allow_youth" value="TRUE" style="display:none;">
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">상품이미지</div>
		
		<div class="itemImage">
			<button type="button" data-type="default" onclick="Shop.seller.item.post.addImage(this);"><span>기본이미지(필수)</span></button>
			<button type="button" data-type="addition" onclick="Shop.seller.item.post.addImage(this);">추가이미지</button>
		</div>
		
		<div class="inputBlock">
			<input type="hidden" name="image_default">
			<input type="hidden" name="image_addition">
			<input name="image" type="file" accept="image/*,image/png" style="display:none;">
			<div class="helpBlock" data-default="기본이미지는 필수로 1개의 이미지를 선택하여야 하며, 추가이미지는 한번에 여러장의 이미지를 선택하여 업로드할 수 있습니다.<br>이미지는 자동으로 600px X 600px 으로 리사이징 됩니다."></div>
		</div>
	</div>
	
	<div class="inputBox">
		<div class="label">상품상세소개</div>
		
		<div class="inputBlock">
			<?php $wysiwyg->doLayout(); ?>
			<div class="helpBlock"></div>
		</div>
	</div>
	
	<div class="splitLine"></div>
	
	<div class="inputBox">
		<div class="inputBlock">
			<label><input type="checkbox" name="option_enable" value="TRUE"> 상품옵션을 사용합니다.</label>
		</div>
	</div>
	
	<div id="ModuleShopSellerItemOption" class="inputBox">
		<div class="label">옵션</div>
		
		<div class="inputBlock">
			<div class="row">
				<div class="col-xs-4">
					<input type="text" name="option1" class="inputControl" placeholder="옵션명 #1" data-error="옵션명 #1을 입력하여 주십시오.">
				</div>
				<div class="col-xs-8">
					<input type="text" name="option1_select" class="inputControl" placeholder="옵션항목 #1 (콤마로 구분)" data-error="옵션항목 #1이 입력되지 않았거나, 콤마로 구분된 선택항목이 없습니다.">
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-4">
					<input type="text" name="option2" class="inputControl" placeholder="옵션명 #2" data-error="옵션명 #2을 입력하여 주십시오.">
				</div>
				<div class="col-xs-8">
					<input type="text" name="option2_select" class="inputControl" placeholder="옵션항목 #2 (콤마로 구분)" data-error="옵션항목 #2이 입력되지 않았거나, 콤마로 구분된 선택항목이 없습니다.">
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-4">
					<input type="text" name="option3" class="inputControl" placeholder="옵션명 #3" data-error="옵션명 #3을 입력하여 주십시오.">
				</div>
				<div class="col-xs-8">
					<input type="text" name="option3_select" class="inputControl" placeholder="옵션항목 #3 (콤마로 구분)" data-error="옵션항목 #3이 입력되지 않았거나, 콤마로 구분된 선택항목이 없습니다.">
				</div>
			</div>
			
			<div class="helpBlock" data-default="옵션명과 옵션항목을 입력 후 옵션목록 생성하기 버튼을 클릭하면, 해당옵션에 따라 옵션항목이 자동으로 생성되며 옵션별 추가금액 및 재고를 설정할 수 있습니다."></div>
			
			<button type="button" class="btn btnBlue" onclick="Shop.seller.item.post.optionList();">옵션목록 생성하기</button>
		</div>
	</div>
	
	<div id="ModuleShopSellerItemOptionList" class="inputBox">
		<div class="label">옵션목록</div>
		<div class="row">
			<div class="col-xs-6">
				선택항목
			</div>
			<div class="col-xs-3">
				추가가격
			</div>
			<div class="col-xs-3">
				재고
			</div>
		</div>
		
		<div class="list inputBlock">
			
		</div>
		
		<div class="inputBlock">
			<div class="helpBlock" data-default="재고에 -1 입력시 재고관리를 하지 않고 항상 구매가능상태로 설정됩니다.">재고에 -1 입력시 재고관리를 하지 않고 항상 구매가능상태로 설정됩니다.</div>
		</div>
		
		<button type="button" class="btn btnRed" onclick="Shop.seller.item.post.optionDelete();"><i class="fa fa-trash-o"></i> 선택옵션삭제</button>
	</div>
	
	<div class="inputBlock">
		<input name="options" type="hidden">
		<div class="helpBlock" data-default=""></div>
	</div>
	
	<div id="ModuleShopSellerItemEa" class="inputBox">
		<div class="label">재고 <span class="required">*</span></div>
		<div class="inputBlock">
			<input type="number" name="ea" value="-1" class="inputControl">
			<div class="helpBlock" data-default="재고에 -1 입력시 재고관리를 하지 않고 항상 구매가능상태로 설정됩니다."></div>
		</div>
	</div>
	
	<div class="splitLine"></div>
	
	<div class="inputBox">
		<div class="label">배송정보</div>
		<div class="inputBlock">
			<button type="button">택배사선택 <span class="arrow"></span></button>
			
			<ul>
				
			</ul>
		</div>
	</div>
</div>