<div class="defaultTitle">
	<i class="fa fa-server"></i> 서비스안내
</div>

<div class="defaultContent boxGray">
	단독서버를 <span class="fontRed">IDC에 입고시킨 후 미니톡 채팅서버 및 미니톡 클라이언트용 웹서버로 이용</span>할 수 있도록 모든 세팅을 갖춰드리며 서버자원이 허용하는 한도내에서 모든 기능을 자유롭게 사용할 수 있습니다.<br>
	단독서버 호스팅을 이용하시면 일반적인 웹사이트뿐만 아니라 클라이언트 설치가 불가능한 <span class="fontRed">서비스형 블로그</span>(티스토리, 워드프레스닷컴 등)나 <span class="fontRed">서비스형 웹사이트</span>(Wix.com 등)에서도 미니톡을 사용하실 수 있습니다.<br>
	사용중인 단독서버에 접속할 수 있는 도메인(http://userid.minitalk.kr)을 별도로 제공해드리기 때문에 웹사이트에 클라이언트를 삽입하거나, 미니톡 클라이언트 관리자에 접속하기위해 별도로 도메인을 설정할 필요가 없습니다.
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
							<li data-value="RENTAL">임대형서비스</li>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr class="split">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td class="label">대역폭</td>
			<td class="input">
				<input type="hidden" name="maxuser">
				<div class="inputBlock">
					<div class="selectControl" data-field="maxuser">
						<button type="button" disabled="disabled">대역폭 선택 <span class="arrow"></span></button>
						
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

<?php echo $Module->getServerList(); ?>

<div class="blankSpace"></div>

<div class="row">
	<div class="col-sm-6">
		<?php $IM->getWidget('forum/recently')->setTemplet('default')->setValue('type','post')->setValue('label',41)->setValue('count',10)->setValue('fid','minitalk')->setValue('titleIcon','<i class="fa fa-leanpub"></i>')->doLayout(); ?>
	</div>
	
	<div class="col-sm-6">
		<?php $IM->getWidget('qna/recently')->setTemplet('default')->setValue('type','question')->setValue('title','서비스 문의')->setValue('label',11)->setValue('count',10)->setValue('qid','minitalk')->setValue('titleIcon','<i class="fa fa-question"></i>')->doLayout(); ?>
	</div>
</div>


<script>$(document).ready(function() { Minitalk.server.init(); });</script>