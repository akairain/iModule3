<?php
if (defined('__IM__') == false) exit;
define('__IM_FOOTER_INCLUDED__',true);
?>
	</div>
	
	<div class="footer">
		<div class="menu">
			<div class="container"></div>
		</div>
		
		<div class="copyright">
			<div class="container">
				<div class="logo">
					
				</div>
			</div>
		</div>
	</div>
</div>

<nav id="iModuleSlideMenu" class="sidemenu" role="navigation">
	<div>
		<div class="loginform">
			<?php $IM->getWidget('member/login')->setTemplet('@sidemenu')->doLayout(); ?>
		</div>
		
		<ul>
		<?php $menus = $IM->getMenus(); for ($i=0, $loop=count($menus);$i<$loop;$i++) { $pages = $IM->getPages($menus[$i]->menu); ?>
			<li class="menu<?php echo $IM->menu == $menus[$i]->menu ? ' opened' : ''; ?>">
				<a href="<?php echo $IM->getUrl($menus[$i]->menu,false); ?>">
					<?php if (count($pages) > 0) { ?><i class="fa fa-chevron-up"></i><i class="fa fa-chevron-down"></i><?php } ?>
					<i class="fa fa-plus"></i><i class="fa fa-minus"></i>
					&nbsp;&nbsp;<?php echo $menus[$i]->title; ?>
				</a>
				<?php if (count($pages) > 0) { ?>
				<ul>
					<?php for ($j=0, $loopj=count($pages);$j<$loopj;$j++) { ?>
					<li class="page"><a href="<?php echo $IM->getUrl($menus[$i]->menu,$pages[$j]->page,false); ?>"><i class="fa fa-caret-right"></i>&nbsp;&nbsp;<?php echo $pages[$j]->title; ?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
			</li>
		<?php } ?>
		</ul>
		<?php if ($_SERVER['HTTP_HOST'] == 'gura.so') { ?>
		<div style="padding:5px;">
			<ins class="adsbygoogle" style="display:inline-block;width:250px;height:250px" data-ad-client="ca-pub-3210736654114323" data-ad-slot="5270439723" data-override-format="true" data-page-url="http://<?php echo $_SERVER['HTTP_HOST']; ?>"></ins>
			<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
		</div>
		<?php } ?>
	</div>
</nav>

<script async=true src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-239651-15', 'auto');
ga('send', 'pageview');
</script>

</body>
</html>