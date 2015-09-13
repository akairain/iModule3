<?php
if (defined('__IM__') == false) exit;

REQUIRE_ONCE $Widget->getPath().'/classes/feed.class.php';

if ($Widget->getTempletDir() == null) $IM->printError('NOT_SELECTED_TEMPLET');
if (file_exists($Widget->getTempletPath().'/styles/style.css') == true) $IM->addSiteHeader('style',$Widget->getTempletDir().'/styles/style.css');

$rss = $Widget->getValue('rss');
$rss = is_array($rss) == true ? $rss : array($rss);
$limit = $Widget->getValue('limit');
$cache = $Widget->getValue('cache') ? $Widget->getValue('cache') : 86400;

if ($Widget->cacheCheck() < time() - $cache) {
	$feed = new feed();
	$items = array();
	$sorts = array();
	for ($i=0, $loop=count($rss);$i<$loop;$i++) {
		$data = $feed->loadRss($rss[$i]);
		
		foreach ($data->item as $item) {
			$content = (string)($item->description);
			$images = array();
			
			if (preg_match_all('/<img(.*?)src=("|\')?(.*?)("|\')? (.*?)>/i',$content,$matches) == true) {
				foreach ($matches[3] as $key=>$image) {
					$images[] = $matches[3][$key];
				}
			}
			$items[] = array(
				'rss_title'=>htmlSpecialChars($data->title),
				'rss_description'=>htmlSpecialChars($data->description),
				'rss_link'=>(string)($data->link),
				'title'=>htmlSpecialChars($item->title),
				'content'=>$content,
				'author'=>htmlSpecialChars($item->author),
				'link'=>(string)($item->link),
				'reg_date'=>intval($item->timestamp),
				'images'=>$images
			);
			$sorts[] = intval($item->timestamp);
		}
	}
	
	arsort($sorts);
	
	$lists = array();
	foreach ($sorts as $key=>$time) {
		$lists[] = $items[$key];
	}
	
	$data = json_encode($lists,JSON_UNESCAPED_UNICODE);
	$Widget->cacheStore($data);
} else {
	$data = $Widget->cache();
}

$lists = json_decode($data);
if ($limit) $lists = array_slice($lists,0,$limit);

INCLUDE $Widget->getTempletPath().'/templet.php';
?>