					<?php $post = $IM->getModule('dataroom')->getPost(2); ?>
					<a href="<?php echo $IM->getUrl('download','program',false); ?>" class="downloadLink">
						<i class="fa fa-download"></i>
						<div class="text">미니톡 클라이언트</div>
						<div class="version">Latest <b><?php echo $post->last_version; ?></b> <span class="reg_date"><?php echo GetTime('M d, Y',$post->last_update); ?></div>
					</a>
					
					<div style="height:5px; overflow:hidden;"></div>
					
					<?php $post = $IM->getModule('dataroom')->getPost(3); ?>
					<a href="<?php echo $IM->getUrl('download','program',false); ?>" class="downloadLink">
						<i class="fa fa-download"></i>
						<div class="text">미니톡 서버프로그램</div>
						<div class="version">Latest <b><?php echo $post->last_version; ?></b> <span class="reg_date"><?php echo GetTime('M d, Y',$post->last_update); ?></div>
					</a>
					
					<div class="blankSpace"></div>