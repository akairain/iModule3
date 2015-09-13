<?php if ($IM->page == 'history') { ?>

<div class="tabTitle" role="tab">
	<ul>
		<li data-toggle="client" style="width:33.3%;" class="selected">미니톡 클라이언트</li>
		<li data-toggle="server" style="width:33.4%;">서버프로그램</li>
		<li data-toggle="old" style="width:33.3%;">미니톡 구버전</li>
	</ul>
</div>

<div class="tabContent" role="tabpanel" data-toggle="client">
	<?php $IM->getWidget('dataroom/history')->setTemplet('default')->setValue('idx',2)->doLayout(); ?>
</div>

<div class="tabContent" role="tabpanel" data-toggle="server" style="display:none;">
	<?php $IM->getWidget('dataroom/history')->setTemplet('default')->setValue('idx',3)->doLayout(); ?>
</div>

<div class="tabContent" role="tabpanel" data-toggle="old" style="display:none;">
	<?php $IM->getWidget('dataroom/history')->setTemplet('default')->setValue('idx',1)->doLayout(); ?>
</div>

<div style="text-align:center; line-height:30px; color:#999;">
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i>
</div>

<div class="WidgetDataroomHistoryDefault">
	<article class="historyBox">
		<h3>Version 3.0.0</h3>
		
		<div class="release">August 11, 2009</div>
		
		<ul>
			<li><span class="NEW">NEW</span> 언어셋지원 (번역해주실분을 구합니다.)</li>
			<li><span class="UPDATE">UPDATE</span> ExtJS 3.0 라이브러리로 업데이트하여 좀더 나은 UI구성과 속도향상</li>
			<li><span class="BUGFIX">BUGFIX</span> 로그인이 페이지를 이동함에 따라 자동으로 로그아웃되던 문제 수정</li>
			<li><span class="UPDATE">UPDATE</span> 배경색 및 글꼴색을 초기화할 수 있도록 초기화메뉴를 추가</li>
			<li><span class="UPDATE">UPDATE</span> 몇몇테마가 제거되고(쵸콜렛, 올리브, 페퍼민트, 블랙), 새로운 테마추가(그레이, 핑크, 옐로우, 오렌지, 다크그레이, 그린)</li>
			<li><span class="UPDATE">UPDATE</span> 메세지를 받은 시각이 한국표준시각기준에서 설정된 타임존에 맞게 출력되도록 업데이트</li>
			<li><span class="BUGFIX">BUGFIX</span> 차단자등록이 특정상황에서 의도치않은 방향으로 동작하던 문제 수정</li>
			<li><span class="BUGFIX">BUGFIX</span> 차단을 했음에도, 귓속말이 차단자로부터 받아지던 버그수정</li>
			<li><span class="BUGFIX">BUGFIX</span> 서버로 부터 데이터를 온전히 받지 못해, 닉네임이 정상적으로 출력되지 않거나, 접속자중 일부가 보이지 않던 문제 수정</li>
			<li><span class="UPDATE">UPDATE</span> 유저목록을 출력하는 알고리즘을 변경하여, 부하를 줄이고 유저목록을 실시간으로 출력할 수 있도록 업데이트</li>
			<li><span class="UPDATE">UPDATE</span> 글을 전송할 수 있는 버튼을 추가(모바일기기 대응)</li>
			<li><span class="NEW">NEW</span> 자신의 상태(온라인, 자리비움, 다른용무중 등)을 변경할 수 있도록 하는 기능 추가</li>
			<li><span class="UPDATE">UPDATE</span> 자리비움, 다른용무중일 때 귓속말이 왔을 때 귓속말을 보낸 상대방에게 부재중임을 알리도록 업데이트</li>
			<li><span class="NEW">NEW</span> 이모티콘 지원</li>
			<li><span class="UPDATE">UPDATE</span> CTRL+R 키를 이용하여, 이전에 귓속말을 보내거나 받았던 사람들에게 바로 귓속말을 보낼 수 있도록 업데이트</li>
			<li><span class="UPDATE">UPDATE</span> 방향키(위, 아래)를 이용하여 이전에 했던 대화를 그대로 전송할 수 있도록 업데이트</li>
			<li><span class="UPDATE">UPDATE</span> 다국어 지원으로 인하여, /귓, /도움 등의 명령어를 영문으로 변경 (/? 으로 바뀐 명령어를 확인하세요.)</li>
			<li><span class="UPDATE">UPDATE</span> 가로사이즈에 따라 늘어나던 광고영역이 고정된 크기로 나타날 수 있도록 업데이트</li>
			<li><span class="NEW">NEW</span> 가로사이즈에 따라 마지막 대화시각 및 현재시각을 추가</li>
			<li><span class="UPDATE">UPDATE</span> 도배방지기능이 정해진 라인수 이상으로 채팅할 때 바로 30초간 대화가 차단되는 방식으로 업데이트</li>
			<li><span class="BUGFIX">BUGFIX</span> 3분 채팅금지가 관리자가 아니더라도 가능했던 점 수정</li>
			<li><span class="UPDATE">UPDATE</span> 같은사람이 반복적으로 개인채널에 초대할 경우, 가장 최근의 초대메세지만 보이도록 업데이트</li>
			<li><span class="BUGFIX">BUGFIX</span> 개인채널에서 채널접속자의 닉네임이 변경되었을 때, 변경된 닉네임으로 나오지 않던 문제 수정</li>
		</ul>
		
		<i class="icon fa fa-cog"></i>
	</article>
</div>

<div style="text-align:center; line-height:30px; color:#999;">
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i>
</div>

<div class="WidgetDataroomHistoryDefault">
	<article class="historyBox">
		<h3>Version 2.0.0</h3>
		
		<div class="release">August 11, 2009</div>
		
		<ul>
			<li><span class="NEW">NEW</span> 마우스 오른쪽버튼을 클릭하였을 때, 미니온메뉴가 나타나도록 업데이트</li>
			<li><span class="NEW">NEW</span> 채팅스크롤을 일시적으로 고정할 수 있도록 업데이트</li>
			<li><span class="UPDATE">UPDATE</span> 가로형으로 사용할 때 배너크기가 과하게 출력되지 않도록 업데이트</li>
			<li><span class="UPDATE">UPDATE</span> 호출테러를 방지하기 위해, 호출을 받았던 사람이 30초 이내에는 새로운 호출을 받지 않도록 업데이트</li>
			<li><span class="NEW">NEW</span> 지속적인 도배로 인하여 5회 이상 경고를 받았을경우, 3분간 채팅을 하지 못하도록 업데이트</li>
			<li><span class="NEW">NEW</span> 10초 이내 설정된 횟수만큼 대화를 시도할 경우 경고와 함께 해당메세지를 전송하지 않도록 업데이트</li>
			<li><span class="NEW">NEW</span> 채널관리자가 특정유저의 대화를 3분간 제한할 수 있도록 업데이트</li>
			<li><span class="NEW">NEW</span> /명령어 추가 ("/도움" 을 입력하면 도움말이 출력됩니다.)</li>
			<li><span class="NEW">NEW</span> DEV알쯔 회원아이디로 미니온관리자권한을 획득할 수 있도록 업데이트</li>
			<li><span class="NEW">NEW</span> 화면비우기 기능 추가</li>
			<li><span class="NEW">NEW</span> 채널관리자가 최근에 이루어진 15줄의 채팅기록을 초기화할 수 있는 명령어 추가</li>
			<li><span class="BUGFIX">BUGFIX</span> 로그인/닉네임변경창의 미니온 로고색깔이 테마에 따라 변경되지 않던 문제 수정</li>
		</ul>
		
		<i class="icon fa fa-cog"></i>
	</article>
</div>

<div style="text-align:center; line-height:30px; color:#999;">
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i><br>
	<i class="fa fa-circle-thin"></i>
</div>

<div class="WidgetDataroomHistoryDefault">
	<article class="historyBox">
		<h3>Version 1.0.0</h3>
		
		<div class="release">July 16, 2009</div>
		
		<ul>
			<li><span class="NEW">NEW</span> 미니온 프로젝트 시작</li>
			<li><span class="NEW">NEW</span> 이미지닉네임지원</li>
			<li><span class="NEW">NEW</span> 웹폰트(13가지)지원</li>
			<li><span class="NEW">NEW</span> 호출기능(상대방에게 호출음으로 호출사실을 알림)</li>
			<li><span class="NEW">NEW</span> 개인채널(1:1대화 및 1:多대화) 및 귓속말기능 지원</li>
			<li><span class="NEW">NEW</span> 관리자에 한하여 IP차단 및 IP보기 지원</li>
			<li><span class="NEW">NEW</span> 채널관리툴 지원</li>
			<li><span class="NEW">NEW</span> 미니온서버 1,000명 동접테스트 완료</li>
		</ul>
		
		<i class="icon fa fa-cog"></i>
	</article>
</div>

<?php } ?>

<?php if ($IM->page == 'license') { ?>

<div class="boxDefault fontRed">
	<i class="fa fa-warning"></i> 미니톡은 클라이언트 프로그램과 서버 프로그램의 라이센스가 서로 다릅니다.<br>사용하고자 하는 프로그램의 라이센스를 정확히 확인하여 주시기 바랍니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-desktop"></i> 미니톡 클라이언트 라이센스 <span class="count">Minitalk Client License</span>
</div>

<div class="defaultContent">
	미니톡 채팅서버에 접속하기 위한 클라이언트 프로그램은 GPL v3 라이센스를 따릅니다.<br>
	Copyright (c) <?php echo date('Y'); ?> Arzz.<br><br>
	
	이 프로그램은 자유 소프트웨어입니다. 소프트웨어의 피양도자는 자유 소프트웨어 재단이 공표한 GNU 일반 공중 사용 허가서 2판 또는 그 이후 판을 임의로 선택해서, 그 규정에 따라 프로그램을 개작하거나 재배포할 수 있습니다.<br><br>
	이 프로그램은 유용하게 사용될 수 있으리라는 희망에서 배포되고 있지만, 특정한 목적에 맞는 적합성 여부나 판매용으로 사용할 수 있으리라는 묵시적인 보증을 포함한 어떠한 형태의 보증도 제공하지 않습니다. 보다 자세한 사항에 대해서는 GNU 일반 공중 사용 허가서를 참고하시기 바랍니다.<br><br>

	GNU 일반 공중 사용 허가서는 이 프로그램과 함께 제공됩니다. 만약, 이 문서가 누락되어 있다면 자유 소프트웨어 재단으로 문의하시기 바랍니다.<br>
	(자유 소프트웨어 재단: Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA)<br><br>
	
	<a href="http://korea.gnu.org/documents/copyleft/gpl.ko.html" target="_blank">GNU 일반 공중 사용허가서 전문</a>
	
	<br><br>
	<i class="fa fa-share-alt"></i> 미니톡 클라이언트에는 아래와 같은 오픈소스 프로그램이 사용되었습니다.<br>
	<b>ExtJS5.0</b> - GPL v3 (<a href="http://www.sencha.com" target="_blank">http://www.sencha.com</a>)<br>
	<b>socket.io</b> - MIT License (<a href="http://socket.io" target="_blank">http://socket.io</a>)<br><br>
	미니톡 클라이언트는 위의 오픈소스 라이센스 규정에 따라 오픈소스가 사용된 전체 소프트웨어(미니톡 클라이언트)의 소스코드를 공개하고 있습니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-server"></i> 미니톡 서버프로그램 라이센스 <span class="count">Minitalk Server License</span>
</div>

<div class="defaultContent">
	이 소프트웨어의 복제본과 관련된 문서화 파일("소프트웨어")을 구매하는 사람은 1개의 물리적 서버와 그 서버에 속한 N개의 도메인하에서 별다른 제한 없이 프로그램을 사용할 수 있는 권한을 부여 받습니다.<br>
	여기에는 소프트웨어의 복제본을 1개의 서버하에서 무제한으로 사용, 복제, 수정, 병합할 수 있는 권리가 포함됩니다.<br><br>

	이 소프트웨어를 획득 또는 구매한 사람은 어떠한 경우에라도 타인에게 이 소프트웨어를 양도, 공표, 배포할 수 없습니다.<br>
	이 소프트웨어를 일부 또는 전체를 변형하여 2차적 저작물을 창작할 수는 있으나, 그 저작물을 양도, 공표, 배포할 수 없습니다.<br><br>

	이 소프트웨어는 상품성, 특정 목적 적합성, 그리고 비침해에 대한 보증을 포함한 어떠한 형태의 보증도 명시적이나 묵시적으로 설정되지 않은 "있는 그대로의" 상태로 제공되며, 소프트웨어를 개발한 개발자나 저작권자는 어떠한 경우에도 소프트웨어나 소프트웨어의 사용 등의 행위와 관련하여 일어나는 어떤 요구사항이나 손해 및 기타 책임에 대해 계약상, 불법행위 또는 기타 이유로 인한 책임을 지지 않습니다.
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-lock"></i> 미니톡 구버전 라이센스 <span class="count">Minitalk Older License</span>
</div>

<div class="defaultContent">
	<div class="boxDefault fontRed"><i class="fa fa-warning"></i> 미니톡 구버전은 미니톡 5.X 버전 이하를 의미합니다.</div>
	
	<div class="blankSpace"></div>
	
	이 소프트웨어의 복제본과 관련된 문서화 파일("소프트웨어")을 구매하는 사람은 1개의 서버 및 1개의 도메인하에서 별다른 제한 없이 무상으로 사용할 수 있는 권한을 부여 받습니다.<br>
	여기에는 소프트웨어의 복제본을 1개의 서버 및 1개의 도메인하에서 무제한으로 사용, 복제, 수정, 병합할 수 있는 권리가 포함됩니다.<br><br>

	이 소프트웨어를 획득 또는 구매한 사람은 어떠한 경우에라도 타인에게 이 소프트웨어를 양도, 공표, 배포할 수 없습니다.<br>
	이 소프트웨어를 통해 소프트웨어를 임대방식으로 제공하거나, 서로 다른 서버 및 서로 다른 도메인하에 사용할 수 없으며, 이 소프트웨어를 일부 또는 전체를 변형하여 2차적 저작물을 창작할 수는 있으나, 그 저작물을 양도, 공표, 배포할 수 없습니다.<br><br>

	이 소프트웨어는 상품성, 특정 목적 적합성, 그리고 비침해에 대한 보증을 포함한 어떠한 형태의 보증도 명시적이나 묵시적으로 설정되지 않은 "있는 그대로의" 상태로 제공되며, 소프트웨어를 개발한 개발자나 저작권자는 어떠한 경우에도 소프트웨어나 소프트웨어의 사용 등의 행위와 관련하여 일어나는 어떤 요구사항이나 손해 및 기타 책임에 대해 계약상, 불법행위 또는 기타 이유로 인한 책임을 지지 않습니다.
</div>

<?php } ?>

<?php if ($IM->page == 'example') { ?>

<?php
$addonDir = __IM_DIR__.'/addons/syntaxhighlighter';
$IM->addSiteHeader('script',$addonDir.'/scripts/shCore.js');
$IM->addSiteHeader('script',$addonDir.'/scripts/shAutoloader.js');
$IM->addSiteHeader('style',$addonDir.'/styles/shCoreEmacs.css');
		
$script = array(
	'$(document).ready(function() {',
	'	SyntaxHighlighter.autoloader(',
	'		"applescript '.$addonDir.'/scripts/shBrushAppleScript.js",',
	'		"actionscript3 as3 '.$addonDir.'/scripts/shBrushAS3.js",',
	'		"bash shell '.$addonDir.'/scripts/shBrushBash.js",',
	'		"coldfusion cf '.$addonDir.'/scripts/shBrushColdFusion.js",',
	'		"cpp c '.$addonDir.'/scripts/shBrushCpp.js",',
	'		"c# c-sharp csharp '.$addonDir.'/scripts/shBrushCSharp.js",',
	'		"css '.$addonDir.'/scripts/shBrushCss.js",',
	'		"delphi pascal '.$addonDir.'/scripts/shBrushDelphi.js",',
	'		"diff patch pas '.$addonDir.'/scripts/shBrushDiff.js",',
	'		"erl erlang '.$addonDir.'/scripts/shBrushErlang.js",',
	'		"groovy '.$addonDir.'/scripts/shBrushGroovy.js",',
	'		"java '.$addonDir.'/scripts/shBrushJava.js",',
	'		"jfx javafx '.$addonDir.'/scripts/shBrushJavaFX.js",',
	'		"js jscript javascript '.$addonDir.'/scripts/shBrushJScript.js",',
	'		"perl pl '.$addonDir.'/scripts/shBrushPerl.js",',
	'		"php '.$addonDir.'/scripts/shBrushPhp.js",',
	'		"text plain '.$addonDir.'/scripts/shBrushPlain.js",',
	'		"py python '.$addonDir.'/scripts/shBrushPython.js",',
	'		"ruby rails ror rb '.$addonDir.'/scripts/shBrushRuby.js",',
	'		"sass scss '.$addonDir.'/scripts/shBrushSass.js",',
	'		"scala '.$addonDir.'/scripts/shBrushScala.js",',
	'		"sql '.$addonDir.'/scripts/shBrushSql.js",',
	'		"vb vbnet '.$addonDir.'/scripts/shBrushVb.js",',
	'		"xml xhtml xslt html '.$addonDir.'/scripts/shBrushXml.js"',
	'	);',
	'	SyntaxHighlighter.defaults["toolbar"] = false;',
	'	SyntaxHighlighter.defaults["ruler"] = true;',
	'	SyntaxHighlighter.all();',
	'});'
);
?>
<div class="defaultTitle">
	<i class="fa fa-desktop"></i> 미니톡 클라이언트 미리보기
</div>

<div class="defaultContent">
	<div class="row">
		<div class="col-sm-3">
			<script type="text/javascript" src="https://www.examples.kr/minitalk/script/minitalk.js" charset="UTF-8"></script>
			<script type="text/javascript">
			new Minitalk({
				id:"example1",
				channel:"example",
				width:"100%",
				height:500,
				skin:"default",
				type:"vertical",
				language:"ko",
				nickname:"Guest<?php echo rand(10000,99999); ?>"
			});
			</script>
		</div>
		
		<div class="col-sm-9">
			<script type="text/javascript" src="https://www.examples.kr/minitalk/script/minitalk.js" charset="UTF-8"></script>
			<script type="text/javascript">
			new Minitalk({
				id:"example2",
				channel:"example",
				width:"100%",
				height:500,
				skin:"default",
				type:"horizontal",
				language:"ko",
				nickname:"Guest<?php echo rand(10000,99999); ?>"
			});
			</script>
		</div>
	</div>
</div>

<div class="defaultTitle">
	<i class="fa fa-file-code-o"></i> 예제소스
</div>

<pre class="brush:html">
&lt;script type="text/javascript" src="http://www.yourdomain.com/minitalk/script/minitalk.js" charset="UTF-8"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
new Minitalk({
	id:"example",
	channel:"example",
	width:"100%",
	height:500,
	skin:"default",
	type:"auto",
	language:"ko"
});
&lt;/script&gt;
</pre>

<div class="defaultTitle">
	<i class="fa fa-file-code-o"></i> 회원연동 예제소스
</div>

<pre class="brush:php">
&lt;?php
$_MINITALK_KEY = '12345678123456781234567812345678'; // 미니톡 클라이언트 설치시 입력한 암호화KEY

function MiniTalkEncoder($value) {
	global $_MINITALK_KEY;
	
	$padSize = 16 - (strlen($value) % 16);
	$value = $value.str_repeat(chr($padSize),$padSize);
	$output = mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$_MINITALK_KEY,$value,MCRYPT_MODE_CBC,str_repeat(chr(0),16));
	return base64_encode($output);
}

function MiniTalkDecoder($value) {
	global $_MINITALK_KEY;
	
	$value = base64_decode($value);
	$output = mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$_MINITALK_KEY,$value,MCRYPT_MODE_CBC,str_repeat(chr(0),16));
	$valueLen = strlen($output);
	if ($valueLen % 16 > 0) $output = '';

	$padSize = ord($output{$valueLen - 1});
	if (($padSize < 1) || ($padSize > 16)) $output = '';

	for ($i=0;$i<$padSize;$i++) {
		if (ord($output{$valueLen - $i - 1}) != $padSize) $output = '';
	}
	
	return substr($output,0,$valueLen-$padSize);
}

function GetOpperCode($opper) {
	$value = json_encode(array('opper'=>$opper,'ip'=>$_SERVER['REMOTE_ADDR']));
	return urlencode(MiniTalkEncoder($value));
}
?&gt;
&lt;script type="text/javascript" src="http://www.yourdomain.com/minitalk/script/minitalk.js" charset="UTF-8"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
new Minitalk({
	id:"example",
	channel:"example",
	width:"100%",
	height:560,
	nickname:"&lt;?php echo $nickname; ?&gt;", // 해당변수에 회원닉네임이 있다고 가정
	&lt;?php if ($isAdmin == true) { // $isAdmin 변수가 true 일때 관리자 권한부여 ?&gt;
	opperCode:"&lt;?php echo GetOpperCode('ADMIN'); ?&gt;",
	&lt;?php } elseif ($isMember == true) { // 관리자가 아니면서 $isMember 변수가 true 일때 회원 권한부여 ?&gt;
	opperCode:"&lt;?php echo GetOpperCode('MEMBER'); ?&gt;",
	&lt;?php } ?&gt;
	skin:"default",
	type:"auto",
	language:"ko"
});
&lt;/script&gt;</pre>

<div class="boxDefault">
	API문서를 참고하여, 자신에게 맞는 환경설정 변수(Configs)를 정의하여 주시기 바랍니다.<br />
	이 예제문서는 가장 기본적이고 필수 환경설정 변수(Configs)만을 이용하여 작성되었습니다.<br /><br />
	<span class="fontRed">예제미리보기 페이지의 예제는 현재 미니톡 6.x의 최신버전으로, 새로운 버전이 업데이트 될 때마다 기본소스는 변경될 수 있습니다.</span>
</div>

<script>
<?php echo implode(PHP_EOL,$script); ?>
</script>

<?php } ?>

<?php if ($IM->page == 'screenshot') { ?>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 서버관리 페이지 <span class="count">Server management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-1.png" alt="Server management page">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 카테고리관리 페이지 <span class="count">Category management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-2.png" alt="Category management page">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 채널관리 페이지 <span class="count">Chatting channel management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-3.png" alt="Chatting channel management page">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 위젯스크립트 생성페이지 <span class="count">Chatting Widget Script Source Generator</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-4.png" alt="Chatting Widget Script Source Generator">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 로그관리 페이지 <span class="count">Chatting logs management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-5.png" alt="Chatting logs management page">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> IP차단자관리 페이지 <span class="count">Banned user management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-6.png" alt="Banned user management page">
</div>

<div class="blankSpace"></div>

<div class="defaultTitle">
	<i class="fa fa-picture-o"></i> 브로드캐스트관리 페이지 <span class="count">Broadcast message management page</span>
</div>

<div class="defaultContent wrapContent">
	<img src="/externals/images/minitalk.admin.screenshot-7.png" alt="Broadcast message management page">
</div>

<?php } elseif ($IM->menu == 'forum') { ?>

<style>
.forum {display:table; width:100%; table-layout:fixed;}
.forum .recently {display:table-cell; width:100%; vertical-align:top;}
.forum .recently .inner {padding-right:15px;}
.forum .sns {display:table-cell; width:280px; vertical-align:top;}

@media (max-width:767px) {
	.forum .recently .inner {padding:0px;}
	.forum .sns {display:none;}
}
</style>

<div class="forum">
	<div class="recently">
		<div class="inner">
			<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('bid','minitalk')->setValue('category',21)->setValue('titleIcon','<i class="fa fa-leaf"></i>')->doLayout(); ?>
			
			<div class="blankSpace"></div>
			
			<?php $IM->getWidget('qna/recently')->setTemplet('default')->setValue('count',10)->setValue('qid','minitalk')->setValue('titleIcon','<i class="fa fa-question"></i>')->doLayout(); ?>
			
			<div class="blankSpace"></div>
			
			<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('bid','minitalk')->setValue('category',22)->setValue('titleIcon','<i class="fa fa-bug"></i>')->doLayout(); ?>
			
			<div class="blankSpace"></div>
			
			<?php $IM->getWidget('board/recently')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('bid','minitalk')->setValue('category',23)->setValue('titleIcon','<i class="fa fa-leanpub"></i>')->doLayout(); ?>
		</div>
	</div>
	
	<div class="sns">
		<div>
			<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
			<ins class="adsbygoogle" style="display:inline-block;width:280px;height:280px" data-ad-client="ca-pub-3210736654114323" data-ad-slot="7126937767"></ins>
			<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
		</div>
		
		<div class="blankSpace"></div>
		
		<div id="fb-root"></div>
		<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.3&appId=223649611025503"; fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script>
		<div class="fb-page" data-href="https://www.facebook.com/minitalk.kr" data-hide-cover="false" data-show-facepile="true" data-show-posts="true" data-width="100%"><div class="fb-xfbml-parse-ignore"></div></div>
		
		<div class="blankSpace"></div>
		
		<div style="height:300px;">
			<a class="twitter-timeline" data-dnt="true" href="https://twitter.com/minitalk_kr" data-widget-id="248349519736082432"></a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
	</div>
</div>

<?php } ?>