<div class="defaultTitle">
	<i class="fa fa-heartbeat"></i> 후원안내
</div>

<div class="defaultContent boxGray">
	보내주신 후원금은 알쯔닷컴 서버 임차료 및 회선 사용료, 인건비 등으로 전액 사용되며 남거나 모자르는 경우 이월하거나 목표치를 상향 수정할 수 있습니다.<br>
	하단의 후원금 상태바 게이지는 월 고정지출을 기준으로 하여 <?php echo date('Y년 m월'); ?>의 후원금 달성률을 퍼센티지(%)로 표시하고 있습니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-line-chart"></i> 후원목표 달성률
</div>

<?php
$fullPrice = $this->fullPrice;
$prevMonth = date('Y-m',mktime(0,0,0,date('m')-1,1,date('Y')));
$thisMonth = date('Y-m');

$prevTotal = $this->getMonthTotal($prevMonth);
$thisTotal = $this->getMonthTotal($thisMonth) + ($prevTotal > $fullPrice ? $prevTotal - $fullPrice : 0);

$thisPercentage = $thisTotal / $fullPrice * 100 > 100 ? 100 : sprintf('%0.2f',$thisTotal / $fullPrice * 100);
$prevPercentage = $prevTotal / $fullPrice * 100 > 100 ? 100 : sprintf('%0.2f',$prevTotal / $fullPrice * 100);
?>
<div class="defaultContent boxGray">
	<div class="progress" data-position="top" data-type="<?php echo $thisPercentage > 50 ? 'right' : 'left'; ?>">
		<div class="text"><?php echo date('F, Y',strtotime($thisMonth.'-01')); ?></div>
		<div class="graph" style="width:<?php echo $thisPercentage; ?>%;"></div>
		<div class="percentage" style="<?php echo ($thisPercentage > 50 ? 'right' : 'left').':'.($thisPercentage > 50 ? 100 - $thisPercentage : $thisPercentage).'%'; ?>;"><?php echo $thisPercentage; ?>%</div>
	</div>
	
	<div class="progress" data-position="bottom" data-type="<?php echo $prevPercentage > 50 ? 'right' : 'left'; ?>">
		<div class="text"><?php echo date('F, Y',strtotime($prevMonth.'-01')); ?></div>
		<div class="graph" style="width:<?php echo $prevPercentage; ?>%;"></div>
		<div class="percentage" style="<?php echo ($prevPercentage > 50 ? 'right' : 'left').':'.($prevPercentage > 50 ? 100 - $prevPercentage : $prevPercentage).'%'; ?>;"><?php echo $prevPercentage; ?>%</div>
	</div>
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-gift"></i> 후원자 혜택
</div>

<div class="defaultContent boxGray">
	후원해주신 모든분들께는 <span class="fontRed">후원금액만큼의 포인트를 선물</span>로 드립니다. (1,000원 후원시 1,000 포인트 적립)<br>
	또한 <span class="fontRed">후원금액의 2% 만큼의 EXP를 상승</span>시켜드립니다. (1,000원 후원시 20EXP 상승)<br>
	포인트는 유료자료를 다운로드 받거나 알쯔닷컴에서 제공하는 각종 유료서비스를 신청하는데 사용할 수 있으며, EXP는 레벨상승에 필요한 경험치로, 모든 <span class="fontRed">유료자료 및 유료서비스는 레벨에 따라 할인(최대 25%)이 적용</span>됩니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-credit-card"></i> 후원방법
</div>

<div class="defaultContent boxGray">
	후원은 아래의 계좌를 통해서 받고 있습니다.<br><br>
	
	- 기업은행 094-064750-02-018 장진우<br>
	- 씨티은행 801-19690-263-01 장진우
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-file-text-o"></i> 후원자 혜택 신청
</div>

<div class="boxDefault fontRed warning" style="margin-top:0px;">
	<i class="fa fa-gift"></i> 후원을 하신 후 후원자 혜택을 받고자 하시는 분들은 회원로그인 후 아래의 양식을 입력하여 주시면 24시간 이내 처리됩니다.
</div>

<form id="ModuleDonationWriteForm" name="donation" onsubmit="return Donation.submit(this);">
	<table class="defaultForm">
	<tr>
		<td class="label">입금계좌</td>
		<td class="input">
			<div class="inputBlock">
				<input type="hidden" name="intype">
				<div class="selectControl" data-field="intype">
					<button type="button">입금하신 계좌를 선택하여 주십시오. <span class="arrow"></span></button>
					
					<ul>
						<li data-value="기업은행">기업은행 094-064750-02-018 장진우</li>
						<li data-value="씨티은행">씨티은행 801-19690-263-01 장진우</li>
					</ul>
				</div>
				<div class="helpBlock" data-error="입금하신 계좌를 선택하여 주십시오."></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label">입금자명</td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="name" class="inputControl">
				<div class="helpBlock" data-default="입금하신 성함을 입력하여 주십시오."></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label">입금금액</td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="price" class="inputControl">
				<div class="helpBlock" data-default="입금하신 금액을 숫자로만 입력하여 주십시오."></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label">비공개여부</td>
		<td class="input">
			<div class="inputBlock">
				<input type="hidden" name="is_secret">
				<div class="selectControl" data-field="is_secret" value="FALSE">
					<button type="button">후원내역 공개 <span class="arrow"></span></button>
					
					<ul>
						<li data-value="FALSE">후원내역 공개</li>
						<li data-value="TRUE">후원내역 비공개</li>
					</ul>
				</div>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="splitBottom">
		<td colspan="2"><div></div></td>
	</tr>
	</table>
	
	<button type="submit" class="btn btnRed" data-loading="신청중입니다..."><i class="fa fa-check"></i> 후원자 혜택 신청하기<span class="hidden-xs"> (후원에 감사드립니다.)</span></button>
</form>

<div class="blankSpace"></div>

<?php
$startDate = $this->getStartDate();
$totalPrice = $this->getTotal();
$monthAverage = round($totalPrice/ceil((time() - $startDate)/60/60/24/30));
?>
<div class="defaultTitle">
	<i class="fa fa-won"></i> 후원금액 <span id="ModuleDonationAverage">(<i class="fa fa-won"></i> <?php echo number_format($monthAverage); ?>/month)</span>
</div>
<div class="defaultContent boxGray center">
	<span class="gray"><?php echo date('Y년 m월 d일',$startDate); ?></span> 이래, <div class="visible-xs"></div> 총 <span id="ModuleDonationTotal" data-ready="FALSE" data-total="<?php echo $totalPrice; ?>">0</span>원을 후원하여 주셨습니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-bars"></i> 후원자 명단
</div>

<div id="ModuleDonationList"></div>


