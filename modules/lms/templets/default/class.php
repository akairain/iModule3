	<div class="contextTitle">
		<i class="fa fa-university"></i> <?php echo $Module->getLanguage('class/title'); ?>
	</div>
	
	<div class="viewPanel">
		<div class="itemBox">
			<div class="itemPanel">
				<div class="coverPanel">
					<div class="classImage" style="background-image:url(<?php echo $class->cover == null ? '' : $class->cover->path; ?>);">
						<div class="classTitle">
							<div class="title"><?php echo $class->title; ?></div>
							<div class="author"><?php echo $class->name; ?></div>
							<div class="detail"><i class="fa fa-user"></i> <?php echo number_format($class->student); ?> <i class="fa fa-book"></i> <?php echo number_format($class->subject); ?></div>
						</div>
					</div>
				</div>
				
				<div class="detailPanel">
					<div class="detail">
						<h4><?php echo $class->title; ?></h4>
						
						<div class="description"><?php echo $class->content; ?></div>
						
						<div class="splitLine"></div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('author'); ?></div>
							<div class="value"><?php echo $class->name; ?></div>
						</div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('class_type'); ?></div>
							<div class="value"><?php echo $Module->getLanguage('class_type_list/'.$class->type); ?></div>
						</div>
						
						<div class="tag">
							<div class="label"><?php echo $Module->getLanguage('class_attend'); ?></div>
							<div class="value"><?php echo $Module->getLanguage('class_attend_list/'.$class->attend); ?></div>
						</div>
						
						<div class="splitLine"></div>
						
						<div class="button">
							<button class="btn <?php echo $purchase == null ? 'btnRed' : 'btnBlue'; ?>" onclick="">
								<i class="fa fa-check"></i>
								강의 수강하기
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($Module->checkPermission('modify_class') == true || $class->midx == $this->IM->getModule('member')->getLogged()) { ?>
		<div class="actionButton">
			<!-- button type="button" class="btn btnRed" onclick="Dataroom.post.delete(<?php echo $post->idx; ?>);"><i class="fa fa-lock"></i> <?php echo $Module->getLanguage('button/lock'); ?></button -->
			<button type="button" class="btn btnBlue" onclick="Lms.subject.add(<?php echo $class->idx; ?>);"><i class="fa fa-book"></i> <?php echo $Module->getLanguage('button/add_subject'); ?></button>
			<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'create',$class->idx); ?>" class="btn btnWhite"><i class="fa fa-university"></i> <?php echo $Module->getLanguage('button/modify_class'); ?></a>
		</div>
		<?php } else { ?>
		<div class="blankSpace"></div>
		<?php } ?>
		
		<div class="contextTab">
			<ul class="detail">
				<li<?php echo $tab == 'subject' ? ' class="selected"' : ''; ?>><a href="<?php echo $this->IM->getUrl($IM->menu,$IM->page,'class',$class->idx); ?>?tab=subject"><?php echo $Module->getLanguage('subject'); ?> <span><?php echo number_format($class->subject); ?></span></a></li>
				<li<?php echo $tab == 'discuss' ? ' class="selected"' : ''; ?>><a href="<?php echo $this->IM->getUrl($IM->menu,$IM->page,'class',$class->idx); ?>?tab=discuss"><?php echo $Module->getLanguage('discuss'); ?> <span><?php echo number_format($class->subject); ?></span></a></li>
				<li<?php echo $tab == 'attendance' ? ' class="selected"' : ''; ?>><a href="<?php echo $this->IM->getUrl($IM->menu,$IM->page,'class',$class->idx); ?>?tab=attendance"><?php echo $Module->getLanguage('attendance'); ?> <span><?php echo number_format($class->student); ?></span></a></li>
				<li<?php echo $tab == 'mystatus' ? ' class="selected"' : ''; ?>><a href="<?php echo $this->IM->getUrl($IM->menu,$IM->page,'class',$class->idx); ?>?tab=mystatus"><?php echo $Module->getLanguage('mystatus'); ?></a></li>
			</ul>
		</div>
		
		<div class="tabPanel">
			<div class="postContext"><?php echo $tabContext; ?></div>
		</div>
	</div>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list').$IM->getQueryString(); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<?php if ($class->midx == $this->IM->getModule('member')->getLogged()) { ?>
			<button type="button" class="btn btnBlue" onclick="Lms.subject.add(<?php echo $class->idx; ?>);"><i class="fa fa-book"></i> <?php echo $Module->getLanguage('button/add_subject'); ?></button>
			<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'create',$class->idx); ?>" class="btn btnWhite"><i class="fa fa-university"></i> <?php echo $Module->getLanguage('button/modify_class'); ?></a>
			<?php } else { ?>
			<button class="btn <?php echo $purchase == null ? 'btnRed' : 'btnBlue'; ?>" onclick="">
				<i class="fa fa-check"></i>
				강의 수강하기
			</button>
			<?php } ?>
		</td>
	</tr>
	</table>
	
	<script>
	$(document).ready(function() {
		$(".contextTab li").on("click",function() {
			if ($(this).hasClass("selected") == false) {
				$(document).scrollTop($(".contextTab ul."+$(this).attr("data-tab")).offset().top - 50);
			}
		})
	});
	</script>