<div class="defaultTitle">
	<i class="fa fa-plug"></i> 서비스안내
</div>

<div class="defaultContent boxGray">
	채팅호스팅서비스는 미니톡 클라이언트만 설치할 수 있는 회원님들을 위하여, <span class="fontRed">채팅서버를 제공해드리는 서비스</span>입니다.<br>
	채팅호스팅서비스를 신청하신 뒤 미니톡 관리자의 서버관리탭에서, 미니톡 클라이언트가 이용할 채팅서버를 간단히 설정할 수 있습니다.<br>
	미니톡 공식서버 서비스는 서버프로그램을 구동하지 못하는 중소 웹사이트를 대상으로 하는 서비스이며, 대규모 서비스구축을 원하시는분은 단독서버서비스나, 미니톡 서버프로그램을 구매 후 직접 서버를 운영하여 사용하시길 권장해 드립니다.<br><br>

	채팅호스팅서비스는 <span class="fontRed">무료서비스와 유료서비스로 구분</span>되어 있으며 무료서비스는 매 15일마다 미니톡 홈페이지에 접속하여 사용기간을 연장하여야 합니다.<br>
	유료서비스는 <span class="fontRed">사용기간 및 최대동접자수를 직접 설정</span>할 수 있으며, 설정에 따라 요금이 차등적용되며, <span class="fontRed">이용요금납부는 포인트를 통해 지불</span>합니다.
</div>

<div class="blankSpace"></div>

<form name="MinitalkServiceForm" onsubmit="return Minitalk.hosting.submit(this);">
<div class="row">
	<div class="col-sm-8">
		<div class="defaultTitle">
			<i class="fa fa-edit"></i> 서비스신청 및 기간연장
		</div>
		
		<table class="defaultForm">
		<tr>
			<td class="label">신청종류</td>
			<td class="input">
				<input type="hidden" name="type" value="NEW">
				<div class="inputBlock btnGroup" data-field="type">
					<button type="button" class="btn toggle selected" data-value="NEW"><i class="fa fa-check-square-o"></i> 신규신청</button><button type="button" class="btn toggle" data-value="EXTEND"><i class="fa fa-square"></i> 기간연장/신청내역변경</button>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr data-type="NEW">
			<td class="label">서비스<div class="visible-xs"></div>별칭</td>
			<td class="input">
				<div class="inputBlock">
					<input type="text" name="title" class="inputControl">
					<div class="helpBlock">차후 기간연장시 서비스파악이 쉽도록 별칭을 저장할 수 있습니다.<br>(예 : 우리사이트 유료서비스)</div>
				</div>
			</td>
		</tr>
		<tr data-type="EXTEND">
			<td class="label">서비스<div class="visible-xs"></div>선택</td>
			<td class="input">
				<input type="hidden" name="idx">
				<div class="inputBlock">
					<div class="selectControl" data-field="idx">
						<button type="button">서비스 선택 <span class="arrow"></span></button>
						
						<ul>
							<li></li>
						</ul>
					</div>
					<div class="helpBlock">신청내역을 변경하거나, 기간을 연장할 서비스를 선택하여 주십시오.<br>변경 또는 연장내역에 따라 추가결제가 필요할 수 있습니다.</div>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="label">서비스<div class="visible-xs"></div>종류</td>
			<td class="input">
				<input type="hidden" name="service">
				<div class="inputBlock">
					<div class="selectControl" data-field="service">
						<button type="button">서비스종류 선택 <span class="arrow"></span></button>
						
						<ul>
							<li data-value="BETA">베타서비스</li>
							<li data-value="FREE">무료서비스</li>
							<li data-value="PAID">유료서비스</li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="label">접속자수</td>
			<td class="input">
				<input type="hidden" name="maxuser">
				<div class="inputBlock">
					<div class="selectControl" data-field="maxuser">
						<button type="button" disabled="disabled">접속자수 선택 <span class="arrow"></span></button>
						
						<ul>
							<li></li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="label">신청기간</td>
			<td class="input">
				<input type="hidden" name="time">
				<div class="inputBlock">
					<div class="selectControl" data-field="time">
						<button type="button" disabled="disabled">신청기간 선택 <span class="arrow"></span></button>
						
						<ul>
							<li></li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="label">만료일</td>
			<td class="input">
				<input type="hidden" name="time">
				<div class="inputInline">
					<div class="helpBlock fontBlue" data-name="expire_date">신청기간을 선택하시면 예상만료일이 계산됩니다.</div>
				</div>
			</td>
		</tr>
		<tr class="splitBottom">
			<td colspan="2"><div></div></td>
		</tr>
		</table>
		
		<div class="boxDefault fontRed warning">
			<i class="fa fa-warning"></i> 채팅호스팅 서비스를 신청한다는 것은 서비스 이용약관에 동의함을 의미합니다. 서비스 이용약관은 이곳에서 확인하실 수 있습니다.<br>
			<i class="fa fa-info-circle"></i> 서비스 신청취소시 잔여기간에 대한 이용요금은 포인트로 환불되며, 기간연장시 연장시점 이후의 잔여기간 또한 포인트로 환급됩니다.
		</div>
	</div>
	
	<div class="col-sm-4">
		<div class="defaultTitle">
			<i class="fa fa-credit-card"></i> 청구서
		</div>
		
		<div class="bill">
			<div class="line">
				<div class="label">월이용요금(A)</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="monthly" class="price fontRed">0</span>
				</div>
			</div>
			
			<div class="line">
				<div class="label">신청기간(B)</div>
				<div class="price">
					<span><span data-name="time" class="fontRed">0</span>일(1개월=30일)</span>
				</div>
			</div>
			
			<div class="line">
				<div class="label">잔여기간환급(C)</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="refund" class="price fontBlue">0</span>
				</div>
			</div>
			
			<div class="boxDefault"><i class="fa fa-info-circle"></i> 잔여기간이란, 기간연장시 오늘이후 잔여이용요금을 일할계산한 환급금입니다</div>
			
			<div class="line totalLine">
				<div class="label">이용요금(A*B-C)</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="price" class="price fontRed">0</span>
				</div>
			</div>
			
			<div class="boxDefault"><i class="fa fa-info-circle"></i> 이용요금기준으로 할인이 적용되며, 금액이 마이너스일경우 포인트가 환급됩니다</div>
			
			<div class="line totalLine">
				<div class="label">기간할인</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="discount_time" class="price fontBlue">0</span>
				</div>
			</div>
			
			<div class="line">
				<div class="label">회원할인</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="discount_member" class="price fontBlue">0</span>
				</div>
			</div>
			
			<div class="boxDefault"><i class="fa fa-info-circle"></i> 회원할인은 회원레벨에 따라 최대 25%(LV.50) 할인되는 금액입니다</div>
			
			<div class="line totalLine">
				<div class="label">최종금액</div>
				<div class="price">
					<i class="fa fa-rub"></i>
					<span data-name="total" class="price fontRed">0</span>
				</div>
			</div>
		</div>
	</div>
</div>

<button type="submit" class="btn btnRed" data-loading="신청중입니다..."><i class="fa fa-pencil-square-o"></i> 서비스 신청하기 / 기간연장하기</button>

</form>

<div class="blankSpace"></div>

<?php echo $Module->getMyHosting(); ?>

<div class="blankSpace"></div>

<?php echo $Module->getServerList(); ?>

<div class="blankSpace"></div>

<div class="row">
	<div class="col-sm-6">
		<?php $IM->getWidget('forum/recently')->setTemplet('default')->setValue('type','post')->setValue('label',41)->setValue('count',10)->setValue('fid','minitalk')->setValue('titleIcon','<i class="fa fa-leanpub"></i>')->doLayout(); ?>
	</div>
	
	<div class="col-sm-6">
		<?php $IM->getWidget('qna/recently')->setTemplet('default')->setValue('title','서비스 문의')->setValue('label',11)->setValue('count',10)->setValue('qid','minitalk')->setValue('titleIcon','<i class="fa fa-question"></i>')->doLayout(); ?>
	</div>
</div>


<script>$(document).ready(function() { Minitalk.hosting.init(); });</script>