<?php
class ModuleBoard {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $boardPages = array();
	private $postPages = array();
	
	private $boards = array();
	private $categorys = array();
	private $posts = array();
	private $ments = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->board = 'board_table';
		$this->table->category = 'board_category_table';
		$this->table->post = 'board_post_table';
		$this->table->ment = 'board_ment_table';
		$this->table->ment_depth = 'board_ment_depth_table';
		$this->table->attachment = 'board_attachment_table';
		$this->table->history = 'board_history_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/board.js');
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
	}
	
	function getApi($api) {
		$data = new stdClass();
		
		if ($api == 'getList') {
			$start = Request('start') ? Request('start') : 0;
			$limit = Request('limit') ? Request('limit') : 10;
			$sort = Request('sort') ? Request('sort') : 'idx';
			$desc = Request('desc') ? Request('desc') : 'desc';
			$bid = Request('bid');
			
			$lists = $this->db()->select($this->table->post);
			if ($bid !== null) $lists->where('bid',$bid);
			$total = $lists->copy()->count();
			$lists = $lists->orderBy($sort,$desc)->limit($start,$limit)->get();
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$lists[$i] = $this->getArticle('post',$lists[$i],true,true);
			}
			
			$data->start = $start;
			$data->limit = $limit;
			$data->total = $total;
			$data->lists = $lists;
		}
		
		return $data;
	}
	
	function getLanguage($code) {
		if ($this->lang == null) {
			if (file_exists($this->Module->getPath().'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->Module->getPath().'/languages/'.$this->IM->language.'.json'));
			} else {
				$this->lang = json_decode(file_get_contents($this->Module->getPath().'/languages/'.$this->Module->getInfo()->languages[0].'.json'));
			}
		}
		
		$temp = explode('/',$code);
		if (count($temp) == 1) {
			return isset($this->lang->$code) == true ? $this->lang->$code : '';
		} else {
			$string = $this->lang;
			for ($i=0, $loop=count($temp);$i<$loop;$i++) {
				if (isset($string->$temp[$i]) == true) $string = $string->$temp[$i];
				else return '';
			}
			return $string;
		}
	}
	
	function getCountInfo($bid,$config) {
		$board = $this->getBoard($bid);
		if ($board == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $board->postnum;
			$info->last_time = $board->last_post;
		} elseif (isset($config->category) == true) {
			if ($board->use_category == 'USEDALL') {
				$count = $board->postnum;
				$categorys = $this->db()->select($this->table->category)->where('bid',$bid)->get();
				for ($i=0, $loop=count($categorys);$i<$loop;$i++) {
					if ($categorys[$i]->idx != $config->category) $count = $count - $categorys[$i]->postnum;
				}
				$info->count = $count;
			} else {
				$info->count = $this->getCategory($config->category)->postnum;
			}
			$info->last_time = $this->getCategory($config->category)->last_post;
		}
		
		return $info;
	}
	
	function getContext($bid,$config=null) {
		$values = new stdClass();
		
		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		$board = $this->getBoard($bid);
		if ($board == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($bid,$config);
		
		switch ($view) {
			case 'list' :
				$context.= $this->getListContext($bid,$config);
				break;
				
			case 'view' :
				$context.= $this->getViewContext($bid,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($bid,$config);
				break;
		}
		
		$context.= $this->getFooter($bid,$config);
		
		$this->IM->fireEvent('afterGetContext','board',$view,null,null,$context);
		
		return $context;
	}
	
	function getBoard($bid) {
		if (isset($this->boards[$bid]) == true) return $this->boards[$bid];
		$board = $this->db()->select($this->table->board)->where('bid',$bid)->getOne();
		if ($board == null) {
			$this->boards[$bid] = null;
		} else {
			$board->templetPath = $this->Module->getPath().'/templets/'.$board->templet;
			$board->templetDir = $this->Module->getDir().'/templets/'.$board->templet;
			
			$this->boards[$bid] = $board;
		}
		
		return $this->boards[$bid];
	}
	
	function getPush($code,$fromcode,$content) {
		$latest = array_pop($content);
		$count = count($content);
		
		$push = new stdClass();
		$push->image = null;
		$push->link = null;
		if ($count > 0) $push->content = $this->getLanguage('push/'.$code.'s');
		else $push->content = $this->getLanguage('push/'.$code);
		
		if ($code == 'ment') {
			$ment = $this->getMent($latest->idx);
			$from = $ment->name;
			$push->image = $this->IM->getModule('member')->getMember($ment->midx)->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		if ($code == 'replyment') {
			$ment = $this->getMent($latest->idx);
			$from = $ment->name;
			$push->image = $this->IM->getModule('member')->getMember($ment->midx)->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		if ($code == 'post_good' || $code == 'post_bad') {
			$from = $this->IM->getModule('member')->getMember($latest->from)->nickname;
			$push->image = $this->IM->getModule('member')->getMember($latest->from)->photo;
			
			if ($code == 'post_bad') {
				$from = '';
				$push->image = $push->image = $this->IM->getModule('member')->getMember(0)->photo;
			}
			
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		if ($code == 'ment_good' || $code == 'ment_bad') {
			$from = $this->IM->getModule('member')->getMember($latest->from)->nickname;
			$push->image = $this->IM->getModule('member')->getMember($latest->from)->photo;
			
			if ($code == 'ment_bad') {
				$from = '';
				$push->image = $push->image = $this->IM->getModule('member')->getMember(0)->photo;
			}
			
			$ment = $this->getMent($fromcode);
			$post = $this->getPost($ment->parent);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		$push->content = str_replace('{count}','<b>'.$count.'</b>',$push->content);
		return $push;
	}
	
	function getCategory($category) {
		if (isset($this->categorys[$category]) == true) return $this->categorys[$category];
		
		$this->categorys[$category] = $this->db()->select($this->table->category)->where('idx',$category)->getOne();
		
		return $this->categorys[$category];
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getPostPage($idx,$domain=null) {
		if (isset($this->postPages[$idx]) == true) return $this->postPages[$idx];
		
		$post = $this->getPost($idx);
		
		if ($post->category == 0) {
			$this->postPages[$idx] = $this->getBoardPage($post->bid,null,$domain);
			return $this->postPages[$idx];
		}
		
		$this->postPages[$idx] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'board' && $pages[$i]->context->context == $post->bid && $pages[$i]->context->config != null && $pages[$i]->context->config->category == $post->category) {
					$this->postPages[$idx] = $pages[$i];
					return $this->postPages[$idx];
				}
			}
		}
		
		if ($domain === null && $this->postPages[$idx] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getPostPage($idx,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->postPages[$idx] = $this->getPostPage($idx,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		$this->postPages[$idx] = $this->getBoardPage($post->bid);
		
		return $this->postPages[$idx];
	}
	
	function getBoardPage($bid,$category=null,$domain=null) {
		if (isset($this->boardPages[$bid]) == true && $category == null) return $this->boardPages[$bid];
		
		$this->boardPages[$bid] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'board' && $pages[$i]->context->context == $bid) {
					if ($category != null && $pages[$i]->context->config != null && $pages[$i]->context->config->category == $category) {
						return $pages[$i];
					}
					
					if ($category == null && $pages[$i]->context->config == null) {
						$this->boardPages[$bid] = $pages[$i];
						return $this->boardPages[$bid];
					}
					
					$this->boardPages[$bid] = $this->boardPages[$bid] == null ? $pages[$i] : $this->boardPages[$bid];
				}
			}
		}
		
		if ($domain === null && $this->boardPages[$bid] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getBoardPage($bid,$category,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->boardPages[$bid] = $this->getBoardPage($bid,$category,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		return $this->boardPages[$bid];
	}
	
	function getPost($idx) {
		if (isset($this->posts[$idx]) == true) return $this->posts[$idx];
		
		$this->posts[$idx] = $this->db()->select($this->table->post)->where('idx',$idx)->getOne();
		return $this->posts[$idx];
	}
	
	function getMent($idx) {
		if (isset($this->ments[$idx]) == true) return $this->ments[$idx];
		
		$this->ments[$idx] = $this->db()->select($this->table->ment_depth.' d','d.*,m.*')->join($this->table->ment.' m','d.idx=m.idx','LEFT')->where('d.idx',$idx)->getOne();
		return $this->ments[$idx];
	}
	
	function getHeader($bid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$board = $this->getBoard($bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($bid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$board = $this->getBoard($bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getArticle($type,$article,$isLink=false,$isApi=false) {
		if ($type == 'post') {
			if (is_numeric($article) == true) $article = $this->getPost($article);
			$article->title = GetString($article->title,'replace');
			$article->is_secret = $article->is_secret == 'TRUE';
			$article->is_image = preg_match('/<img(.*?)>/',$article->content);
			$article->is_file = $this->db()->select($this->table->attachment)->where('parent',$article->idx)->where('type','POST')->count() > 0;
			$article->is_link = preg_match('/<a(.*?)href(.*?)>/',$article->content);
			$article->is_video = preg_match('/<iframe(.*?)src=(.*?)(youtube|vimeo|daum|naver)(.*?)>/',$article->content);
			
			if (preg_match('/<img(.*?)data-idx="([0-9]+)"(.*?)>/',$article->content,$match) == true) {
				$article->image = $this->IM->getModule('attachment')->getFileInfo($match[2]);
			} else {
				$article->image = null;
			}
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->idx);
				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->idx,false,$page->domain,$page->language);
			}
		} else {
			if (is_numeric($article) == true) $article = $this->getMent($article);
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->parent);
				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->parent,false,$page->domain,$page->language);
			}
		}
		
		$article->member = $this->IM->getModule('member')->getMember($article->midx);
		$article->name = $this->IM->getModule('member')->getMemberNickname($article->midx,true,$article->name);
		$article->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($article->content,$isApi)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
		$article->ip = $this->getArticleIp($article->ip);
		
		return $article;
	}
	
	function getArticleIp($ip) {
		$temp = explode('.',$ip);
		$temp[2] = '***';
		return implode('.',$temp);
	}
	
	function getArticleContent($content,$isApi=false) {
		if (preg_match_all('/<img(.*?)data-idx="([0-9]+)"(.*?)>/',$content,$match) == true) {
			for ($i=0, $loop=count($match[0]);$i<$loop;$i++) {
				$file = $this->IM->getModule('attachment')->getFileInfo($match[2][$i]);
				$image = str_replace('data-idx="'.$match[2][$i].'"','data-idx="'.$match[2][$i].'" src="'.$this->IM->getModule('attachment')->getAttachmentUrl($match[2][$i],'view',$isApi).'" alt="'.$file->name.'"',$match[0][$i]);
				$content = str_replace($match[0][$i],$image,$content);
			}
		}
		return $content;
	}
	
	function getError($content,$title='') {
		return $content;
	}
	
	function getAttachmentFile($file) {
		return $this->db()->select($this->table->attachment)->where('idx',$file->idx)->getOne();
	}
	
	function getListContext($bid,$config,$isView=false) {
		ob_start();
		
		if ($isView == false) {
			$this->IM->setView('list');
			$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		}
		
		$category = empty($config->category) == true ? Request('category') : $config->category;
		
		$board = $this->getBoard($bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		$strQuery = $this->db()->select($this->table->post)->where('bid',$bid);
		
		if ($category != null) {
			if ($board->use_category == 'USEDALL') {
				$strQuery = $strQuery->where('category',array(0,$category),'IN');
			} else {
				$strQuery = $strQuery->where('category',$category);
			}
		}
		
		$key = Request('key') ? Request('key') : 'content';
		$keyword = Request('keyword');
		
		if ($keyword != null && strlen($keyword) > 0) {
			if ($key == 'content') $strQuery = $strQuery->where('p.title,p.search',$keyword,'FULLTEXT');
			elseif ($key == 'name') $strQuery = $strQuery->where('p.name',$keyword,'LIKE');
			elseif ($key == 'ment') $strQuery = $strQuery->join($this->table->ment.' m','p.idx=m.parent','LEFT')->groupBy('m.parent')->where('m.search',$keyword,'FULLTEXT');
		}
		
		$sort = 'idx';
		$direction = 'desc';
		
		if ($isView == true) {
			$idx = Request('idx');
			$post = $this->getPost($idx);
			$prevPost = $strQuery->copy()->where($sort,$post->$sort,$direction == 'desc' ? '>=' : '<=')->count();
			$p = ceil($prevPost/$board->postlimit);
		} else {
			$idx = null;
			$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
			$p = $p < 1 ? 1 : $p;
		}
		
		$startPosition = ($p-1) * $board->postlimit;
		
		$totalCount = $strQuery->copy()->count();
		$strQuery = $strQuery->orderBy($sort,$direction);
		$totalPage = ceil($totalCount/$board->postlimit);
		$lists = $strQuery->limit($startPosition,$board->postlimit)->get();
		
		$pagination = GetPagination($p,$totalPage,$board->pagelimit,'LEFT');
		
		$loopnum = $totalCount - ($p - 1) * $board->postlimit;
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i] = $this->getArticle('post',$lists[$i]);
			$lists[$i]->loopnum = $loopnum - $i;
			$lists[$i]->link = $this->IM->getUrl(null,null,'view',$lists[$i]->idx).$this->IM->getQueryString();
		}
		
		echo '<form name="ModuleBoardListForm" onsubmit="return Board.getListUrl(this);">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.$this->IM->page.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKey" value="'.$key.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKeyword" value="'.$keyword.'">'.PHP_EOL;
		echo '<input type="hidden" name="oCategory" value="'.$category.'">'.PHP_EOL;
		echo '<input type="hidden" name="oSort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="oDirection" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="category" value="'.$category.'">'.PHP_EOL;
		echo '<input type="hidden" name="sort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="direction" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="p" value="'.$p.'">'.PHP_EOL;
		
		
		$values = new stdClass();
		$values->lists = $lists;
		$this->IM->fireEvent('afterInitContext','board','list',$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/list.php') == true) {
			INCLUDE $templetPath.'/list.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getViewContext($bid,$config) {
		ob_start();
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		
		if ($this->IM->getModule('member')->getLogged() !== 1 && $post->is_secret == 'TRUE') {
			if ($post->midx == 0) {
				return $this->getError($this->getLanguage('error/forbidden'));
			} else if ($this->IM->getModule('member')->getLogged() != $post->midx) {
				return $this->getError($this->getLanguage('error/forbidden'));
			} else {
				
			}
		}
		
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'nofollow'));
		$this->IM->setSiteCanonical($this->IM->getUrl(null,null,'view',$idx,true));
		$this->IM->setSiteTitle($post->title);
		$this->IM->setSiteDescription($post->search);
		
		if ($post->image != null) {
			$this->IM->setSiteImage($this->IM->getModule('attachment')->getAttachmentUrl($post->image->idx,'view'));
		}
		
		$board = $this->getBoard($bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		$this->db()->update($this->table->post,array('hit'=>$this->db()->inc()))->where('idx',$idx)->execute();
		
		if ($this->IM->getModule('member')->isLogged() == true) {
			$vote = $this->db()->select($this->table->history)->where('type','POST')->where('parent',$idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
			$voted = $vote == null ? null : $vote->result;
		} else {
			$voted = null;
		}
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$values = new stdClass();
		$values->post = $post;
		$values->attachments = $attachments;
		$this->IM->fireEvent('afterInitContext','board','view',$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.php') == true) {
			INCLUDE $templetPath.'/view.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		$context.= $this->getListContext($bid,$config,true);
		
		return $context;
	}
	
	function getModify($type,$idx) {
		ob_start();
		
		if ($type == 'post') {
			$post = $this->getPost($idx);
			$board = $this->getBoard($post->bid);
		} else {
			$ment = $this->getMent($idx);
			$board = $this->getBoard($ment->bid);
		}
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		$title = $this->getLanguage($type.'Write/modify');
		echo '<form name="ModuleBoardModifyForm" onsubmit="return Board.'.$type.'.modify('.$idx.',this);">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage($type.'Write/password').'</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '<input type="password" name="password" class="inputControl">'.PHP_EOL;
		$content.= '<div class="helpBlock" data-error="'.$this->getLanguage('error/incorrectPassword').'"></div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/modal.php') == true) {
			INCLUDE $templetPath.'/modal.php';
		} else {
			INCLUDE $this->Module->getPath().'/templets/modal.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getDelete($type,$idx) {
		ob_start();
		
		if ($type == 'post') {
			$post = $this->getPost($idx);
			$board = $this->getBoard($post->bid);
		} else {
			$ment = $this->getMent($idx);
			$board = $this->getBoard($ment->bid);
		}
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		
		$title = $this->getLanguage($type.'Delete/title');
		echo '<form name="ModuleBoardDeleteForm" onsubmit="return Board.delete(this);">'.PHP_EOL;
		echo '<input type="hidden" name="action" value="delete">'.PHP_EOL;
		echo '<input type="hidden" name="type" value="'.$type.'">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		
		if ($type == 'post') {
			if ($this->checkPermission('post_delete') == true || ($post->midx != 0 && $post->midx == $this->IM->getModule('member')->getLogged())) {
				$content = '<div class="message">'.$this->getLanguage('postDelete/confirm').'</div>'.PHP_EOL;
			} else {
				$content = '<div class="message">'.$this->getLanguage('postDelete/password').'</div>'.PHP_EOL;
				$content.= '<div class="inputBlock">'.PHP_EOL;
				$content.= '<input type="password" name="password" class="inputControl">'.PHP_EOL;
				$content.= '<div class="helpBlock" data-error="'.$this->getLanguage('error/incorrectPassword').'"></div>'.PHP_EOL;
				$content.= '</div>'.PHP_EOL;
			}
		} else {
			if ($this->checkPermission('ment_delete') == true || ($ment->midx != 0 && $ment->midx == $this->IM->getModule('member')->getLogged())) {
				$content = '<div class="message">'.$this->getLanguage('mentDelete/confirm').'</div>'.PHP_EOL;
			} else {
				$content = '<div class="message">'.$this->getLanguage('mentDelete/password').'</div>'.PHP_EOL;
				$content.= '<div class="inputBlock">'.PHP_EOL;
				$content.= '<input type="password" name="password" class="inputControl">'.PHP_EOL;
				$content.= '<div class="helpBlock" data-error="'.$this->getLanguage('error/incorrectPassword').'"></div>'.PHP_EOL;
				$content.= '</div>'.PHP_EOL;
			}
		}
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/modal.php') == true) {
			INCLUDE $templetPath.'/modal.php';
		} else {
			INCLUDE $this->Module->getPath().'/templets/modal.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWriteContext($bid,$config) {
		ob_start();
		
		$this->IM->setView('write');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noidex,nofollow'));
		
		$board = $this->getBoard($bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if ($board->use_category != 'NONE') {
			$page = $this->IM->getPages($this->IM->menu,$this->IM->page);
			if (empty($config->category) == true || $board->use_category == 'USEDALL') {
				$categorys = $this->db()->select($this->table->category)->where('bid',$bid)->orderBy('sort','asc')->get();
			} else {
				$categorys = array();
			}
		} else {
			$categorys = array();
		}
		
		$default = new stdClass();
		
		$idx = Request('idx');
		if ($idx !== null) {
			$post = $this->getPost($idx);
			
			if ($post == null) {
				header("HTTP/1.1 404 Not Found");
				return $this->getError($this->getLangauge('error/notFound'));
			}
			
			if ($this->checkPermission('post_modify') == false && ($post->midx != 0 && $post->midx != $this->IM->getModule('member')->getLogged())) {
				header("HTTP/1.1 403 Forbidden");
				return $this->getError($this->getLanguage('error/forbidden'));
			} elseif ($post->midx == 0) {
				$password = Request('password');
				$mHash = new Hash();
				if ($mHash->password_validate($password,$post->password) == false) {
					header("HTTP/1.1 403 Forbidden");
					return $this->getError($this->getLanguage('error/incorrectPassword'));
				}
			}
			
			$post->content = $this->getArticleContent($post->content);
			$post->is_notice = $post->is_notice == 'TRUE' ? true : false;
			$post->is_html_title = $post->is_html_title == 'TRUE' ? true : false;
			$post->is_secret = $post->is_secret == 'TRUE' ? true : false;
			$post->is_hidename = $post->is_hidename == 'TRUE' ? true : false;
			
			$post->attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
			for ($i=0, $loop=count($post->attachments);$i<$loop;$i++) {
				$post->attachments[$i] = $post->attachments[$i]->idx;
			}
		} else {
			if (isset($config->category) == true) $default->category = $config->category;
			$post = null;
		}
		
		$formName = 'ModuleBoardWriteForm-'.rand(10000,99999);
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Board.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="bid" value="'.$bid.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($post !== null) echo '<input type="hidden" name="idx" value="'.$post->idx.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Board.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$board = $this->getBoard($post->bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		echo '<div id="ModuleBoardMentList-'.$parent.'" class="mentList">'.PHP_EOL;
		$lists = $this->getMentLastPage($parent);
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			echo $this->getMentItem($lists[$i]);
		}
		
		if (count($lists) == 0) echo '<div class="empty">'.$this->getLanguage('mentList/empty').'</div>'.PHP_EOL;
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentPagination($parent,$p=null) {
		ob_start();
		
		$post = $this->getPost($parent);
		$board = $this->getBoard($post->bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		$totalMents = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$totalPage = ceil($totalMents/$board->mentlimit) == 0 ? 1 : ceil($totalMents/$board->mentlimit);
		
		$pagination = GetPagination($p == null ? $totalPage : $p,$totalPage,$board->pagelimit,'LEFT','@Board.ment.loadPage');
		
		echo '<div id="ModuleBoardMentPagination-'.$parent.'" class="mentPagination" data-parent="'.$parent.'"'.($totalPage == 1 ? ' style="display:none;"' : '').'>'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/ment.pagination.php') == true) {
			INCLUDE $templetPath.'/ment.pagination.php';
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentItem($ment) {
		ob_start();
		
		$board = $this->getBoard($ment->bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		echo '<div id="ModuleBoardMentItem-'.$ment->idx.'" data-idx="'.$ment->idx.'" data-parent="'.$ment->parent.'" data-modify="'.$ment->modify_date.'" class="mentItem ment'.($ment->depth == 0 ? 'Parent' : 'Child').'">'.PHP_EOL;
		
		$ment = $this->getArticle('ment',$ment);
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$ment->idx)->where('type','MENT')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		if ($this->IM->getModule('member')->isLogged() == true) {
			$vote = $this->db()->select($this->table->history)->where('type','MENT')->where('parent',$ment->idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
			$voted = $vote == null ? null : $vote->result;
		} else {
			$voted = null;
		}
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/ment.item.php') == true) {
			INCLUDE $templetPath.'/ment.item.php';
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentWrite($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		$board = $this->getBoard($post->bid);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		echo '<div id="ModuleBoardMentWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleBoardMentForm-'.$parent.'" onsubmit="return Board.ment.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		echo '<input type="hidden" name="source" value="">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/ment.write.php') == true) {
			INCLUDE $templetPath.'/ment.write.php';
		}
		
		echo '</form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Board.ment.init("ModuleBoardMentForm-'.$parent.'"); });</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentPage($parent,$p=1,$mentlimit) {
		$startPosition = ($p-1) * $mentlimit;
		$lists = $this->db()->select($this->table->ment_depth.' d','d.*,m.*')->join($this->table->ment.' m','d.idx=m.idx','LEFT')->where('d.parent',$parent)->orderBy('head','asc')->orderBy('arrange','asc')->limit($startPosition,$mentlimit)->get();
		
		return $lists;
	}
	
	function getMentLastPage($parent) {
		$post = $this->getPost($parent);
		if ($post == null) return array();
		
		$board = $this->getBoard($post->bid);
		$totalCount = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$lastPage = $totalCount > 0 ? ceil($totalCount/$board->mentlimit) : 1;
		
		return $this->getMentPage($parent,$lastPage,$board->mentlimit);
	}
	
	function getMentPosition($idx) {
		$ment = $this->getMent($idx);
		if ($ment == null) return 0;
		
		$board = $this->getBoard($ment->bid);
		$position = $this->db()->select($this->table->ment_depth)->where('parent',$ment->parent)->where('head',$ment->head,'<=')->where('arrange',$ment->arrange,'<=')->count();
		$page = ceil($position/$board->mentlimit);
		
		return $page;
	}
	
	function getWysiwyg($name) {
		$wysiwyg = $this->IM->getModule('wysiwyg')->setName($name)->setModule('board');
		
		return $wysiwyg;
	}
	
	function getOptionForm($language,$name,$value=null,$type=null) {
		$id = $name.'-'.uniqid();
		
		$disabled = false;
		switch ($name) {
			case 'is_notice' :
				$disabled = true;
				break;
				
			case 'is_html_title' :
				$disabled = true;
				break;
				
			case 'is_secret' :
				break;
			
			case 'is_hidename' :
				if ($this->IM->getModule('member')->isLogged() == false) $disabled = true;
				break;
		}
		
		$sHTML = '<input type="checkbox" id="'.$id.'" name="'.$name.'" value="TRUE"';
		$sHTML.= $value === true ? ' checked="checked"' : '';
		$sHTML.= $disabled === true ? ' disabled="disabled"' : '';
		$sHTML.= '><label for="'.$id.'"'.($disabled === true ? ' class="disabled"' : '').'>'.$this->getLanguage($language.'/option/'.$name.($type == 'short' ? '_short' : '')).'</label>';
		
		return $sHTML;
	}
	
	function encodeContent($content,$attachment) {
		if (preg_match_all('/<img(.*?)data-idx="([0-9]+)"(.*?)>/',$content,$match) == true) {
			for ($i=0, $loop=count($match[0]);$i<$loop;$i++) {
				if (in_array($match[2][$i],$attachment) == true) {
					$image = preg_replace('/ src="(.*?)"/','',$match[0][$i]);
					$content = str_replace($match[0][$i],$image,$content);
				} else {
					$file = $this->db()->select($this->table->attachment)->where('idx',$match[2][$i])->getOne();
					if ($file == null) {
						$content = str_replace($match[0][$i],'',$content);
					} else {
						$fileIdx = $this->IM->getModule('attachment')->copyFile($match[2][$i]);
						$image = preg_replace('/ src="(.*?)"/','',$match[0][$i]);
						$image = str_replace('data-idx="'.$match[2][$i].'"','data-idx="'.$fileIdx.'"',$image);
						$content = str_replace($match[0][$i],'',$content);
					}
				}
			}
		}
		
		return $content;
	}
	
	function checkMentTree($idx) {
		$tree = $this->db()->select($this->table->ment_depth)->where('source',$idx)->get();
		for ($i=0, $loop=count($tree);$i<$loop;$i++) {
			$ment = $this->getMent($tree[$i]->idx);
			if ($ment->is_delete == 'FALSE') return true;
			if ($this->checkMentTree($ment->idx) == true) return true;
		}
		return false;
	}
	
	function checkPermission($type) {
		if ($this->IM->getModule('member')->isLogged() == true && $this->IM->getModule('member')->getMember()->type == 'ADMINISTRATOR') return true;
		return false;
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'listUrl') {
			$menu = Request('menu');
			$page = Request('page');
			
			$page = $this->IM->getPages($menu,$page);
			
			$oKey = Request('oKey');
			$oKeyword = Request('oKeyword');
			$oCategory = Request('oCategory');
			$oSort = Request('oSort');
			$oDirection = Request('oDirection');
			
			$key = Request('key');
			$keyword = Request('keyword');
			$category = Request('category');
			$sort = Request('sort');
			$direction = Request('direction');
			
			$key = Request('key');
			$keyword = Request('keyword');
			$p = Request('p') ? Request('p') : 1;
			
			$queryString = 'menu='.$page->menu.'&page='.$page->page.'&key='.$key.'&keyword='.$keyword.'&category='.$category.'&sort='.$sort.'&direction='.$direction.'&p='.$p;
			
			if ($oKey != $key || $oKeyword != $keyword || $oCategory != $category || $oSort != $sort || $oDirection != $direction) $p = 1;
			
			$default = array();
			if (strlen($keyword) == 0) {
				$default['key'] = '';
				$default['keyword'] = '';
			}
			if ($sort == 'idx' && $direction == 'desc') $default['sort'] = $default['direction'] = '';
			if (isset($page->context->config->category) == true && $page->context->config->category == $category) $default['category'] = '';
			
			$url = $this->IM->getUrl($page->menu,$page->page,'list',$p).$this->IM->getQueryString($default,$queryString);
			
			$results->success = true;
			$results->url = $url;
		}
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->bid = Request('bid');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->category = Request('category');
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			$values->is_notice = Request('is_notice') && $this->checkPermission('notice') == true ? 'TRUE' : 'FALSE';
			$values->is_html_title = Request('is_html_title') && $this->checkPermission('html_title') == true ? 'TRUE' : 'FALSE';
			$values->is_secret = Request('is_secret') ? 'TRUE' : 'FALSE';
			$values->is_hidename = Request('is_hidename') && $this->getModule('member')->isLogged() == true ? 'TRUE' : 'FALSE';
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$values->name = Request('name') ? Request('name') : $values->errors['name'] = $this->getLanguage('postWrite/help/name/error');
				$values->password = Request('password') ? Request('password') : $values->errors['password'] = $this->getLanguage('postWrite/help/password/error');
				$values->email = Request('email');
				$values->midx = 0;
			} else {
				$values->name = $this->IM->getModule('member')->getMember()->nickname;
				$values->password = '';
				$values->email = $this->IM->getModule('member')->getMember()->email;
				$values->midx = $this->IM->getModule('member')->getLogged();
			}
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			$values->board = $this->getBoard($values->bid);
			
			if ($values->board->use_category != 'NONE') {
				if ($values->board->use_category == 'FORCE' && ($values->category == null || preg_match('/^[1-9]+[0-9]*$/',$values->category) == false)) {
					$values->errors['category'] = $this->getLanguage('postWrite/help/category/error');
				}
			} else {
				$values->category = 0;
			}
			
			if (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['bid'] = $values->bid;
				$insert['category'] = $values->category;
				$insert['midx'] = $values->midx;
				$insert['password'] = $values->password;
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				$insert['is_notice'] = $values->is_notice;
				$insert['is_html_title'] = $values->is_html_title;
				$insert['is_secret'] = $values->is_secret;
				$insert['is_hidename'] = $values->is_hidename;
				
				if ($values->idx == null) {
					$post = null;
					
					$insert['name'] = $values->name;
					$insert['password'] = $values->password ? $mHash->password_hash($values->password) : '';
					$insert['email'] = $values->email;
					$insert['reg_date'] = $insert['last_ment'] = time();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
					
					if ($this->IM->getModule('member')->isLogged() == true) {
						$this->IM->getModule('member')->sendPoint(null,$values->board->post_point,'board','post',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->board->post_exp,'board','post',array('idx'=>$values->idx));
					}
				} else {
					$post = $this->getPost($values->idx);
					
					if ($this->checkPermission('post_modify') == false && ($post->midx != 0 && $post->midx != $this->IM->getModule('member')->getLogged())) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} elseif ($post->midx == 0) {
						if ($mHash->password_validate($values->password,$post->password) == false) {
							$results->success = false;
							$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
							$results->message = $this->getLanguage('error/incorrectPassword');
						}
					}
					
					if ($results->success == true) {
						if ($this->IM->getModule('member')->isLogged() == false) {
							$insert['name'] = $values->name;
							$insert['password'] = $values->password ? $mHash->password_hash($values->password) : '';
							$insert['email'] = $values->email;
							$insert['ip'] = $_SERVER['REMOTE_ADDR'];
						}
						
						$this->db()->update($this->table->post,$insert)->where('idx',$post->idx)->execute();
						
						if ($post->midx != 0 && $post->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($post->midx,'board','post_modify',$post->idx,array('from'=>$values->name));
						}
						
						if ($this->IM->getModule('member')->isLogged() == true) {
							$this->IM->getModule('member')->addActivity(null,0,'board','post_modify',array('idx'=>$values->idx));
						}
					}
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'bid'=>$values->bid,'type'=>'POST','parent'=>$values->idx))->execute();
						}
					}
					
					if ($post != null && $post->category != $values->category) {
						$lastPost = $this->db()->select($this->table->post)->where('category',$post->category)->orderBy('reg_date','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
						$this->db()->update($this->table->category,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$post->category)->execute();
					}
					
					if ($values->category != 0 && ($post == null || $post->category != $values->category)) {
						$lastPost = $this->db()->select($this->table->post)->where('category',$values->category)->orderBy('reg_date','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
						$this->db()->update($this->table->category,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$values->category)->execute();
					}
					
					$lastPost = $this->db()->select($this->table->post)->where('bid',$values->bid)->orderBy('reg_date','desc')->get();
					$postnum = count($lastPost);
					$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
					$this->db()->update($this->table->board,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('bid',$values->bid)->execute();
					
					$this->IM->setArticle('board',$values->bid,'post',$values->idx,time());
					
					$page = $this->IM->getPages($values->menu,$values->page);
					
					if ($page->context->config == null) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'view',$values->idx);
					} elseif ($page->context->config->category == $values->category) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'view',$values->idx);
					} else {
						$redirectPage = $this->getPostPage($values->idx);
						$results->redirect = $this->IM->getUrl($redirectPage->menu,$redirectPage->page,'view',$values->idx);
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'postModify') {
			$values->idx = Request('idx');
			$values->password = Request('password');
			$post = $this->getPost($values->idx);
			
			if ($post == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('post_modify') == true || ($post->midx != 0 && $post->midx == $this->IM->getModule('member')->getLogged())) {
				$results->success = true;
				$results->permission = true;
			} elseif ($post->midx == 0) {
				if ($values->password === null) {
					$results->success = true;
					$results->permission = false;
					$results->modalHtml = $this->getModify('post',$values->idx);
				} else {
					$mHash = new Hash();
					if ($mHash->password_validate($values->password,$post->password) == true) {
						$results->success = true;
						$results->permission = true;
					} else {
						$results->success = false;
						$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'postDelete') {
			$values->idx = Request('idx');
			$post = $this->getPost($values->idx);
			
			if ($post == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('post_delete') == true || $post->midx == 0 || $post->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->modalHtml = $this->getDelete('post',$values->idx);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'mentModify') {
			$values->idx = Request('idx');
			$values->password = Request('password');
			$ment = $this->getMent($values->idx);
			$results->permission = false;
			
			if ($ment == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('ment_modify') == true || ($ment->midx != 0 && $ment->midx == $this->IM->getModule('member')->getLogged())) {
				$results->success = true;
				$results->permission = true;
			} elseif ($ment->midx == 0) {
				if ($values->password === null) {
					$results->success = true;
					$results->permission = false;
					$results->modalHtml = $this->getModify('ment',$values->idx);
				} else {
					$mHash = new Hash();
					if ($mHash->password_validate($values->password,$ment->password) == true) {
						$results->success = true;
						$results->permission = true;
					} else {
						$results->success = false;
						$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
			
			if ($results->permission == true) {
				$ment->content = $this->getArticleContent($ment->content);
				$attachments = $this->db()->select($this->table->attachment)->where('parent',$ment->idx)->where('type','MENT')->get();
				for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
					$attachments[$i] = $attachments[$i]->idx;
				}
				$ment->attachment = Encoder(json_encode($attachments));
				$results->data = $ment;
			}
		}
		
		if ($action == 'getMent') {
			$values->get = Request('get');
			
			if ($values->get == 'page') {
				$values->parent = Request('parent');
				$values->post = $this->getPost($values->parent);
				$values->board = $this->getBoard($values->post->bid);
				$values->mentlimit = $values->board->mentlimit;
				
				$values->page = Request('page');
				$values->ments = $this->getMentPage($values->parent,$values->page,$values->mentlimit);
				
				if ($values->page > 1 && count($values->ments) == 0) {
					while ($values->page > 1) {
						$values->page = $values->page - 1;
						$values->ments = $this->getMentPage($values->parent,$values->page,$values->mentlimit);
						if (count($values->ments) > 0) break;
					}
				}
				
				if (count($values->ments) == 0) {
					$results->page = null;
					$results->mentHtml = '<div class="empty">'.$this->getLanguage('mentList/empty').'</div>';
				} else {
					$results->page = $values->page;
				}
			} elseif ($values->get == 'idx') {
				$values->idx = Request('idx');
				$ment = $this->getMent($values->idx);
				
				$values->parent = $ment->parent;
				if ($ment != null) {
					$values->page = $this->getMentPosition($values->idx);
					$values->post = $this->getPost($values->parent);
					$values->board = $this->getBoard($values->post->bid);
					$values->mentlimit = $values->board->mentlimit;
					
					$values->ments = $this->getMentPage($values->parent,$values->page,$values->mentlimit);
					$results->page = $values->page;
				} else {
					$results->page = null;
					$results->mentHtml = '<div class="empty">'.$this->getLanguage('mentList/empty').'</div>';
				}
			}
			
			$results->success = true;
			$results->parent = $values->parent;
			$results->mentCount = number_format($values->post->ment);
			$results->idxs = array();
			$results->ments = array();
			if (empty($values->mentHtml) == false) $results->mentHtml = $values->mentHtml;
			
			if ($results->page !== null) {
				for ($i=0, $loop=count($values->ments);$i<$loop;$i++) {
					$results->ments[$i] = array(
						'idx'=>$values->ments[$i]->idx,
						'modify_date'=>$values->ments[$i]->modify_date,
						'html'=>$this->getMentItem($values->ments[$i])
					);
					$results->idxs[$i] = $values->ments[$i]->idx;
				}
			}
			$results->pagination = $this->getMentPagination($results->parent,$results->page);
		}
		
		if ($action == 'getMentDepth') {
			$idx = Request('idx');
			$parent = $this->getMent($idx);
			if ($parent == null || $parent->is_delete == 'TRUE') {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($parent->depth >= 10) {
				$results->success = false;
				$results->message = $this->getLanguage('mentWrite/overdepth');
			} else {
				$results->success = true;
				$results->depth = $parent->depth;
				$results->parent = $parent->parent;
				$results->source = $idx;
			}
		}
		
		if ($action == 'mentWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->source = Request('source');
			$values->parent = Request('parent');
			$values->post = $this->getPost($values->parent);
			$values->board = $this->getBoard($values->post->bid);
			
			$values->is_secret = Request('is_secret') ? 'TRUE' : 'FALSE';
			$values->is_hidename = Request('is_hidename') && $this->IM->getModule('member')->isLogged() == true ? 'TRUE' : 'FALSE';
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$values->name = Request('name') ? Request('name') : $values->errors['name'] = $this->getLanguage('postWrite/help/name/error');
				$values->password = Request('password') ? Request('password') : $values->errors['password'] = $this->getLanguage('postWrite/help/password/error');
				$values->email = Request('email');
				$values->midx = 0;
			} else {
				$values->name = $this->IM->getModule('member')->getMember()->nickname;
				$values->password = '';
				$values->email = $this->IM->getModule('member')->getMember()->email;
				$values->midx = $this->IM->getModule('member')->getLogged();
			}
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$results->success = true;
			if ($values->source) {
				$sourceData = $this->getMent($values->source);
				if ($sourceData == null) {
					$results->success = false;
					$results->message = $this->getLanguage('mentWrite/deleteSource');
				}
			}
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if ($results->success == true && empty($values->errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['bid'] = $values->post->bid;
				$insert['parent'] = $values->parent;
				$insert['midx'] = $values->midx;
				$insert['password'] = $values->password;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				$insert['is_secret'] = $values->is_secret;
				$insert['is_hidename'] = $values->is_hidename;
				
				if ($values->idx == null) {
					$insert['name'] = $values->name;
					$insert['password'] = $values->password ? $mHash->password_hash($values->password) : '';
					$insert['email'] = $values->email;
					$insert['reg_date'] = time();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					
					$values->idx = $this->db()->insert($this->table->ment,$insert)->execute();
					if ($values->source) {
						$sourceData = $this->getMent($values->source);
						$head = $sourceData->head;
						$depth = $sourceData->depth + 1;
						$source = $sourceData->idx;
						
						if ($depth > 1) {
							$depthData = $this->db()->select($this->table->ment_depth)->where('head',$sourceData->head)->where('arrange',$sourceData->arrange,'>')->where('depth',$sourceData->depth,'<=')->orderBy('arrange','asc')->getOne();
							
							if ($depthData == null) {
								$arrange = $values->idx;
							} else {
								$arrange = $depthData->arrange;
								$this->db()->update($this->table->ment_depth,array('arrange'=>$this->db()->inc()))->where('head',$sourceData->head)->where('arrange',$arrange,'>=')->execute();
							}
						} else {
							$arrange = $values->idx;
						}
					} else {
						$head = $values->idx;
						$arrange = $values->idx;
						$depth = 0;
						$source = 0;
					}
					
					$this->db()->insert($this->table->ment_depth,array('idx'=>$values->idx,'parent'=>$values->parent,'head'=>$head,'arrange'=>$arrange,'depth'=>$depth,'source'=>$source))->execute();
					
					if ($this->IM->getModule('member')->isLogged() == true) {
						$this->IM->getModule('member')->sendPoint(null,$values->board->ment_point,'board','ment',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->board->ment_exp,'board','ment',array('idx'=>$values->idx));
					}
					
					if ($values->post->midx != 0 && $values->post->midx != $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($values->post->midx,'board','ment',$values->post->idx,array('idx'=>$values->idx,'from'=>($values->name)));
					}
					
					if ($source != 0 && $sourceData->midx != 0 && $sourceData->midx != $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($sourceData->midx,'board','replyment',$values->post->idx,array('idx'=>$values->idx,'from'=>($values->name)));
					}
				} else {
					$ment = $this->getMent($values->idx);
					
					if ($this->checkPermission('ment_modify') == false && ($ment->midx != 0 && $ment->midx != $this->IM->getModule('member')->getLogged())) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} elseif ($ment->midx == 0) {
						if ($mHash->password_validate($values->password,$ment->password) == false) {
							$results->success = false;
							$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
							$results->message = $this->getLanguage('error/incorrectPassword');
						}
					}
					
					if ($results->success == true) {
						if ($this->IM->getModule('member')->isLogged() == false) {
							$insert['name'] = $values->name;
							$insert['password'] = $values->password ? $mHash->password_hash($values->password) : '';
							$insert['email'] = $values->email;
							$insert['ip'] = $_SERVER['REMOTE_ADDR'];
						}
						$insert['modify_date'] = time();
						
						$this->db()->update($this->table->ment,$insert)->where('idx',$ment->idx)->execute();
						
						if ($ment->midx != 0 && $ment->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($ment->midx,'board','ment_modify',$values->idx,array('from'=>($values->name)));
						}
						
						if ($this->IM->getModule('member')->isLogged() == true) {
							$this->IM->getModule('member')->addActivity(null,0,'board','ment_modify',array('idx'=>$values->idx));
						}
					}
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'bid'=>$values->post->bid,'type'=>'MENT','parent'=>$values->idx))->execute();
						}
						$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
					}
					
					$lastMent = $this->db()->select($this->table->ment)->where('parent',$values->parent)->where('is_delete','FALSE')->orderBy('reg_date','desc')->get();
					$mentnum = count($lastMent);
					$lastMentTime = $mentnum > 0 ? $lastMent[0]->reg_date : $values->post->reg_date;
					$this->db()->update($this->table->post,array('ment'=>$mentnum,'last_ment'=>$lastMentTime))->where('idx',$values->parent)->execute();
					
					if ($values->post->is_secret != 'TRUE') {
						$this->IM->setArticle('board',$values->post->bid,'ment',$values->idx,time());
						$this->IM->setArticle('board',$values->post->bid,'post',$values->post->idx,time());
					}
					
					$results->message = $this->getLanguage('mentWrite/success');
					$results->idx = $values->idx;
					$results->parent = $values->parent;
					$results->page = $this->getMentPosition($values->idx);
				}
			} elseif (count($values->errors) > 0) {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'mentDelete') {
			$values->idx = Request('idx');
			$ment = $this->getMent($values->idx);
			
			if ($ment == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('ment_delete') == true || $ment->midx == 0 || $ment->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->modalHtml = $this->getDelete('ment',$values->idx);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'vote') {
			$values->type = in_array(Request('type'),array('post','ment')) == true ? Request('type') : 'post';
			$values->idx = Request('idx');
			$values->vote = in_array(Request('vote'),array('good','bad')) == true ? Request('vote') : 'good';
			$values->article = $this->getArticle($values->type,$values->idx);
			$values->board = $this->getBoard($values->article->bid);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success= false;
				$results->message = $this->getLanguage('error/notLogged');
			} else {
				$article = $this->db()->select($this->table->{$values->type})->where('idx',$values->idx)->getOne();
				
				if ($article == null) {
					$results->success= false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($article->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success= false;
					$results->message = $this->getLanguage('vote/mypost');
				} else {
					$check = $this->db()->select($this->table->history)->where('type',$values->type)->where('parent',$values->idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
					if ($check == null) {
						$this->db()->update($this->table->{$values->type},array($values->vote=>$this->db()->inc()))->where('idx',$values->idx)->execute();
						$this->db()->insert($this->table->history,array('type'=>strtoupper($values->type),'parent'=>$values->idx,'action'=>'VOTE','midx'=>$this->IM->getModule('member')->getLogged(),'result'=>strtoupper($values->vote),'reg_date'=>time()))->execute();
						$results->success = true;
						$results->message = $this->getLanguage('vote/'.$values->vote);
						$results->liveUpdate = 'liveUpdateBoard'.ucfirst($values->type).ucfirst($values->vote).$values->idx;
						$results->liveValue = number_format($values->vote + 1);
						
						if ($this->IM->getModule('member')->isLogged() == true) {
							$this->IM->getModule('member')->sendPoint(null,$values->board->vote_point,'board',$values->type.'_'.$values->vote,array('idx'=>$values->idx));
							$this->IM->getModule('member')->addActivity(null,$values->board->vote_exp,'board',$values->type.'_'.$values->vote,array('idx'=>$values->idx));
						}
						
						if ($article->midx != 0) {
							$this->IM->getModule('push')->sendPush($article->midx,'board',$values->type.'_'.$values->vote,$article->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('vote/duplicated');
						$results->result = $check->result;
					}
				}
			}
		}
		
		if ($action == 'delete') {
			$values->idx = Request('idx');
			$values->type = Request('type');
			
			if ($values->type == 'post') {
				$post = $this->getPost($values->idx);
				$values->board = $this->getBoard($post->bid);
				
				if ($post == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->checkPermission('post_delete') == true || ($post->midx != 0 && $post->midx == $this->IM->getModule('member')->getLogged())) {
					$results->success = true;
				} elseif ($post->midx == 0) {
					$values->password = Request('password');
					$mHash = new Hash();
					if ($mHash->password_validate($values->password,$post->password) == true) {
						$results->success = true;
					} else {
						$results->success = false;
						$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
					}
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				}
				
				if ($results->success == true) {
					$this->db()->delete($this->table->post)->where('idx',$post->idx)->execute();
					
					$attachments = $this->db()->select($this->table->attachment)->where('parent',$post->idx)->where('type','POST')->get();
					for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
						$attachments[$i] = $attachments[$i]->idx;
					}
					$this->IM->getModule('attachment')->fileDelete($attachments);
					
					$ments = $this->db()->select($this->table->ment)->where('parent',$post->idx)->get();
					for ($i=0, $loop=count($ments);$i<$loop;$i++) {
						$this->db()->delete($this->table->ment)->where('idx',$ments[$i]->idx)->execute();
						$this->db()->delete($this->table->ment_depth)->where('idx',$ments[$i]->idx)->execute();
						
						$attachments = $this->db()->select($this->table->attachment)->where('parent',$ments[$i]->idx)->where('type','MENT')->get();
						for ($j=0, $loopj=count($attachments);$j<$loopj;$j++) {
							$attachments[$j] = $attachments[$j]->idx;
						}
						$this->IM->getModule('attachment')->fileDelete($attachments);
					}
					
					if ($post->category != 0) {
						$lastPost = $this->db()->select($this->table->post)->where('category',$post->category)->orderBy('reg_date','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
						$this->db()->update($this->table->category,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$post->category)->execute();
					}
					
					if ($post->midx != 0) {
						$this->IM->getModule('member')->sendPoint($post->midx,$values->board->post_point * -1,'board','post_delete',array('title'=>$post->title),true);
						if ($post->midx == $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('member')->addActivity($post->midx,0,'board','post_delete',array('title'=>$post->title));
						} else {
							$this->IM->getModule('push')->sendPush($post->midx,'board','post_delete',$values->idx,array('title'=>$post->title));
						}
					}
					
					$this->IM->deleteArticle('board','post',$values->idx);
				}
			} elseif ($values->type == 'ment') {
				$ment = $this->getMent($values->idx);
				$post = $this->getPost($ment->parent);
				$values->board = $this->getBoard($post->bid);
				
				if ($ment == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->checkPermission('ment_delete') == true || ($ment->midx != 0 && $ment->midx == $this->IM->getModule('member')->getLogged())) {
					$results->success = true;
				} elseif ($ment->midx == 0) {
					$values->password = Request('password');
					$mHash = new Hash();
					if ($mHash->password_validate($values->password,$ment->password) == true) {
						$results->success = true;
					} else {
						$results->success = false;
						$results->errors = array('password'=>$this->getLanguage('error/incorrectPassword'));
					}
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				}
				
				if ($results->success == true) {
					if ($this->checkMentTree($values->idx) == false) {
						$this->db()->delete($this->table->ment)->where('idx',$values->idx)->execute();
						$this->db()->delete($this->table->ment_depth)->where('idx',$values->idx)->execute();
						
						if ($ment->source != 0) {
							$source = $this->getMent($ment->source);
							while ($source->is_delete == 'TRUE') {
								if ($this->checkMentTree($source->idx) == false) {
									$this->db()->delete($this->table->ment)->where('idx',$source->idx)->execute();
									$this->db()->delete($this->table->ment_depth)->where('idx',$source->idx)->execute();
									
									if ($source->source != 0) $source = $this->getMent($source->source);
									else break;
								} else {
									break;
								}
							}
						}
						$results->position = null;
					} else {
						$results->position = $values->idx;
						$this->db()->update($this->table->ment,array('is_delete'=>'TRUE','modify_date'=>time(),'content'=>'','search'=>''))->where('idx',$ment->idx)->execute();
					}
					
					$attachments = $this->db()->select($this->table->attachment)->where('parent',$ment->idx)->where('type','MENT')->get();
					for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
						$attachments[$i] = $attachments[$i]->idx;
					}
					$this->IM->getModule('attachment')->fileDelete($attachments);
					
					$lastMent = $this->db()->select($this->table->ment)->where('parent',$ment->parent)->where('is_delete','FALSE')->orderBy('reg_date','desc')->get();
					if (count($lastMent) == 0) {
						$this->db()->update($this->table->post,array('ment'=>0,'last_ment'=>$post->reg_date))->where('idx',$ment->parent)->execute();
						$results->parent = $ment->parent;
					} else {
						if ($results->position == null) {
							$position = $this->db()->select($this->table->ment_depth)->where('parent',$ment->parent)->where('head',$ment->head,'<=')->where('arrange',$ment->arrange,'<')->orderBy('head','asc')->orderBy('arrange','asc')->get();
							$lastPosition = array_pop($position);
							$results->position = $lastPosition->idx;
						}
						
						$this->db()->update($this->table->post,array('ment'=>count($lastMent),'last_ment'=>$lastMent[0]->reg_date))->where('idx',$ment->parent)->execute();
					}
					
					if ($ment->midx != 0) {
						$this->IM->getModule('member')->sendPoint($ment->midx,$values->board->ment_point * -1,'board','ment_delete',array('title'=>$post->title),true);
						if ($ment->midx == $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('member')->addActivity($ment->midx,0,'board','ment_delete',array('title'=>$post->title));
						} else {
							$this->IM->getModule('push')->sendPush($ment->midx,'board','ment_delete',$values->idx,array('title'=>$post->title));
						}
					}
					
					$this->IM->deleteArticle('board','ment',$values->idx);
					$results->message = $this->getLanguage('mentDelete/success');
				}
			}
			$results->type = $values->type;
		}
		
		$this->IM->fireEvent('afterDoProcess','board',$action,$values,$results);
		
		return $results;
	}
	
	function deleteAttachment($idx) {
		$this->db()->delete($this->table->attachment)->where('idx',$idx)->execute();
	}
	
	function resetArticle() {
		$posts = $this->db()->select($this->table->post)->get();
		for ($i=0, $loop=count($posts);$i<$loop;$i++) {
			$this->IM->setArticle('board',$posts[$i]->bid,'post',$posts[$i]->idx,$posts[$i]->last_ment);
		}
		
		$ments = $this->db()->select($this->table->ment)->where('is_delete','FALSE')->get();
		for ($i=0, $loop=count($ments);$i<$loop;$i++) {
			$this->IM->setArticle('board',$ments[$i]->bid,'ment',$ments[$i]->idx,$ments[$i]->modify_date != 0 ? $ments[$i]->modify_date : $ments[$i]->reg_date);
		}
	}
}
?>