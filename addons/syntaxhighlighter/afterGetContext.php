<?php
if (defined('__SYNTAXHIGHLIGHTER_INCLUDED__')) return;
define('__SYNTAXHIGHLIGHTER_INCLUDED__',true);

if ($target == 'board' || $target == 'apidocument' || $target == 'qna' || $target == 'forum' || $target == 'dataroom') {
	if ($view != 'write') {
		$IM->addSiteHeader('script',$Addon->getDir().'/scripts/shCore.js');
		$IM->addSiteHeader('script',$Addon->getDir().'/scripts/shAutoloader.js');
		$IM->addSiteHeader('style',$Addon->getDir().'/styles/shCoreEmacs.css');
		
		$script = array(
			'$(document).ready(function() {',
			'	SyntaxHighlighter.autoloader(',
			'		"applescript '.$Addon->getDir().'/scripts/shBrushAppleScript.js",',
			'		"actionscript3 as3 '.$Addon->getDir().'/scripts/shBrushAS3.js",',
			'		"bash shell '.$Addon->getDir().'/scripts/shBrushBash.js",',
			'		"coldfusion cf '.$Addon->getDir().'/scripts/shBrushColdFusion.js",',
			'		"cpp c '.$Addon->getDir().'/scripts/shBrushCpp.js",',
			'		"c# c-sharp csharp '.$Addon->getDir().'/scripts/shBrushCSharp.js",',
			'		"css '.$Addon->getDir().'/scripts/shBrushCss.js",',
			'		"delphi pascal '.$Addon->getDir().'/scripts/shBrushDelphi.js",',
			'		"diff patch pas '.$Addon->getDir().'/scripts/shBrushDiff.js",',
			'		"erl erlang '.$Addon->getDir().'/scripts/shBrushErlang.js",',
			'		"groovy '.$Addon->getDir().'/scripts/shBrushGroovy.js",',
			'		"java '.$Addon->getDir().'/scripts/shBrushJava.js",',
			'		"jfx javafx '.$Addon->getDir().'/scripts/shBrushJavaFX.js",',
			'		"js jscript javascript '.$Addon->getDir().'/scripts/shBrushJScript.js",',
			'		"perl pl '.$Addon->getDir().'/scripts/shBrushPerl.js",',
			'		"php '.$Addon->getDir().'/scripts/shBrushPhp.js",',
			'		"text plain '.$Addon->getDir().'/scripts/shBrushPlain.js",',
			'		"py python '.$Addon->getDir().'/scripts/shBrushPython.js",',
			'		"ruby rails ror rb '.$Addon->getDir().'/scripts/shBrushRuby.js",',
			'		"sass scss '.$Addon->getDir().'/scripts/shBrushSass.js",',
			'		"scala '.$Addon->getDir().'/scripts/shBrushScala.js",',
			'		"sql '.$Addon->getDir().'/scripts/shBrushSql.js",',
			'		"vb vbnet '.$Addon->getDir().'/scripts/shBrushVb.js",',
			'		"xml xhtml xslt html '.$Addon->getDir().'/scripts/shBrushXml.js"',
			'	);',
			'	SyntaxHighlighter.defaults["toolbar"] = false;',
			'	SyntaxHighlighter.defaults["ruler"] = true;',
			'	SyntaxHighlighter.defaults["auto-links"] = false;',
			'	SyntaxHighlighter.all();',
			'});'
		);
		
		if ($target != 'apidocument') {
			$eventName = array('board'=>'Board.ment.print','qna'=>'Qna.answer.print, Qna.ment.print','forum'=>'Forum.ment.print','dataroom'=>'Dataroom.ment.print');
			
			$script = array_merge($script,array(
				'$(document).on("'.$eventName[$target].'",function(e,result) {',
				'	SyntaxHighlighter.vars.discoveredBrushes = null;',
				'	SyntaxHighlighter.autoloader(',
				'		"applescript '.$Addon->getDir().'/scripts/shBrushAppleScript.js",',
				'		"actionscript3 as3 '.$Addon->getDir().'/scripts/shBrushAS3.js",',
				'		"bash shell '.$Addon->getDir().'/scripts/shBrushBash.js",',
				'		"coldfusion cf '.$Addon->getDir().'/scripts/shBrushColdFusion.js",',
				'		"cpp c '.$Addon->getDir().'/scripts/shBrushCpp.js",',
				'		"c# c-sharp csharp '.$Addon->getDir().'/scripts/shBrushCSharp.js",',
				'		"css '.$Addon->getDir().'/scripts/shBrushCss.js",',
				'		"delphi pascal '.$Addon->getDir().'/scripts/shBrushDelphi.js",',
				'		"diff patch pas '.$Addon->getDir().'/scripts/shBrushDiff.js",',
				'		"erl erlang '.$Addon->getDir().'/scripts/shBrushErlang.js",',
				'		"groovy '.$Addon->getDir().'/scripts/shBrushGroovy.js",',
				'		"java '.$Addon->getDir().'/scripts/shBrushJava.js",',
				'		"jfx javafx '.$Addon->getDir().'/scripts/shBrushJavaFX.js",',
				'		"js jscript javascript '.$Addon->getDir().'/scripts/shBrushJScript.js",',
				'		"perl pl '.$Addon->getDir().'/scripts/shBrushPerl.js",',
				'		"php '.$Addon->getDir().'/scripts/shBrushPhp.js",',
				'		"text plain '.$Addon->getDir().'/scripts/shBrushPlain.js",',
				'		"py python '.$Addon->getDir().'/scripts/shBrushPython.js",',
				'		"ruby rails ror rb '.$Addon->getDir().'/scripts/shBrushRuby.js",',
				'		"sass scss '.$Addon->getDir().'/scripts/shBrushSass.js",',
				'		"scala '.$Addon->getDir().'/scripts/shBrushScala.js",',
				'		"sql '.$Addon->getDir().'/scripts/shBrushSql.js",',
				'		"vb vbnet '.$Addon->getDir().'/scripts/shBrushVb.js",',
				'		"xml xhtml xslt html '.$Addon->getDir().'/scripts/shBrushXml.js"',
				'	);',
				'	setTimeout("SyntaxHighlighter.highlight()",1000);',
				'});'
			));
		}
		$context.= PHP_EOL.'<script>'.implode(PHP_EOL,$script).'</script>'.PHP_EOL;
	}
}
?>