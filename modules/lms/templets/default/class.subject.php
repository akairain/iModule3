<?php for ($i=0, $loop=count($lists);$i<$loop;$i++) { ?>

<div class="subjectBox">
	<div class="percent">
		<?php if ($attend != null) { ?>
		<div class="bar" style="width:<?php echo $lists[$i]->percent; ?>%;"></div>
		<div class="view"><?php echo $attend->mode == 'P' ? '수강율' : '진도율'; ?> : <?php echo sprintf('%0.2f',$lists[$i]->percent); ?>%</div>
		<?php } ?>
	</div>
	
	<h5>
		<i class="fa fa-file-text-o"></i> <?php echo $lists[$i]->title; ?>
		
		<div class="configs" data-subject-idx="<?php echo $lists[$i]->idx; ?>">
			<button type="button" onclick="Lms.subject.config(this);"><i class="fa fa-cog"></i></button>
			
			<ul class="menu" data-role="config">
				<li data-role="sort">
					순서변경 <i class="fa fa-caret-right"></i>
					
					<ul class="submenu" data-title="[{title}] 다음으로 이동">
						<li></li>
					</ul>
				</li>
				<li>강의주제수정</li>
				<li>강의주제삭제</li>
			</ul>
		</div>
	</h5>
	
	<div class="row">
		<?php foreach ($lists[$i]->posts as $post) { ?>
		<div class="col-sm-3 col-xs-6">
			<div class="post">
				<div class="preview <?php echo $post->type; ?>"<?php echo $post->image != null ? ' style="background-image:url('.$post->image.');"' : ''; ?> onclick="Lms.post.view(<?php echo $post->idx; ?>);">
					<?php if ($post->type == 'youtube' || $post->type == 'video') { ?><div class="video"><i class="fa fa-play"></i></div><?php } ?>
					
					<?php if ($attend != null) { ?>
					<div class="percent">
						<div class="bar" style="width:<?php echo $post->percent; ?>%;"></div>
						<div class="view"><?php echo $attend->mode == 'P' ? '수강율' : '진도율'; ?> : <?php echo sprintf('%0.2f',$post->percent); ?>%</div>
					</div>
					
					<?php if ($attend->mode == 'S' && $post->percent >= 90) { ?><div class="complete"><i class="fa fa-check"></i></div><?php } ?>
					<?php } ?>
				</div>
				
				<div class="title" onclick="Lms.post.view(<?php echo $post->idx; ?>);"><?php echo $post->title; ?></div>
				<div class="reg_date">
					<span class="time"><?php echo GetTime('Y-m-d H:i',$post->reg_date); ?></span>
					<div class="configs" data-post-idx="<?php echo $post->idx; ?>">
						<button type="button" onclick="Lms.post.config(this);"><i class="fa fa-cog"></i></button>
						
						<ul class="menu" data-role="config">
							<li data-role="sort">
								순서변경 <i class="fa fa-caret-right"></i>
								
								<ul class="submenu" data-title="[{title}] 다음으로 이동">
									<li></li>
								</ul>
							</li>
							<li data-role="move">
								다른 강의주제로 이동 <i class="fa fa-caret-right"></i>
								
								<ul class="submenu" data-title="[{title}] (으)로 이동">
									<li></li>
								</ul>
							</li>
							<li>학습자료수정</li>
							<li>학습자료삭제</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
	
	<div class="actionButton">
		<button type="button" class="btn btnBlue" onclick="Lms.item.add(<?php echo $lists[$i]->idx; ?>);"><i class="fa fa-cube"></i> 새 학습자료추가</button>
	</div>
</div>

<?php } ?>