<?php
function makeTracking($time) {
	$tracking = '';
	for ($i=0;$i<$time;$i++) {
		$tracking.= rand(0,9);
	}
	
	return $tracking;
}
?>
	<div class="viewBox">
		<h5><i class="fa fa-bar-chart"></i> 학습통계보기</h5>
		
		<div id="ModuleLmsPlayingStatus">
			<div>
				<div class="item" data-tracking="<?php echo makeTracking($context->time); ?>">
					<div class="icon hit">
						<i class="fa fa-eye"></i>
						<div class="name">시청횟수</div>
					</div>
					<div class="detail">
						<div class="description">전체 학습자의 동영상 구간별 시청횟수를 확인할 수 있습니다.</div>
						<div class="graph"></div>
					</div>
				</div>
			</div>
			
			<div>
				<div class="item" data-tracking="<?php echo makeTracking($context->time); ?>">
					<div class="icon pause">
						<i class="fa fa-pause"></i>
						<div class="name">일시정지</div>
					</div>
					<div class="detail">
						<div class="description">전체 학습자의 동영상 구간별 일시정지 횟수를 확인할 수 있습니다. (너무 빠르게 강의하거나 필기할 내용이 많은 구간이었나요?)</div>
						<div class="graph"></div>
					</div>
				</div>
			</div>
			
			<div>
				<div class="item" data-tracking="<?php echo makeTracking($context->time); ?>">
					<div class="icon forward">
						<i class="fa fa-fast-forward"></i>
						<div class="name">빨리감기</div>
					</div>
					<div class="detail">
						<div class="description">구간별 빨리감기(30초 이내)를 한 횟수를 확인할 수 있습니다. (내용이 너무 지루하지 않았나요?)</div>
						<div class="graph"></div>
					</div>
				</div>
			</div>
			
			<div>
				<div class="item" data-tracking="<?php echo makeTracking($context->time); ?>">
					<div class="icon backward">
						<i class="fa fa-fast-backward"></i>
						<div class="name">되감기</div>
					</div>
					<div class="detail">
						<div class="description">구간별 되돌려감기(30초 이내)를 한 횟수를 확인할 수 있습니다. (진행이 너무 빨랐거나, 설명이 난해하지는 않았나요?)</div>
						<div class="graph"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div id="ModuleLmsMentStatus" class="viewBox">
		<h5><i class="fa fa-tasks"></i> 댓글통계 보기</h5>
		
		<div class="item">
			<div class="graph"></div>
		</div>
	</div>
	
	<div class="viewBox">
		<h5><i class="fa fa-tasks"></i> 수강상태 보기</h5>
		
		<div id="ModuleLmsTrackingList" class="row">
			<?php foreach ($students as $student) { ?>
			<div class="col-xs-12 col-sm-6">
				<div class="item <?php echo $post->type; ?>" data-tracking="<?php echo $student->tracking; ?>">
					<div class="photo" style="background-image:url(<?php echo $student->user->photo; ?>);">
						<i class="fa fa-<?php echo $student->percent > 90 ? 'check' : ($student->percent == 0 ? 'times' : 'arrow-right'); ?>"></i>
						<div class="percent"><?php echo $student->percent; ?>%</div>
					</div>
					
					<div class="detail">
						<div class="name">
							<?php if ($student->update_date > 0) { ?><span class="reg_date"><?php echo GetTime('Y.m.d H:i:s',$student->update_date); ?></span><?php } ?>
							<?php echo $this->IM->getModule('member')->getMemberNickname($student->midx,false); ?>
						</div>
						<div class="graph">
						
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	
	<script>
	Lms.ment.getStatus(<?php echo $post->idx; ?>);
	</script>