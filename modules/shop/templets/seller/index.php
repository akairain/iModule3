<div class="wrapper">
	<aside class="sidePanel">
		<div class="profile">
			<div class="image"><img src="<?php echo $member->photo; ?>"></div>
			<div class="info">
				<div class="nickname"><?php echo $member->nickname; ?></div>
				<div class="link">
					<a href="<?php echo $IM->getUrl($IM->getModule('member')->getMemberPage('modify')->menu,$IM->getModule('member')->getMemberPage('modify')->page,false); ?>"><?php echo $IM->getModule('member')->getLanguage('modify/title'); ?></a>
					<button type="button" data-loading="<?php echo $IM->getModule('member')->getLanguage('login/logout_loading'); ?>" onclick="Member.logout(this);" class="logout"><?php echo $IM->getModule('member')->getLanguage('login/logout'); ?></button>
				</div>
			</div>
		</div>
		
		<ul>
		<?php
		for ($i=0, $loop=count($views);$i<$loop;$i++) { ?>
			<li<?php echo $IM->view == $views[$i] ? ' class="selected"' : ''; ?>>
				<a href="<?php echo $IM->getUrl(null,null,$views[$i],false); ?>"><i class="fa <?php echo $icons[$views[$i]]; ?>"></i> <?php echo $Module->getLanguage('seller/'.$views[$i].'/title'); ?></a>
				<i class="fa fa-angle-right"></i>
			</li>
		<?php } ?>
		</ul>
	</aside>
	
	<section class="viewPanel">
		<div class="nbreadcrumb">
			<h4><i class="fa <?php echo $icons[$IM->view]; ?>"></i> <?php echo $Module->getLanguage('seller/'.$IM->view.'/title'); ?></h4>
		</div>
		
		<div class="panelWrapper">
			<?php echo $viewPanel; ?>
		</div>
	</section>
</div>