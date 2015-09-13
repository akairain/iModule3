<?php
class ModuleForum {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $forumPages = array();
	private $postPages = array();
	private $labelUrls = array();
	
	private $forums = array();
	private $labels = array();
	private $posts = array();
	private $ments = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->forum = 'forum_table';
		$this->table->label = 'forum_label_table';
		$this->table->category = 'forum_category_table';
		$this->table->post = 'forum_post_table';
		$this->table->post_label = 'forum_post_label_table';
		$this->table->ment = 'forum_ment_table';
		$this->table->ment_depth = 'forum_ment_depth_table';
		$this->table->attachment = 'forum_attachment_table';
		$this->table->history = 'forum_history_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/forum.js');
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
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
	
	function getCountInfo($fid,$config) {
		$forum = $this->getForum($fid);
		if ($forum == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $forum->postnum;
			$info->last_time = $forum->last_post;
		} elseif (isset($config->label) == true) {
			$info->count = $this->getLabel($config->label)->postnum;
			$info->last_time = $this->getLabel($config->label)->last_post;
		}
		
		return $info;
	}
	
	function getContext($fid,$config=null) {
		$values = new stdClass();
		
		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		$forum = $this->getForum($fid);
		if ($forum == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($fid,$config);
		
		switch ($view) {
			case 'list' :
				$context.= $this->getListContext($fid,$config);
				break;
				
			case 'view' :
				$context.= $this->getViewContext($fid,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($fid,$config);
				break;
		}
		
		$context.= $this->getFooter($fid,$config);
		
		$this->IM->fireEvent('afterGetContext','forum',$view,null,null,$context);
		
		return $context;
	}
	
	function getForum($fid) {
		if (isset($this->forums[$fid]) == true) return $this->forums[$fid];
		$forum = $this->db()->select($this->table->forum)->where('fid',$fid)->getOne();
		if ($forum == null) {
			$this->forums[$fid] = null;
		} else {
			$forum->templetPath = $this->Module->getPath().'/templets/'.$forum->templet;
			$forum->templetDir = $this->Module->getDir().'/templets/'.$forum->templet;
			
			$this->forums[$fid] = $forum;
		}
		
		return $this->forums[$fid];
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
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		if ($code == 'replyment') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
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
		
		$push->content = str_replace('{count}','<b>'.$count.'</b>',$push->content);
		return $push;
	}
	
	function getLabel($label) {
		if (isset($this->labels[$label]) == true) return $this->labels[$label];
		
		$this->labels[$label] = $this->db()->select($this->table->label)->where('idx',$label)->getOne();
		
		return $this->labels[$label];
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getPostPage($idx,$domain=null) {
		if (isset($this->postPages[$idx]) == true) return $this->postPages[$idx];
		
		$post = $this->getPost($idx);
		$labels = $this->db()->select($this->table->post_label)->where('idx',$idx)->get();
		for ($i=0, $loop=count($labels);$i<$loop;$i++) $labels[$i] = $labels[$i]->label;
		
		if (count($labels) == 0) {
			$this->postPages[$idx] = $this->getForumPage($post->fid,null,$domain);
			return $this->postPages[$idx];
		}
		
		$this->postPages[$idx] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'forum' && $pages[$i]->context->context == $post->fid && $pages[$i]->context->config != null && in_array($pages[$i]->context->config->label,$labels) == true) {
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
		
		$this->postPages[$idx] = $this->getForumPage($post->fid);
		
		return $this->postPages[$idx];
	}
	
	function getForumPage($fid,$label=null,$domain=null) {
		if (isset($this->forumPages[$fid]) == true && $label == null) return $this->forumPages[$fid];
		
		$this->forumPages[$fid] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'forum' && $pages[$i]->context->context == $fid) {
					if ($label != null && $pages[$i]->context->config != null && $pages[$i]->context->config->label == $label) {
						return $pages[$i];
					}
					
					if ($label == null && $pages[$i]->context->config == null) {
						$this->forumPages[$fid] = $pages[$i];
						return $this->forumPages[$fid];
					}
					
					$this->forumPages[$fid] = $this->forumPages[$fid] == null ? $pages[$i] : $this->forumPages[$fid];
				}
			}
		}
		
		if ($domain === null && $this->forumPages[$fid] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getForumPage($fid,$label,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->forumPages[$fid] = $this->getForumPage($fid,$label,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		return $this->forumPages[$fid];
	}
	
	function getLabelUrl($idx) {
		if (isset($this->labelUrls[$idx]) == true) return $this->labelUrls[$idx];
		
		$label = $this->getLabel($idx);
		$forum = $this->getForum($label->fid);
		
		$page = null;
		$sitemap = $this->IM->getPages();
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module') {
					if ($pages[$i]->context->module == 'forum' && $pages[$i]->context->context == $forum->fid && $pages[$i]->context->config != null && isset($pages[$i]->context->config->label) == true && $pages[$i]->context->config->label == $idx) {
						$page = $pages[$i];
					}
				}
			}
		}
		
		if ($page == null) $isParams = true;
		else $isParams = false;
		
		if ($page == null) {
			$this->labelUrls[$idx] = $this->IM->getUrl(null,null,'list',1);
		} else {
			$this->labelUrls[$idx] = $this->IM->getUrl($page->menu,$page->page,'list',1);
		}
		
		if ($isParams == true) $this->labelUrls[$idx].= '?label='.$idx;
		
		return $this->labelUrls[$idx];
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
	
	function getHeader($fid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$forum = $this->getForum($fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($fid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$forum = $this->getForum($fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getLabels($idx) {
		return $this->db()->select($this->table->post_label.' p')->join($this->table->label.' l','p.label=l.idx','LEFT')->where('p.idx',$idx)->get();
	}
	
	function getArticle($type,$article,$isLink=false) {
		if ($type == 'post') {
			if (is_numeric($article) == true) $article = $this->getPost($article);
			$article->title = GetString($article->title,'replace');
			$article->is_image = preg_match('/<img(.*?)>/',$article->content);
			$article->is_file = $this->db()->select($this->table->attachment)->where('parent',$article->idx)->where('type','POST')->count() > 0;
			$article->is_link = preg_match('/<a(.*?)href(.*?)>/',$article->content);
			$article->is_video = preg_match('/<iframe(.*?)src=(.*?)(youtube|vimeo|daum|naver)(.*?)>/',$article->content);
			
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
		$article->name = $this->IM->getModule('member')->getMemberNickname($article->midx,true);
		$article->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($article->content)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
		$article->ip = $this->getArticleIp($article->ip);
		
		return $article;
	}
	
	function getArticleIp($ip) {
		$temp = explode('.',$ip);
		$temp[2] = '***';
		return implode('.',$temp);
	}
	
	function getArticleContent($content) {
		if (preg_match_all('/<img(.*?)data-idx="([0-9]+)"(.*?)>/',$content,$match) == true) {
			for ($i=0, $loop=count($match[0]);$i<$loop;$i++) {
				$file = $this->IM->getModule('attachment')->getFileInfo($match[2][$i]);
				$image = str_replace('data-idx="'.$match[2][$i].'"','data-idx="'.$match[2][$i].'" src="'.$this->IM->getModule('attachment')->getAttachmentUrl($match[2][$i],'view').'" alt="'.$file->name.'"',$match[0][$i]);
				$content = str_replace($match[0][$i],$image,$content);
			}
		}
		return $content;
	}
	
	function getPostPagination($idx,$config) {
		
	}
	
	function getError($content,$title='') {
		return $content;
	}
	
	function getAttachmentFile($file) {
		return $this->db()->select($this->table->attachment)->where('idx',$file->idx)->getOne();
	}
	
	function getListContext($fid,$config,$isView=false) {
		ob_start();
		
		$this->IM->setView('list');
		if ($isView == false) $this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$label = empty($config->label) == true ? Request('label') : $config->label;
		$category = empty($config->category) == true ? Request('category') : $config->category;
		
		$forum = $this->getForum($fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		if ($label == null) {
			$strQuery = $this->db()->select($this->table->post.' p','p.*')->where('p.fid',$fid);
		} else {
			$strQuery = $this->db()->select($this->table->post_label.' l','p.*, l.label')->join($this->table->post.' p','l.idx=p.idx','LEFT')->where('l.label',$label);
		}
		
		if ($category != null) {
			if ($forum->use_category == 'USEDALL') {
				$strQuery = $strQuery->where('p.category',array(0,$category),'IN');
			} else {
				$strQuery = $strQuery->where('p.category',$category);
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
			$prevPost = $strQuery->copy()->where('p.'.$sort,$post->$sort,$direction == 'desc' ? '>=' : '<=')->count();
			$p = ceil($prevPost/$forum->postlimit);
		} else {
			$idx = null;
			$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
			$p = $p < 1 ? 1 : $p;
		}
		
		$startPosition = ($p-1) * $forum->postlimit;
		
		$totalCount = $strQuery->copy()->count();
		$strQuery = $strQuery->orderBy('p.'.'idx','desc');
		$totalPage = ceil($totalCount/$forum->postlimit);
		$lists = $strQuery->limit($startPosition,$forum->postlimit)->get();
		
		$pagination = GetPagination($p,$totalPage,$forum->pagelimit,'LEFT');
		
		$loopnum = $totalCount - ($p - 1) * $forum->postlimit;
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i] = $this->getArticle('post',$lists[$i]);
			$lists[$i]->loopnum = $loopnum - $i;
			$lists[$i]->link = $this->IM->getUrl(null,null,'view',$lists[$i]->idx).$this->IM->getQueryString();
		}
		
		echo '<form name="ModuleForumListForm" onsubmit="return Forum.getListUrl(this);">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.$this->IM->page.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKey" value="'.$key.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKeyword" value="'.$keyword.'">'.PHP_EOL;
		echo '<input type="hidden" name="oCategory" value="'.$category.'">'.PHP_EOL;
		echo '<input type="hidden" name="oLabel" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="oSort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="oDirection" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="category" value="'.$category.'">'.PHP_EOL;
		echo '<input type="hidden" name="label" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="sort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="direction" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="p" value="'.$p.'">'.PHP_EOL;
		
		
		$values = new stdClass();
		$values->lists = $lists;
		$this->IM->fireEvent('afterInitContext','forum','list',$values);
		
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
	
	function getViewContext($fid,$config) {
		ob_start();
		
		$this->IM->setView('view');
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'nofollow'));
		$this->IM->setSiteCanonical($this->IM->getUrl(null,null,'view',$idx,true));
		$this->IM->setSiteTitle($post->title);
		$this->IM->setSiteDescription($post->search);
		
		if ($post->image != null) {
			$this->IM->setSiteImage($this->IM->getModule('attachment')->getAttachmentUrl($post->image->idx,'view'));
		}
		
		$forum = $this->getForum($fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
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
		$this->IM->fireEvent('afterInitContext','forum','view',$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.php') == true) {
			INCLUDE $templetPath.'/view.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		$context.= $this->getListContext($fid,$config,true);
		
		return $context;
	}
	
	function getDelete($type,$idx) {
		ob_start();
		
		if ($type == 'post') {
			$post = $this->getPost($idx);
			$forum = $this->getForum($post->fid);
		} else {
			$ment = $this->getMent($idx);
			$forum = $this->getForum($ment->fid);
		}
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		
		$title = $this->getLanguage($type.'Delete/title');
		echo '<form name="ModuleForumDeleteForm" onsubmit="return Forum.delete(this);">'.PHP_EOL;
		echo '<input type="hidden" name="action" value="delete">'.PHP_EOL;
		echo '<input type="hidden" name="type" value="'.$type.'">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		
		if ($type == 'post') {
			$content = '<div class="message">'.$this->getLanguage('postDelete/confirm').'</div>'.PHP_EOL;
		} else {
			$content = '<div class="message">'.$this->getLanguage('mentDelete/confirm').'</div>'.PHP_EOL;
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
	
	function getWriteContext($fid,$config) {
		ob_start();
		
		$this->IM->setView('write');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,nofollow'));
		
		$forum = $this->getForum($fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		if ($forum->use_label != 'NONE') {
			$labels = $this->db()->select($this->table->label)->where('fid',$fid)->orderBy('postnum','desc')->get();
		} else {
			$labels = array();
		}
		
		$default = new stdClass();
		
		$idx = Request('idx');
		if ($idx !== null) {
			$post = $this->getPost($idx);
			
			if ($post == null) {
				header("HTTP/1.1 404 Not Found");
				return $this->getError($this->getLangauge('error/notFound'));
			}
			
			if ($this->checkPermission('post_modify') == false && $post->midx != $this->IM->getModule('member')->getLogged()) {
				header("HTTP/1.1 403 Forfidden");
				return $this->getError($this->getLanguage('error/forbidden'));
			}
			
			if ($forum->use_label != 'NONE') {
				$post->labels = $this->db()->select($this->table->post_label)->where('idx',$post->idx)->get();
				for ($i=0, $loop=count($post->labels);$i<$loop;$i++) {
					$post->labels[$i] = $post->labels[$i]->label;
				}
			} else {
				$post->labels = array();
			}
			
			$post->content = $this->getArticleContent($post->content);
			
			$post->attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
			for ($i=0, $loop=count($post->attachments);$i<$loop;$i++) {
				$post->attachments[$i] = $post->attachments[$i]->idx;
			}
		} else {
			if (isset($config->label) == true) $default->label = $config->label;
			$post = null;
		}
		
		$formName = 'ModuleForumWriteForm-'.rand(10000,99999);
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Forum.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="fid" value="'.$fid.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($post !== null) echo '<input type="hidden" name="idx" value="'.$post->idx.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Forum.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$forum = $this->getForum($post->fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		echo '<div id="ModuleForumMentList-'.$parent.'" class="mentList">'.PHP_EOL;
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
		$forum = $this->getForum($post->fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		$totalMents = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$totalPage = ceil($totalMents/$forum->mentlimit) == 0 ? 1 : ceil($totalMents/$forum->mentlimit);
		
		$pagination = GetPagination($p == null ? $totalPage : $p,$totalPage,$forum->pagelimit,'LEFT','@Forum.ment.loadPage');
		
		echo '<div id="ModuleForumMentPagination-'.$parent.'" class="mentPagination" data-parent="'.$parent.'"'.($totalPage == 1 ? ' style="display:none;"' : '').'>'.PHP_EOL;
		
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
		
		$forum = $this->getForum($ment->fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		echo '<div id="ModuleForumMentItem-'.$ment->idx.'" data-idx="'.$ment->idx.'" data-parent="'.$ment->parent.'" data-modify="'.$ment->modify_date.'" class="mentItem ment'.($ment->depth == 0 ? 'Parent' : 'Child').'">'.PHP_EOL;
		
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
		$forum = $this->getForum($post->fid);
		$templetPath = $forum->templetPath;
		$templetDir = $forum->templetDir;
		
		echo '<div id="ModuleForumMentWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleForumMentForm-'.$parent.'" onsubmit="return Forum.ment.submit(this);">'.PHP_EOL;
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
		echo '<script>$(document).ready(function() { Forum.ment.init("ModuleForumMentForm-'.$parent.'"); });</script>';
		
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
		
		$forum = $this->getForum($post->fid);
		$totalCount = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$lastPage = $totalCount > 0 ? ceil($totalCount/$forum->mentlimit) : 1;
		
		return $this->getMentPage($parent,$lastPage,$forum->mentlimit);
	}
	
	function getMentPosition($idx) {
		$ment = $this->getMent($idx);
		if ($ment == null) return 0;
		
		$forum = $this->getForum($ment->fid);
		$position = $this->db()->select($this->table->ment_depth)->where('parent',$ment->parent)->where('head',$ment->head,'<=')->where('arrange',$ment->arrange,'<=')->count();
		$page = ceil($position/$forum->mentlimit);
		
		return $page;
	}
	
	function getWysiwyg($name) {
		$wysiwyg = $this->IM->getModule('wysiwyg')->setName($name)->setModule('forum');
		
		return $wysiwyg;
	}
	
	function getOptionForm($language,$name,$value=null,$type=null) {
		$id = $name.'-'.uniqid();
		
		$disabled = false;
		switch ($name) {
			case 'is_ment' :
				$value = $value === null ? true : $value;
				break;
				
			case 'is_reply' :
				$value = $value === null ? true : $value;
				break;
			
			case 'is_push' :
				$value = $value === null ? true : $value;
				if ($this->IM->getModule('member')->isLogged() == false) $disabled = true;
				break;
			
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
			$oLabel = Request('oLabel');
			$oSort = Request('oSort');
			$oDirection = Request('oDirection');
			
			$key = Request('key');
			$keyword = Request('keyword');
			$label = Request('label');
			$sort = Request('sort');
			$direction = Request('direction');
			
			$key = Request('key');
			$keyword = Request('keyword');
			$p = Request('p') ? Request('p') : 1;
			
			$queryString = 'menu='.$page->menu.'&page='.$page->page.'&key='.$key.'&keyword='.$keyword.'&label='.$label.'&sort='.$sort.'&direction='.$direction.'&p='.$p;
			
			if ($oKey != $key || $oKeyword != $keyword || $oLabel != $label || $oSort != $sort || $oDirection != $direction) $p = 1;
			
			$default = array();
			if (strlen($keyword) == 0) {
				$default['key'] = '';
				$default['keyword'] = '';
			}
			if ($sort == 'idx' && $direction == 'desc') $default['sort'] = $default['direction'] = '';
			if (isset($page->context->config->label) == true && $page->context->config->label == $label) $default['label'] = '';
			if (isset($page->context->config->category) == true && $page->context->config->category == $category) $default['category'] = '';
			
			$url = $this->IM->getUrl($page->menu,$page->page,'list',$p).$this->IM->getQueryString($default,$queryString);
			
			$results->success = true;
			$results->url = $url;
		}
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->fid = Request('fid');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			$values->forum = $this->getForum($values->fid);
			if ($values->forum->use_label != 'NONE') {
				$values->labels = is_array(Request('labels')) == true ? Request('labels') : array();
				if ($values->forum->use_label == 'FORCE' && count($values->labels) == 0) {
					$values->errors['labels'] = $this->getLanguage('postWrite/help/labels/error');
				}
			} else {
				$values->labels = array();
			}
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['fid'] = $values->fid;
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				
				if ($values->idx == null) {
					$post = null;
					$reg_date = time();
					
					$insert['reg_date'] = $reg_date;
					$insert['last_ment'] = $reg_date;
					$insert['last_ment_midx'] = $insert['midx'];
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
					
					$this->IM->getModule('member')->sendPoint(null,$values->forum->post_point,'forum','post',array('idx'=>$values->idx));
					$this->IM->getModule('member')->addActivity(null,$values->forum->post_exp,'forum','post',array('idx'=>$values->idx));
				} else {
					$post = $this->getPost($values->idx);
					$reg_date = $post->last_ment;
					
					if ($this->checkPermission('post_modify') == false && $post->midx != $this->IM->getModule('member')->getLogged()) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					}
					
					if ($results->success == true) {
						$this->db()->update($this->table->post,$insert)->where('idx',$post->idx)->execute();
						if ($post->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($post->midx,'forum','post_modify',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
						$this->IM->getModule('member')->addActivity(null,0,'forum','post_modify',array('idx'=>$values->idx));
					}
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'fid'=>$values->fid,'type'=>'POST','parent'=>$values->idx))->execute();
						}
						$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
					}
					
					$labels = $this->db()->select($this->table->post_label)->where('idx',$values->idx)->get();
					for ($i=0, $loop=count($labels);$i<$loop;$i++) {
						if (in_array($labels[$i]->label,$values->labels) == false) {
							$this->db()->delete($this->table->post_label)->where('idx',$values->idx)->where('label',$labels[$i]->label)->execute();
							
							$lastPost = $this->db()->select($this->table->post_label)->where('label',$labels[$i]->label)->orderBy('reg_date','desc')->get();
							$postnum = count($lastPost);
							$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
							$this->db()->update($this->table->label,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$labels[$i]->label)->execute();
						}
					}
					
					if (count($values->labels) > 0) {
						for ($i=0, $loop=count($values->labels);$i<$loop;$i++) {
							if ($this->db()->select($this->table->post_label)->where('idx',$values->idx)->where('label',$values->labels[$i])->count() == 0) {
								$this->db()->insert($this->table->post_label,array('idx'=>$values->idx,'label'=>$values->labels[$i],'reg_date'=>$reg_date))->execute();
								
								$lastPost = $this->db()->select($this->table->post_label)->where('label',$values->labels[$i])->orderBy('reg_date','desc')->get();
								$postnum = count($lastPost);
								$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
								$this->db()->update($this->table->label,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$values->labels[$i])->execute();
							}
						}
					}
					
					$lastPost = $this->db()->select($this->table->post)->where('fid',$values->fid)->orderBy('last_ment','desc')->get();
					$postnum = count($lastPost);
					$lastPostTime = $postnum > 0 ? $lastPost[0]->last_ment : 0;
					$this->db()->update($this->table->forum,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('fid',$values->fid)->execute();
					
					$this->IM->setArticle('forum',$values->fid,'post',$values->idx,time());
					$page = $this->IM->getPages($values->menu,$values->page);
					
					if ($page->context->config == null) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'view',$values->idx);
					} elseif (in_array($page->context->config->label,$values->labels) == true) {
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
			$post = $this->getPost($values->idx);
			
			if ($post == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('post_modify') == true || $post->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->permission = true;
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
			} elseif ($this->checkPermission('post_delete') == true || $post->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->modalHtml = $this->getDelete('post',$values->idx);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'mentWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->source = Request('source');
			$values->parent = Request('parent');
			$values->post = $this->getPost($values->parent);
			$values->forum = $this->getForum($values->post->fid);
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
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
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($results->success == true && empty($values->errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['fid'] = $values->post->fid;
				$insert['parent'] = $values->parent;
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				
				if ($values->idx == null) {
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
					
					if ($values->post->midx != $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($values->post->midx,'forum','ment',$values->post->idx,array('idx'=>$values->idx,'from'=>$this->IM->getModule('member')->getLogged()));
					}
					
					if ($source != 0 && $sourceData->midx != $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($sourceData->midx,'forum','replyment',$values->post->idx,array('idx'=>$values->idx,'from'=>$this->IM->getModule('member')->getLogged()));
					}
					
					$this->IM->getModule('member')->sendPoint(null,$values->forum->ment_point,'forum','ment',array('idx'=>$values->idx));
					$this->IM->getModule('member')->addActivity(null,$values->forum->ment_exp,'forum','ment',array('idx'=>$values->idx));
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
						
						if ($ment->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($ment->midx,'forum','ment_modify',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
						$this->IM->getModule('member')->addActivity(null,0,'forum','ment',array('idx'=>$values->idx));
					}
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'fid'=>$values->post->fid,'type'=>'MENT','parent'=>$values->idx))->execute();
						}
					}
					
					$lastMent = $this->db()->select($this->table->ment)->where('parent',$values->parent)->where('is_delete','FALSE')->orderBy('reg_date','desc')->get();
					$mentnum = count($lastMent);
					$lastMentTime = $mentnum > 0 ? $lastMent[0]->reg_date : $values->post->reg_date;
					$this->db()->update($this->table->post,array('ment'=>$mentnum,'last_ment'=>$lastMentTime))->where('idx',$values->parent)->execute();
					
					$this->IM->setArticle('forum',$values->post->fid,'post',$values->parent,time());
					$this->IM->setArticle('forum',$values->post->fid,'ment',$values->idx,time());
					
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
		
		if ($action == 'mentModify') {
			$values->idx = Request('idx');
			$ment = $this->getMent($values->idx);
			
			if ($ment == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('ment_modify') == true || $ment->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
			
			if ($results->success == true) {
				$ment->content = $this->getArticleContent($ment->content);
				$attachments = $this->db()->select($this->table->attachment)->where('parent',$ment->idx)->where('type','MENT')->get();
				for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
					$attachments[$i] = $attachments[$i]->idx;
				}
				$ment->attachment = Encoder(json_encode($attachments));
				$results->data = $ment;
			}
		}
		
		if ($action == 'vote') {
			$values->idx = Request('idx');
			$values->vote = in_array(Request('vote'),array('good','bad')) == true ? Request('vote') : 'good';
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success= false;
				$results->message = $this->getLanguage('error/notLogged');
			} else {
				$values->post = $this->getPost($values->idx);
				
				if ($values->post == null) {
					$results->success= false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success= false;
					$results->message = $this->getLanguage('vote/mypost');
				} else {
					$values->forum = $this->getForum($values->post->fid);
					$check = $this->db()->select($this->table->history)->where('parent',$values->idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
					if ($check == null) {
						if ($values->vote == 'good') {
							$this->db()->update($this->table->post,array('vote'=>$this->db()->inc()))->where('idx',$values->idx)->execute();
						} else {
							$this->db()->update($this->table->post,array('vote'=>$this->db()->dec()))->where('idx',$values->idx)->execute();
						}
						$this->db()->insert($this->table->history,array('type'=>'POST','parent'=>$values->idx,'action'=>'VOTE','midx'=>$this->IM->getModule('member')->getLogged(),'result'=>strtoupper($values->vote),'reg_date'=>time()))->execute();
						$results->success = true;
						$results->message = $this->getLanguage('vote/'.$values->vote);
						$results->liveUpdate = 'liveUpdateForumVote'.$values->idx;
						$results->liveValue = number_format($values->vote == 'good' ? $values->post->vote + 1 : $values->post->vote - 1);
						
						$this->IM->getModule('member')->sendPoint(null,$values->forum->vote_point,'forum','post_'.$values->vote,array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->forum->vote_exp,'forum','post_'.$values->vote,array('idx'=>$values->idx));
						$this->IM->getModule('push')->sendPush($values->post->midx,'forum','post_'.$values->vote,$values->post->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('vote/duplicated');
						$results->result = $check->result;
					}
				}
			}
		}
		
		if ($action == 'getMent') {
			$values->get = Request('get');
			
			if ($values->get == 'page') {
				$values->parent = Request('parent');
				$values->post = $this->getPost($values->parent);
				$values->forum = $this->getForum($values->post->fid);
				$values->mentlimit = $values->forum->mentlimit;
				
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
					$values->forum = $this->getForum($values->post->fid);
					$values->mentlimit = $values->forum->mentlimit;
					
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
		
		if ($action == 'mentDelete') {
			$values->idx = Request('idx');
			$ment = $this->getMent($values->idx);
			
			if ($ment == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->checkPermission('ment_delete') == true || $ment->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->modalHtml = $this->getDelete('ment',$values->idx);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'delete') {
			$values->idx = Request('idx');
			$values->type = Request('type');
			
			if ($values->type == 'post') {
				$post = $this->getPost($values->idx);
				$values->forum = $this->getForum($post->fid);
				
				if ($post == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->checkPermission('post_delete') == true || $post->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = true;
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
					
					$labels = $this->db()->select($this->table->post_label)->where('idx',$post->idx)->get();
					for ($i=0, $loop=count($labels);$i<$loop;$i++) {
						$this->db()->delete($this->table->post_label)->where('idx',$values->idx)->where('label',$labels[$i]->label)->execute();
							
						$lastPost = $this->db()->select($this->table->post_label)->where('label',$labels[$i]->label)->orderBy('reg_date','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
						$this->db()->update($this->table->label,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$labels[$i]->label)->execute();
					}
					
					$this->IM->getModule('member')->sendPoint($post->midx,$values->forum->ment_point * -1,'forum','post_delete',array('title'=>$post->title),true);
					if ($post->midx == $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('member')->addActivity($post->midx,0,'forum','post_delete',array('title'=>$post->title));
					} else {
						$this->IM->getModule('push')->sendPush($post->midx,'forum','post_delete',$values->idx,array('title'=>$post->title));
					}
					
					$this->IM->deleteArticle('forum','post',$values->idx);
				}
			} elseif ($values->type == 'ment') {
				$ment = $this->getMent($values->idx);
				$post = $this->getPost($ment->parent);
				$values->forum = $this->getForum($post->fid);
				
				if ($ment == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->checkPermission('ment_delete') == true || $ment->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = true;
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
					$results->message = $this->getLanguage('mentDelete/success');
					
					$this->IM->getModule('member')->sendPoint($ment->midx,$values->forum->ment_point * -1,'forum','ment_delete',array('title'=>$post->title),true);
					if ($ment->midx == $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('member')->addActivity($ment->midx,0,'forum','ment_delete',array('title'=>$post->title));
					} else {
						$this->IM->getModule('push')->sendPush($ment->midx,'forum','ment_delete',$values->idx,array('title'=>$post->title));
					}
					
					$this->IM->deleteArticle('forum','ment',$values->idx);
				}
			}
			$results->type = $values->type;
		}
		
		$this->IM->fireEvent('afterDoProcess','forum',$action,$values,$results);
		
		return $results;
	}
	
	function deleteAttachment($idx) {
		$this->db()->delete($this->table->attachment)->where('idx',$idx)->execute();
	}
	
	function resetArticle() {
		$posts = $this->db()->select($this->table->post)->get();
		for ($i=0, $loop=count($posts);$i<$loop;$i++) {
			$this->IM->setArticle('forum',$posts[$i]->fid,'post',$posts[$i]->idx,$posts[$i]->last_ment);
		}
		
		$ments = $this->db()->select($this->table->ment)->where('is_delete','FALSE')->get();
		for ($i=0, $loop=count($ments);$i<$loop;$i++) {
			$this->IM->setArticle('forum',$ments[$i]->fid,'ment',$ments[$i]->idx,$ments[$i]->modify_date != 0 ? $ments[$i]->modify_date : $ments[$i]->reg_date);
		}
	}
}
?>