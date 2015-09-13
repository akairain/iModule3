		<?php if ($IM->language == 'ko') { ?>
		<div class="row">
			<a href="<?php echo $IM->getUrl('coursemos',false); ?>" class="box box1 col-md-4 col-sm-6 col-xs-12">
				<div class="title">코스모스</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('service',false); ?>" class="box box2 col-md-4 col-sm-6 col-xs-12">
				<div class="title">서비스</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('clients',false); ?>" class="box box3 col-md-4 col-sm-6 col-xs-12">
				<div class="title">클라이언트</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('introduce',false); ?>" class="box box4 col-md-4 col-sm-6 col-xs-12">
				<div class="title">소개</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('news',false); ?>" class="box box5 col-md-4 col-sm-6 col-xs-12">
				<div class="title">소식</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
			<a href="http://blog.coursemos.kr" class="box box6 col-md-4 col-sm-6 col-xs-12" target="_blank">
				<div class="title">블로그</div>
				<div class="detail">자세히 보기 <span class="fa fa-plus"></span></div>
			</a>
		</div>
		<?php } else { ?>
		<div class="row">
			<a href="<?php echo $IM->getUrl('coursemos',false); ?>" class="box box1 col-md-4 col-sm-6 col-xs-12">
				<div class="title">COURSEMOS</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('service',false); ?>" class="box box2 col-md-4 col-sm-6 col-xs-12">
				<div class="title">SERVICE</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('clients',false); ?>" class="box box3 col-md-4 col-sm-6 col-xs-12">
				<div class="title">CLIENTS</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('introduce',false); ?>" class="box box4 col-md-4 col-sm-6 col-xs-12">
				<div class="title">ABOUT US</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
			<a href="<?php echo $IM->getUrl('news',false); ?>" class="box box5 col-md-4 col-sm-6 col-xs-12">
				<div class="title">NEWS</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
			<a href="http://blog.coursemos.kr" class="box box6 col-md-4 col-sm-6 col-xs-12" target="_blank">
				<div class="title">BLOG</div>
				<div class="detail">Show Details <span class="fa fa-plus"></span></div>
			</a>
		</div>
		<?php } ?>