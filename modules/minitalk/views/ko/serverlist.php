<div class="defaultTitle">
	<i class="fa fa-bar-chart-o"></i> 미니톡 채팅호스팅 서비스 상태<span class="refresh" onclick="Minitalk.getServerList();"><i class="fa fa-refresh"></i></span>
</div>

<div class="defaultTable">
	<div class="fixedColumn" style="width:140px;">
		<table>
		<thead>
			<tr>
				<th><div>서버아이피:포트</div></th>
			</tr>
		</thead>
		
		<tbody>
			<?php for ($i=0, $loop=count($servers);$i<$loop;$i++) { ?>
			<tr>
				<td><div class="center"><?php echo $servers[$i]->ip; ?>:<?php echo $servers[$i]->port; ?></div></td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
	</div>
	
	<div class="scrollColumn">
		<div class="scrolling">
		<table style="min-width:520px;">
		<thead>
			<tr>
				<th style="width:80px;"><div>상태</div></th>
				<th style="width:80px;"><div>버전</div></th>
				<th style="width:80px;"><div>개설채널</div></th>
				<th style="width:80px;"><div>접속자</div></th>
				<th style="width:100%;"><div>업데이트 시각</div></th>
			</tr>
		</thead>
		<tbody>
			<?php for ($i=0, $loop=count($servers);$i<$loop;$i++) { ?>
			<tr>
				<td><div class="center"><?php if ($servers[$i]->status == 'ONLINE') { ?><?php echo $servers[$i]->user > 3000 ? '<span class="fontRed">혼잡</span>' : ($servers[$i]->user > 1000 ? '<span style="color:orange;">보통</span>' : '<span class="fontBlue">쾌적</span>'); ?><?php } else { ?><span style="color:#999;">오프라인</span><?php } ?></div></td>
				<td><div class="center">v<?php echo $servers[$i]->version; ?>.x</div></td>
				<td><div class="right"><?php echo number_format($servers[$i]->channel); ?></div></td>
				<td><div class="right"><?php echo number_format($servers[$i]->user); ?></div></td>
				<td><div class="center"><?php if ($servers[$i]->status == 'ONLINE') { ?><?php echo date('Y.m.d H:i:s',$servers[$i]->check_date); ?> <span class="fontRed">(<?php echo time() - $servers[$i]->check_date < 1 ? '방금전' : sprintf('%02d',time() - $servers[$i]->check_date).'초전'; ?>)</span><?php } else { ?><span class="fontRed">(서버가 오프라인입니다)</span><?php } ?></div></td>
			</tr>
			<?php } ?>
		</tbody>
		</table>
		</div>
	</div>
</div>
<div class="boxDefault"><i class="fa fa-info-circle"></i> 테이블의 전체내용이 아닌 일부내용만 보일경우, 테이블을 우측으로 스크롤하여 전체내용을 확인할 수 있습니다.</div>