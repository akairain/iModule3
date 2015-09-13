<div class="defaultTable">
	<div class="fixedColumn" style="width:120px;">
		<table>
		<thead>
			<tr>
				<th><div>후원자명</div></th>
			</tr>
		</thead>
		
		<tbody>
			<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
			<tr>
				<td><div><?php echo $lists[$i]->name === false ? '비공개' : $lists[$i]->name; ?></div></td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
	</div>
	
	<div class="scrollColumn">
		<div class="scrolling">
		<table style="min-width:560px;">
		<thead>
			<tr>
				<th style="width:90px;"><div>후원방법</div></th>
				<th style="width:110px;"><div>후원금액</div></th>
				<th style="width:100px;"><div>후원일</div></th>
				<th style="width:180px;"><div>후원감사혜택</div></th>
				<th style="width:100%;"><div>상태</div></th>
			</tr>
		</thead>
		<tbody>
			<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>
			<tr>
				<td><div class="center"><?php echo $lists[$i]->intype; ?></div></td>
				<td><div class="right roboto price<?php echo floor(log10($lists[$i]->price)); ?>"><i class="fa fa-won"></i> <?php echo number_format($lists[$i]->price); ?></div></td>
				<td><div class="center roboto"><?php echo $lists[$i]->reg_date; ?></div></td>
				<td class="gift">
					<div class="point price<?php echo floor(log10($lists[$i]->gift_point)); ?>"><i class="fa fa-rub fontBlue"></i> <?php echo number_format($lists[$i]->gift_point); ?></div>
					<div class="point price<?php echo floor(log10($lists[$i]->gift_exp)); ?>"><span class="fontRed">EXP</span> <?php echo number_format($lists[$i]->gift_exp); ?></div>
				</td>
				<td<?php if ($this->IM->getModule('member')->getLogged() === 1) { ?> style="cursor:pointer;" onclick="Donation.show(<?php echo $lists[$i]->idx; ?>);"<?php } ?>>
					<div class="center">
						<?php if ($lists[$i]->status == 'WAIT') { ?>
						<span class="fontGray">입금확인중</span><span class="visible-lg-inline fontGray">(24시간내에 처리됩니다.)</span>
						<?php } elseif ($lists[$i]->status == 'TRUE') { ?>
						<span class="fontBlue">지급완료</span> <span class="visible-lg-inline fontGray">(후원에 감사드립니다)</span>
						<?php } else { ?>
						<span class="fontRed">확인불가</span> <span class="visible-lg-inline fontGray">(입금내역을 확인할 수 없습니다.)</span>
						<?php } ?>
					</div>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		</div>
	</div>
</div>
<div class="boxDefault"><i class="fa fa-info-circle"></i> 테이블의 전체내용이 아닌 일부내용만 보일경우, 테이블을 우측으로 스크롤하여 전체내용을 확인할 수 있습니다.</div>

<div class="center"><?php echo $pagination->html; ?></div>