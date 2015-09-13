<style>
.communityRecently {display:table; width:100%; table-layout:fixed;}
.communityRecently > div {display:table-cell; width:100%; vertical-align:top; padding-right:15px;}
.communityRecently > aside {display:table-cell; width:250px; vertical-align:top;}

@media (max-width:767px) {
	.communityRecently {display:block; width:100%;}
	.communityRecently > div {display:block; width:100%; margin-bottom:15px; padding-right:0px;}
	.communityRecently > aside {display:none; width:100%;}
}
</style>
<div class="communityRecently">
	<div>
		<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('bid','freeboard')->setValue('titleIcon','<i class="fa fa-comment-o"></i>')->doLayout(); ?>
		
		<div class="blankSpace"></div>
		
		<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',8)->setValue('bid','humor')->setValue('titleIcon','<i class="fa fa-smile-o"></i>')->doLayout(); ?>
		
		<div class="blankSpace"></div>
		
		<?php $IM->getWidget('forum/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('fid','tip')->setValue('titleIcon','<i class="fa fa-lightbulb-o"></i>')->doLayout(); ?>
		
		<div class="blankSpace"></div>
		
		<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('bid','promotion')->setValue('titleIcon','<i class="fa fa-microphone"></i>')->doLayout(); ?>
	</div>
	
	<aside>
		<?php $IM->getWidget('member/recently')->setTemplet('default')->setValue('count',16)->doLayout(); ?>
		
		<div style="margin-top:10px; height:250px;">
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<ins class="adsbygoogle" style="display:inline-block;width:250px;height:250px" data-ad-client="ca-pub-3210736654114323" data-ad-slot="5270439723"></ins>
			<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
		</div>
		
	</aside>
</div>

<?php $IM->getWidget('board/recently')->setTemplet('gallery')->setValue('type','post')->setValue('count',4)->setValue('bid','photo')->setValue('titleIcon','<i class="fa fa-camera-retro"></i>')->doLayout(); ?>