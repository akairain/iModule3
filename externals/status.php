<style>
.serverList section {display:table; width:100%; table-layout:fixed; margin:15px 0px; background:#fff; border:1px solid #ddd; box-shadow:1px 1px 2px rgba(0,0,0,0.1); box-sizing:border-box;}
.serverList section aside {display:table-cell; width:100%; background-color:#000; background-image:url(/images/bg.jpg); background-size:cover; vertical-align:middle; position:relative;}
.serverList section aside div {width:100%; padding-bottom:100%; background-size:90%; background-position:50% 50%; background-repeat:no-repeat;}
.serverList section aside span {display:inline-block; position:absolute; right:10px; bottom:10px; color:#fff; padding:3px 8px; background:rgba(0,0,0,0.6); font-family:Roboto;}
.serverList section article {display:table-cell; width:540px; vertical-align:middle; padding:0px 20px; font-family:Roboto; box-sizing:border-box;}
.serverList section article h4 {font-size:24px; font-weight:400; color:#333; height:34px; line-height:34px; margin-top:15px;}
.serverList section article h4 span {font-weight:100; color:#999;}
.serverList section article h5 a {font-size:16px; font-weight:400; text-decoration:none; color:#e91b23;}

.serverList section article ul.detail {list-style:none; margin:15px 0px;}
.serverList section article ul.detail li {color:#999; line-height:20px;}
.serverList section article ul.detail li span {color:#666;}

.serverList .tabTitle {margin:0px;}
.serverList .tabContent {margin:0px; height:135px; margin-bottom:10px;}

@media (max-width:767px) {
	.serverList section {display:block;}
	.serverList section aside {display:block; width:100%;}
	.serverList section article {display:block; width:100%;}
	.serverList .tabContent {position:relative; width:100%; height:0; padding-bottom:27%; margin-bottom:20px;}
	.serverList .tabContent img {position:absolute; top:0px; left:0px; width:100%;}
}

@media (min-width:768px) and (max-width:1199px) {
	.serverList section article {padding:0px 10px; width:520px;}
	.serverList section article h4 {font-size:20px; height:26px; line-height:26px; margin-top:10px;}
	.serverList section article h5 a {font-size:14px;}
}
</style>

<script>
function GetServerData(server) {
	$.ajax({
		type:"POST",
		url:"//"+server+".moimz.com",
		dataType:"json",
		success:function(data) {
			for (var key in data) {
				$("section[data-server="+server+"] span[data-server-info="+key+"]").html(data[key]);
			}
		}
	});
}
</script>

<div class="defaultTitle">
	<i class="fa fa-quote-left"></i> 운영서버현황
</div>

<div class="defaultContent boxGray">
	알쯔닷컴 및 이하 서비스를 운영하기 위해 알쯔닷컴에서는 아래와 같은 서버를 운영하고 있습니다.<br>
	서버는 사용목적에 따라 내부 서비스가 독립적으로 구성되어 있으며, 상호 영향을 주지 않도록 네트워크구성도 독립적으로 구성되어 있습니다.
</div>

<div class="serverList">
	<?php
	$servers = array(
		'earth'=>array(
			'128.199.158.39',
			'WEB SERVER / DATABASE',
			'Digital Ocean In Singapore'
		),
		'moon'=>array(
			'128.199.242.217',
			'PROGRAM EXAMPLES',
			'Digital Ocean In Singapore'
		),
		'mars'=>array(
			'128.199.231.9',
			'SSL SERVER',
			'Digital Ocean In Singapore'
		),
		'pluto'=>array(
			'128.199.64.16',
			'MINITALK HOSTING #1',
			'Digital Ocean In Singapore'
		),
		'iss'=>array(
			'107.170.232.164',
			'PREVIOUS VERSION STORAGE',
			'Digital Ocean In San Francisco'
		),
		'blackhole'=>array(
			'128.199.124.225',
			'FOR DEVELOPERS',
			'Digital Ocean In San Francisco'
		)
	);
	foreach ($servers as $server=>$info) {
	?>
	<section data-server="<?php echo $server; ?>">
		<aside>
			<div style="background-image:url(/images/<?php echo $server; ?>.png);"></div>
			<span><?php echo $info[1]; ?></span>
		</aside>
		
		<article>
			<h4><span>SERVER.</span><?php echo strtoupper($server); ?></h4>
			<h5><a href="https://<?php echo $server; ?>.moimz.com" target="_blank">https://<?php echo $server; ?>.moimz.com</a></h5>
			
			<ul class="detail">
				<li>IP <span><?php echo $info[0]; ?></span>, Host By <?php echo $info[2]; ?>.</li>
				<li>Server Uptime <span data-server-info="uptime"><i class="fa fa-spin fa-spinner"></i></span>, Load Average <span data-server-info="load"><i class="fa fa-spin fa-spinner"></i></span>, Disk Usage <span data-server-info="used"><i class="fa fa-spin fa-spinner"></i></span> / <span data-server-info="total"><i class="fa fa-spin fa-spinner"></i></span> </li>
			</ul>
			
			<div class="tabTitle" role="tab" data-type="mouseover">
				<ul>
					<li data-toggle="<?php echo $server; ?>traffic" style="width:33.3%;" class="selected">Traffic</li>
					<li data-toggle="<?php echo $server; ?>cpu" style="width:33.4%;">CPU Usage</li>
					<li data-toggle="<?php echo $server; ?>memory" style="width:33.3%;">Memory Usage</li>
				</ul>
			</div>
			
			<div class="tabContent" role="tabpanel" data-toggle="<?php echo $server; ?>traffic">
				<img src="//<?php echo $server; ?>.moimz.com/mrtg/server.traffic-day.png" alt="traffic">
			</div>
			
			<div class="tabContent" role="tabpanel" data-toggle="<?php echo $server; ?>cpu" style="display:none;">
				<img src="//<?php echo $server; ?>.moimz.com/mrtg/server.cpu-day.png" alt="cpu">
			</div>
			
			<div class="tabContent" role="tabpanel" data-toggle="<?php echo $server; ?>memory" style="display:none;">
				<img src="//<?php echo $server; ?>.moimz.com/mrtg/server.memory-day.png" alt="memory">
			</div>
			
		</article>
	</section>
	<script>GetServerData("<?php echo $server; ?>");</script>
	<?php } ?>
</div>

<div class="ModuleMinitalk">
<?php echo $IM->getModule('minitalk')->getServerList(); ?>
</div>