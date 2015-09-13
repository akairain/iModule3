<?php
$team = $IM->db('default','coursemos_')->select('team_table')->orderBy('position_code','asc')->get();
$history = $IM->db('default','coursemos_')->select('history_table')->orderBy('year','asc')->get();
$partnership = $IM->db('default','coursemos_')->select('partnership_table')->orderBy('idx','asc')->get();
?>
		<?php if ($IM->language == 'ko') { ?>
		<div id="coursemos"></div>
		
		<section class="column">
			<h2>코스모스팀</h2>
			
			<article>
				<h3>진지함과 유쾌함, 열정과 노력으로 하나의 가치를 공유하는 아주 다른 사람들이 모여 코스모스를 만들고 있습니다.</h3>
				
				<p>
					톡톡 튀는 개성들이 신기할 정도로 즐거운 조화를 이루는 곳.이러닝 생태계의 상생과 열린 교육의 가치를 공유하고 나눔의 철학에 공감하는 사람들, 팀 코스모스입니다.
				</p>
			</artcle>
		</section>
		
		<section class="block">
			<ul class="team">
				<?php for ($i=0, $loop=count($team);$i<$loop;$i++) { ?>
				<li class="item">
					<article>
						<div class="profile" style="background-image:url(<?php echo __IM_DIR__.'/templets/coursemos/images/team/'.$team[$i]->idx.'.jpg'; ?>);"></div>
						<div class="box">
							<div class="name"><?php echo $team[$i]->name_en; ?></div>
							<div class="line"></div>
							<div class="role"><?php echo $team[$i]->nickname; ?></div>
							<div class="korname"><span><?php echo $team[$i]->name; ?></span></div>
							<div class="email"><?php echo $team[$i]->email ? '<a href="mailto:'.$team[$i]->email.'">'.$team[$i]->email.'</a>' : ''; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
			</ul>
		</section>
		
		<div class="splitLine"></div>
		
		<section class="block history">
			<h2>관찰일기 <small>코스모스 씨앗부터 꽃이 피기까지</small></h2>
			
			<div class="tree">
				<div class="left">
					<ul>
						<?php for ($i=1, $loop=count($history);$i<$loop;$i=$i+2) { ?>
						<li>
							<article>
								<i></i>
								<h3><?php echo $history[$i]->year; ?></h3>
								<div class="line"></div>
								<h4><?php echo $history[$i]->title; ?></h4>
								
								<p><?php echo nl2br($history[$i]->content); ?></p>
							</article>
						</li>
						<?php } ?>
					</ul>
				</div>
				<div class="right">
					<ul>
						<?php for ($i=0, $loop=count($history);$i<$loop;$i++) { ?>
						<li<?php echo $i % 2 == 1 ? ' class="visible-xs"' : ''; ?>>
							<article>
								<i></i>
								<h3><?php echo $history[$i]->year; ?></h3>
								<div class="line"></div>
								<h4><?php echo $history[$i]->title; ?></h4>
								
								<p><?php echo nl2br($history[$i]->content); ?></p>
							</article>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</section>
		
		<div class="splitLine"><div id="partnership"></div></div>
		
		<section class="column">
			<h2>파트너쉽</h2>
			
			<article>
				<h3>코스모스의 비전과 가치 공유를 통해 IT생태계 조성에 함께하는 파트너를 소개합니다.</h3>
				
				<p>
					오픈소스 Moodle 기반의 학습플랫폼 코스모스는 협력업체를 비롯하여 전세계 무들 사용자가 함께 만들어 가고 있습니다. 코스모스는 상생하는 이러닝 생태계를 지향합니다.
				</p>
			</artcle>
		</section>
		
		<section class="block partnerContainer">
			<ul class="partner">
				<?php for ($i=0, $loop=count($partnership);$i<$loop;$i++) { ?>
				<li class="item">
					<article>
						<div class="logo" style="background-image:url(<?php echo __IM_DIR__.'/templets/coursemos/images/partnership/'.$partnership[$i]->idx.'.png'; ?>);"></div>
						<div class="box">
							<div class="name"><?php echo $partnership[$i]->name; ?></div>
							<div class="line"></div>
							<div class="role"><?php echo $partnership[$i]->role; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
				<?php for ($i=$loop%4;$i<4;$i++) { ?>
				<li class="item">
					<article>
						<div class="logo"></div>
					</article>
				</li>
				<?php } ?>
			</ul>
		</section>
		
		<div class="splitLine"><div id="ubion"></div></div>
		
		<section class="column">
			<h2>UBION</h2>
			
			<article>
				<h3>㈜유비온은 교육솔루션, 컨텐츠, 서비스사업 중심의 유비쿼터스 기반 평생교육 기업입니다.</h3>
				
				<p>
					플랫폼에서 콘텐츠까지 이러닝 전반에 걸쳐 업계를 선도하고 있으며, 국내 최초로 오픈 소스 무들을 활용한 한국형 학습플랫폼 브랜드 코스모스를 런칭하였습니다.<br>
					국내는 물론이고 해외로도 적극적으로 사업영역을 확장하고 있습니다.
				</p>
			</article>
		</section>
		
		<section class="block">
			<div class="ubion">
				<canvas id="canvas"></canvas>
				
				<div class="center"></div>
				
				<div class="circle solution">
					<article>
						<div class="box">
							<div><i class="fa fa-lightbulb-o"></i><h4>솔루션 사업</h4></div>
						</div>
						
						<div class="detailBox">
							학습플랫폼서비스<br>
							- 코스모스<br>
							- 스마트 플래시
						</div>
					</article>
				</div>
				<div class="circle hrd">
					<article>
						<div class="box">
							<div><i class="fa fa-building-o"></i><h4>기업교육</h4></div>
						</div>
						
						<div class="detailBox">
							인터넷원격훈련(이러닝)<br>
							- 우편원격훈련<br>
							- 근로자직무능력향상<br>
							- NH농협지식채움+
						</div>
					</article>
				</div>
				<div class="circle publish">
					<article>
						<div class="box">
							<div><i class="fa fa-book"></i><h4>도서출판</h4></div>
						</div>
						
						<div class="detailBox">
							자격수험서<br>
							경제/경영 도서
						</div>
					</article>
				</div>
				<div class="circle e-learning">
					<article>
						<div class="box">
							<div><i class="fa fa-desktop"></i><h4>온라인 교육</h4></div>
						</div>
						
						<div class="detailBox">
							온라인 교육 브랜드 사이트 운영<br>
							- 와우패스(금융)<br>
							- 랜드스쿨(부동산)<br>
							- 고시닷컴(고시)<br>
							- 원격평생교육원(학점은행제)
						</div>
					</article>
				</div>
				<div class="circle academy">
					<article>
						<div class="box">
							<div><i class="fa fa-graduation-cap"></i><h4>교육센터<br>/학원</h4></div>
						</div>
						
						<div class="detailBox">
							금융교육전문 와우패스센터 운영
						</div>
					</article>
				</div>
				<div class="circle research">
					<article>
						<div class="box">
							<div><i class="fa fa-flask"></i><h4>연구소</h4></div>
						</div>
						
						<div class="detailBox">
							교육공학, 금융, 취업 연구소 운영
						</div>
					</article>
				</div>
			</div>
		</section>
		<?php } else { ?>
		<div id="coursemos"></div>
		
		<section class="column">
			<h2>Team COURSEMOS</h2>
			
			<article>
				<h3>Every member of the Team COURSEMOS who has distinct personality gathered to bloom COURSEMOS with sharing the one value.</h3>
				
				<p>
					People who share value of coexistence in e-learning and open education and practice sharing.
				</p>
			</artcle>
		</section>
		
		<section class="block">
			<ul class="team">
				<?php for ($i=0, $loop=count($team);$i<$loop;$i++) { ?>
				<li class="item">
					<article>
						<div class="profile" style="background-image:url(<?php echo __IM_DIR__.'/templets/coursemos/images/team/'.$team[$i]->idx.'.jpg'; ?>);"></div>
						<div class="box">
							<div class="name"><?php echo $team[$i]->name_en; ?></div>
							<div class="line"></div>
							<div class="role"></div>
							<div class="korname"></div>
							<div class="email"><?php echo $team[$i]->email ? '<a href="mailto:'.$team[$i]->email.'">'.$team[$i]->email.'</a>' : ''; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
			</ul>
		</section>
		
		<div class="splitLine"></div>
		
		<section class="block history">
			<h2>Observation diary on COURSEMOS</h2>
			
			<div class="tree">
				<div class="left">
					<ul>
						<?php for ($i=1, $loop=count($history);$i<$loop;$i=$i+2) { ?>
						<li>
							<article>
								<i></i>
								<h3><?php echo $history[$i]->year; ?></h3>
								<div class="line"></div>
								<h4><?php echo $history[$i]->title_en; ?></h4>
								
								<p><?php echo nl2br($history[$i]->content_en); ?></p>
							</article>
						</li>
						<?php } ?>
					</ul>
				</div>
				<div class="right">
					<ul>
						<?php for ($i=0, $loop=count($history);$i<$loop;$i++) { ?>
						<li<?php echo $i % 2 == 1 ? ' class="visible-xs"' : ''; ?>>
							<article>
								<i></i>
								<h3><?php echo $history[$i]->year; ?></h3>
								<div class="line"></div>
								<h4><?php echo $history[$i]->title_en; ?></h4>
								
								<p><?php echo nl2br($history[$i]->content_en); ?></p>
							</article>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</section>
		
		<div class="splitLine"><div id="partnership"></div></div>
		
		<section class="column">
			<h2>Partnership</h2>
			
			<article>
				<h3>We share the vision and value of COURSEMOS with our partners to make our ecosystem fertile!</h3>
				
				<p>
					COURSEMOS aims to be the learning land of abundance, by cooperation with those valuable partners as well as numerous Moodle users around the world.
				</p>
			</artcle>
		</section>
		
		<section class="block partnerContainer">
			<ul class="partner">
				<?php for ($i=0, $loop=count($partnership);$i<$loop;$i++) { ?>
				<li class="item">
					<article>
						<div class="logo" style="background-image:url(<?php echo __IM_DIR__.'/templets/coursemos/images/partnership/'.$partnership[$i]->idx.'.png'; ?>);"></div>
						<div class="box">
							<div class="name"><?php echo $partnership[$i]->name_en; ?></div>
							<div class="line"></div>
							<div class="role"><?php echo $partnership[$i]->role_en; ?></div>
						</div>
					</article>
				</li>
				<?php } ?>
				<?php for ($i=$loop%4;$i<4;$i++) { ?>
				<li class="item">
					<article>
						<div class="logo"></div>
					</article>
				</li>
				<?php } ?>
			</ul>
		</section>
		
		<div class="splitLine"><div id="ubion"></div></div>
		
		<section class="column">
			<h2>UBION</h2>
			
			<article>
				<h3>Ubion Co., Ltd. Is the life-long education company focused on educational solutions, contents and services for ubiquitous learning.</h3>
				
				<p>
					Ubion is leading the e-learning field in Korea, known as its platforms and contents, and is the first company which released the localized learning platform brand in Korea, COURSEMOS, based on the open-source software, Moodle. With the advanced technology, Ubion is expanding the field to the global market.
				</p>
			</article>
		</section>
		
		<section class="block">
			<div class="ubion">
				<canvas id="canvas"></canvas>
				
				<div class="center"></div>
				
				<div class="circle solution">
					<article>
						<div class="box">
							<div><i class="fa fa-lightbulb-o"></i><h4>Solution Business</h4></div>
						</div>
						
						<div class="detailBox">
							Learning platform service<br>
							- COURSEMOS<br>
							- Smart Flash
						</div>
					</article>
				</div>
				<div class="circle hrd">
					<article>
						<div class="box">
							<div><i class="fa fa-building-o"></i><h4>Corporate Education</h4></div>
						</div>
						
						<div class="detailBox">
							Internet remote training (e-learning)<br>
							- Mail remote training<br>
							- Job performance improvement<br>
							- Training for employees (NH Nonghyup)
						</div>
					</article>
				</div>
				<div class="circle publish">
					<article>
						<div class="box">
							<div><i class="fa fa-book"></i><h4>Publication</h4></div>
						</div>
						
						<div class="detailBox">
							Book for test preparation<br>
							Books on economics & business management
						</div>
					</article>
				</div>
				<div class="circle e-learning">
					<article>
						<div class="box">
							<div><i class="fa fa-desktop"></i><h4>Online Education</h4></div>
						</div>
						
						<div class="detailBox">
							Operate learning brands in online<br>
							- Wowpass(Finance)<br>
							- Land school (Real estate)<br>
							- Gosi.com (National official exam)<br>
							- Ubion Remote Lifelong Education (Academic credit)
						</div>
					</article>
				</div>
				<div class="circle academy">
					<article>
						<div class="box">
							<div><i class="fa fa-graduation-cap"></i><h4>Education Center</h4></div>
						</div>
						
						<div class="detailBox">
							Operate Wowpass Financial Training Center
						</div>
					</article>
				</div>
				<div class="circle research">
					<article>
						<div class="box">
							<div><i class="fa fa-flask"></i><h4>Consulting Center</h4></div>
						</div>
						
						<div class="detailBox">
							Operate labs on educational technology/finance/employment
						</div>
					</article>
				</div>
			</div>
		</section>
		<?php } ?>
		<script>
		$(".team article, .partner article, .ubion .circle").on("touchstart",function() {
			$(this).trigger("hover");
		});
		
		function drawUbion() {
			var canvas = $("canvas").get(0);
			canvas.width = $("canvas").width();
			canvas.height = $("canvas").height();
			
			var center = {top:$(".ubion .center").position().top + $(".ubion .center").outerHeight() / 2,left:$(".ubion .center").position().left + $(".ubion .center").outerWidth() / 2};
			$(".ubion .circle").each(function() {
				console.log($(this));
				console.log($(this).position());
				
				var circle = {top:$(this).position().top + $(this).outerHeight() / 2,left:$(this).position().left + $(this).outerWidth() / 2};
				
				console.log(circle,center);
				
				var context = canvas.getContext("2d");
				context.strokeStyle = "#e0e0e0";
				context.lineWidth = 2;
				context.lineCap = "bevel";
				context.lineJoin = "round";
	
				context.beginPath();
				context.moveTo(circle.left,circle.top);
				context.lineTo(center.left,center.top);
				context.stroke();
			});
		}
		
		drawUbion();
		
		$(window).on("resize",function() {
			drawUbion();
		});
		</script>