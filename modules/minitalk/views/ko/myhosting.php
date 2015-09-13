<div class="defaultTitle">
	<i class="fa fa-bars"></i> 나의 서비스 관리
</div>

<?php if (count($services) > 0) { ?>
<div class="defaultTable">
	<div class="fixedColumn" style="width:120px;">
		<table>
		<thead>
			<tr>
				<th>
					<div>별칭</div>
				</th>
			</tr>
		</thead>
		
		<tbody>
			<?php foreach ($services as $service) { ?>
			<tr>
				<td><div class="left" style="height:91px;"><?php echo $service->title; ?></div></td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
	</div>
	
	<div class="scrollColumn">
		<div class="scrolling">
		<table style="min-width:550px;">
		<thead>
			<tr>
				<th style="width:100%;"><div>클라이언트ID</div></th>
				<th style="width:100px;"><div>만료일</div></th>
				<th style="width:100px;"><div>상태</div></th>
				<th style="width:100px;"><div>설정</div></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($services as $service) { ?>
			<tr>
				<td><div class="center"><?php echo $service->client_id; ?></div></td>
				<td><div class="center"><?php echo GetTime('Y-m-d',$service->expire_date); ?></div></td>
				<td><div class="center"><?php echo $service->expire_date < time() ? '<span class="fontRed">만료됨</span>' : ($service->server_id != '' ? '<span class="fontBlue">연결됨</span>' : '연결대기'); ?></div></td>
				<td rowspan="2">
					<div class="center">
						<button type="button" class="btn btnBlue" onclick="Minitalk.hosting.extend(<?php echo $service->idx; ?>);"><i class="fa fa-calendar-o"></i> 기간연장</button>
					</div>
					<div class="center" style="margin-top:5px;">
						<button type="button" class="btn btnRed" onclick="Minitalk.hosting.disconnect(<?php echo $service->idx; ?>);"><i class="fa <?php echo $service->server_id == '' ? 'fa-trash' : 'fa-unlink'; ?>"></i> <?php echo $service->server_id == '' ? '삭제하기' : '연결해제'; ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<div class="progress">
						<div class="graph">
							<div class="bar">
								<div class="percentage" style="width:<?php echo $service->expire_date < time() ? '100%' : ((time() - $service->reg_date)/($service->expire_date - $service->reg_date)*100).'%'; ?>;"></div>
							</div>
						</div>
						
						<div class="text">
							<?php echo $service->expire_date < time() ? '<span class="fontRed">만료됨</span>' : '<span class="fontBlue">'.round(($service->expire_date - time()) / 60 / 60 / 24).'일 남음</span>'; ?> / <?php echo floor(($service->expire_date - $service->reg_date) / 60 / 60 / 24); ?>일 연장신청
						</div>
					</div>
					
					<div class="progress">
						<div class="graph">
							<div class="bar">
								<div class="percentage" style="width:<?php echo $service->maxuser < $service->user ? '100%' : ($service->user / $service->maxuser * 100).'%'; ?>;"></div>
							</div>
						</div>
						
						<div class="text">
							<span class="fontRed"><?php echo number_format($service->user); ?></span>명 접속중 / 최대 <?php echo number_format($service->maxuser); ?>명
						</div>
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

<?php } else { ?>

<div style="height:150px; background:#f4f4f4; text-align:center; line-height:150px;">
	사용중인 서비스내역이 없습니다.
</div>

<?php } ?>