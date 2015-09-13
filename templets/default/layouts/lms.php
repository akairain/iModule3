	<div class="nbreadcrumb">
		<div class="container">
			<h3>
			<?php
			$titleIcon = array('board'=>'file-text-o','dataroom'=>'floppy-o','apidocument'=>'book');
			if ($IM->page == null) {
				$pageInfo = $IM->getMenus($IM->menu);
				if (isset($pageInfo->context->icon) == true && preg_match('/^fa\-/',$pageInfo->context->icon) == true) {
					$icon = $pageInfo->context->icon;
				} else {
					$icon = 'fa-file-o';
				}
			} else {
				$pageInfo = $IM->getPages($IM->menu,$IM->page);
				if (isset($pageInfo->context->icon) == true && preg_match('/^fa\-/',$pageInfo->context->icon) == true) {
					$icon = $pageInfo->context->icon;
				} else {
					if ($pageInfo->type == 'module') {
						$icon = empty($titleIcon[$pageInfo->context->module]) == true ? 'fa-file-o' : 'fa-'.$titleIcon[$pageInfo->context->module];
					} else {
						$icon = 'fa-file-o';
					}
				}
			}
			echo '<i class="fa '.$icon.'"></i> '.$pageInfo->title;
			?>
			</h3>
			
			<ol>
				<li><a href="<?php echo __IM_DIR__.'/'; ?>"><i class="fa fa-home"></i></a></li>
				<?php if ($IM->menu != null) { ?>
				<li><i class="fa fa-angle-right"></i></li>
				<li<?php echo $IM->page == null ? ' class="current"' : ''; ?>><a href="<?php echo $IM->getUrl(null,false); ?>"><?php echo $IM->getMenus($IM->menu)->title; ?></a></li>
				<?php if ($IM->page != null) { ?>
				<li><i class="fa fa-angle-right"></i></li>
				<li class="current"><a href="<?php echo $IM->getUrl(null,null,false); ?>"><?php echo $IM->getPages($IM->menu,$IM->page)->title; ?></a></li>
				<?php } } ?>
			</ol>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<?php echo $context; ?>
			</div>
			
			<div class="col-md-3 hidden-sm hidden-xs">
				<?php $IM->getWidget('member/login')->setTemplet('@sidebar')->doLayout(); ?>
				<div class="blankSpace"></div>
				<?php
				if ($pageInfo->type == 'module' && $pageInfo->context->module == 'lms' && $IM->view == 'view') {
					$idx = Request('idx');
					$post = $IM->getModule('lms')->getPost($idx);
					$IM->getWidget('lms/subjectlist')->setTemplet('default')->setValue('class',$post->class)->doLayout();
				} else {
					$IM->getWidget('pagelist')->setTemplet('@sidemenu')->setValue('menu',$IM->menu)->doLayout();
				}
				?>
				<div class="blankSpace"></div>
				
				<?php
				if ($IM->getPages('index','notice') !== null && $IM->getPages('index','notice')->type == 'module' && $IM->getPages('index','notice')->context->module == 'board') {
					$notice = $IM->getWidget('board/recently')->setTemplet('@sidelist')->setValue('type','post')->setValue('bid',$IM->getPages('index','notice')->context->context)->setValue('titleIcon','<i class="fa fa-bell"></i>')->setValue('count',2);
					if ($IM->getPages('index','notice')->context->config != null && $IM->getPages('index','notice')->context->config->category) {
						$notice->setValue('category',$IM->getPages('index','notice')->context->config->category);
					}
					$notice->doLayout();
					echo '<div class="blankSpace"></div>';
				}
				?>
			</div>
		</div>
	</div>