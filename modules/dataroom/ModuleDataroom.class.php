<?php
class ModuleDataroom {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	
	private $dataroomPages = array();
	private $postPages = array();
	
	private $datarooms = array();
	private $categorys = array();
	private $posts = array();
	private $ments = array();

	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->dataroom = 'dataroom_table';
		$this->table->category = 'dataroom_category_table';
		$this->table->post = 'dataroom_post_table';
		$this->table->post_version = 'dataroom_post_version_table';
		$this->table->ment = 'dataroom_ment_table';
		$this->table->ment_depth = 'dataroom_ment_depth_table';
		$this->table->question = 'dataroom_question_table';
		$this->table->answer = 'dataroom_answer_table';
		$this->table->attachment = 'dataroom_attachment_table';
		$this->table->history = 'dataroom_history_table';
		$this->table->purchase = 'dataroom_purchase_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/dataroom.js');
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
	
	function getCountInfo($did,$config) {
		$dataroom = $this->getDataroom($did);
		if ($dataroom == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $dataroom->postnum;
			$info->last_time = $dataroom->last_post;
		} elseif (isset($config->category) == true) {
			$info->count = $this->getCategory($config->category)->postnum;
			$info->last_time = $this->getCategory($config->category)->last_post;
		}
		
		return $info;
	}
	
	function getContext($did,$config=null) {
		$context = '';
		$values = new stdClass();
		
		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		$dataroom = $this->getDataroom($did);
		if ($dataroom == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($did,$config);
		
		switch ($view) {
			case 'list' :
				$context.= $this->getListContext($did,$config);
				break;
				
			case 'view' :
				$context.= $this->getViewContext($did,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($did,$config);
				break;
				
			case 'version' :
				$context.= $this->getVersionWriteContext($did,$config);
				break;
		}
		
		$context.= $this->getFooter($did,$config);
		
		$this->IM->fireEvent('afterGetContext','dataroom',$view,null,null,$context);
		
		return $context;
	}
	
	function getDataroom($did) {
		if (isset($this->datarooms[$did]) == true) return $this->datarooms[$did];
		$dataroom = $this->db()->select($this->table->dataroom)->where('did',$did)->getOne();
		if ($dataroom == null) {
			$this->datarooms[$did] = null;
		} else {
			$dataroom->templetPath = $this->Module->getPath().'/templets/'.$dataroom->templet;
			$dataroom->templetDir = $this->Module->getDir().'/templets/'.$dataroom->templet;
			
			$this->datarooms[$did] = $dataroom;
		}
		
		return $this->datarooms[$did];
	}
	
	function getPush($code,$fromcode,$content) {
		$latest = array_pop($content);
		$count = count($content);
		
		$push = new stdClass();
		$push->image = null;
		$push->link = null;
		if ($count > 0) $push->content = $this->getLanguage('push/'.$code.'s');
		else $push->content = $this->getLanguage('push/'.$code);
		
		if ($code == 'question') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
		if ($code == 'answer') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$question = $this->db()->select($this->table->question)->where('idx',$fromcode)->getOne();
			$title = GetCutString($question->title,15);
			$post = $this->getPost($question->parent);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx,false,$page->domain);
		}
		
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
			$this->postPages[$idx] = $this->getDataroomPage($post->did,null,$domain);
			return $this->postPages[$idx];
		}
		
		$this->postPages[$idx] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'dataroom' && $pages[$i]->context->context == $post->did && $pages[$i]->context->config != null && $pages[$i]->context->config->category == $post->category) {
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
		
		$this->postPages[$idx] = $this->getDataroomPage($post->did);
		
		return $this->postPages[$idx];
	}
	
	function getDataroomPage($did,$category=null,$domain=null) {
		if (isset($this->dataroomPages[$did]) == true && $category == null) return $this->dataroomPages[$did];
		
		$this->dataroomPages[$did] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'dataroom' && $pages[$i]->context->context == $did) {
					if ($category != null && $pages[$i]->context->config != null && $pages[$i]->context->config->category == $category) {
						return $pages[$i];
					}
					
					if ($category == null && $pages[$i]->context->config == null) {
						$this->dataroomPages[$did] = $pages[$i];
						return $this->dataroomPages[$did];
					}
					
					$this->dataroomPages[$did] = $this->dataroomPages[$did] == null ? $pages[$i] : $this->dataroomPages[$did];
				}
			}
		}
		
		if ($domain === null && $this->dataroomPages[$did] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getDataroomPage($did,$category,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->dataroomPages[$did] = $this->getDataroomPage($did,$category,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		return $this->dataroomPages[$did];
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
	
	function getVersions($idx) {
		$versions = $this->db()->select($this->table->post_version)->where('parent',$idx)->orderBy('reg_date','desc')->get();
		for ($i=0, $loop=count($versions);$i<$loop;$i++) {
			$versions[$i]->file = $this->IM->getModule('attachment')->getFileInfo($versions[$i]->file);
			$versions[$i]->history = explode("\n",preg_replace('/\[(NEW|UPDATE|BUGFIX)\]/','<span class="\1">\1</span>',$versions[$i]->history));
		}
		
		return $versions;
	}
	
	function getPurchaseData($idx,$midx=null) {
		$member = $this->IM->getModule('member')->getMember($midx);
		if ($member == null) return null;
		
		return $this->db()->select($this->table->purchase)->where('parent',$idx)->where('midx',$member->idx)->getOne();
	}
	
	function getHeader($did,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$board = $this->getDataroom($did);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($did,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$board = $this->getDataroom($did);
		$templetPath = $board->templetPath;
		$templetDir = $board->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getArticle($type,$article,$isLink=false) {
		if ($type == 'post') {
			if (is_numeric($article) == true) $article = $this->getPost($article);
			$article->title = GetString($article->title,'replace');
			$article->logo = $article->logo == 0 ? null : $this->IM->getModule('attachment')->getFileInfo($article->logo);
			$article->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($article->content)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->idx);
				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->idx,false,$page->domain,$page->language);
			}
		} else {
			if (is_numeric($article) == true) $article = $this->getMent($article);
			if (empty($this->ments[$article->idx]) == true) $this->ments[$article->idx] = $article;
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->parent);
				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->parent,false,$page->domain,$page->language);
			}
		}
		
		$article->member = $this->IM->getModule('member')->getMember($article->midx);
		$article->name = $this->IM->getModule('member')->getMemberNickname($article->midx);
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
	
	function getError($content,$title='') {
		return $content;
	}
	
	function getAttachmentFile($file) {
		if ($file->target == 'logo') return $this->db()->select($this->table->post)->where('logo',$file->idx)->getOne();
		elseif ($file->target == 'file') return $this->db()->select($this->table->post_version)->where('file',$file->idx)->getOne();
		else return $this->db()->select($this->table->attachment)->where('idx',$file->idx)->getOne();
	}
	
	function getListContext($did,$config) {
		ob_start();
		
		$this->IM->setView('list');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$category = empty($config->category) == true ? Request('category') : $config->category;
		
		$dataroom = $this->getDataroom($did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$strQuery = $this->db()->select($this->table->post.' p')->where('p.did',$did);
		if ($category != null) {
			$strQuery = $strQuery->where('p.category',$category);
		}
		
		$keyword = Request('keyword');
		
		if ($keyword != null && strlen($keyword) > 0) {
			$strQuery = $strQuery->where('p.title,p.search',$keyword,'FULLTEXT');
		}
		
		$sort = Request('sort') ? Request('sort') : 'update';
		
		if ($sort == 'mypost') {
			$strQuery->where('p.midx',$this->IM->getModule('member')->getLogged());
		} elseif ($sort == 'purchase') {
			$strQuery->join($this->table->purchase.' m','p.idx=m.parent','LEFT')->where('m.midx',$this->IM->getModule('member')->getLogged());
		}
		
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$startPosition = ($p-1) * $dataroom->postlimit;
		
		$totalCount = $strQuery->copy()->count();
		if ($sort == 'purchase') {
			$strQuery = $strQuery->orderBy('m.reg_date','desc');
		} elseif ($sort == 'download') {
			$strQuery = $strQuery->orderBy('p.download','desc');
		} elseif ($sort == 'mypost') {
			$strQuery = $strQuery->orderBy('p.idx','desc');
		} else {
			$strQuery = $strQuery->orderBy('p.last_update','desc');
			$sort = 'update';
		}
		$totalPage = ceil($totalCount/$dataroom->postlimit);
		$lists = $strQuery->limit($startPosition,$dataroom->postlimit)->get();
		
		$pagination = GetPagination($p,$totalPage,$dataroom->pagelimit,'LEFT');
		
		$loopnum = $totalCount - ($p - 1) * $dataroom->postlimit;
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i] = $this->getArticle('post',$lists[$i]);
			$lists[$i]->loopnum = $loopnum - $i;
			$lists[$i]->link = $this->IM->getUrl(null,null,'view',$lists[$i]->idx).$this->IM->getQueryString();
		}
		
		echo '<form name="ModuleDataroomListForm" onsubmit="return Dataroom.getListUrl(this);">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.$this->IM->page.'">'.PHP_EOL;

		echo '<input type="hidden" name="oKeyword" value="'.$keyword.'">'.PHP_EOL;
		echo '<input type="hidden" name="oCategory" value="'.$category.'">'.PHP_EOL;

		echo '<input type="hidden" name="oSort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="category" value="'.$category.'">'.PHP_EOL;

		echo '<input type="hidden" name="sort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="p" value="'.$p.'">'.PHP_EOL;
		
		
		$values = new stdClass();
		$values->lists = $lists;
		$this->IM->fireEvent('afterInitContext','dataroom','list',$values);
		
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
	
	function getViewContext($did,$config) {
		ob_start();
		
		$this->IM->setView('view');
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		$this->IM->setSiteCanonical($this->IM->getUrl(null,null,'view',$idx,true));
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'nofollow'));
		$this->IM->setSiteTitle($post->title);
		$this->IM->setSiteDescription($post->search);
		
		if ($post->logo != null) {
			$this->IM->setSiteImage($this->IM->getModule('attachment')->getAttachmentUrl($post->logo->idx,'view'));
		}
		
		$dataroom = $this->getDataroom($did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$this->db()->update($this->table->post,array('hit'=>$this->db()->inc()))->where('idx',$idx)->execute();
		
		if ($this->IM->getModule('member')->isLogged() == true) {
			$vote = $this->db()->select($this->table->history)->where('parent',$idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
			$voted = $vote == null ? null : $vote->result;
		} else {
			$voted = null;
		}
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$versions = $this->getVersions($idx);
		$purchase = $this->getPurchaseData($idx);
		
		$values = new stdClass();
		$values->post = $post;
		$values->attachments = $attachments;
		$this->IM->fireEvent('afterInitContext','dataroom','view',$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.php') == true) {
			INCLUDE $templetPath.'/view.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWriteContext($did,$config) {
		ob_start();
		
		$this->IM->setView('write');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$dataroom = $this->getDataroom($did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		if ($this->IM->getModule('member')->isLogged() == false) {
			return $this->getError($this->getLanguage('error/notLogged'));
		}
		
		if ($this->checkPermission('write') == false) {
			return $this->getError($this->getLanguage('error/forbidden'));
		}
		
		if ($dataroom->use_category == 'USED') {
			$page = $this->IM->getPages($this->IM->menu,$this->IM->page);
			if (empty($config->category) == true) {
				$categorys = $this->db()->select($this->table->category)->where('did',$did)->orderBy('sort','asc')->get();
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
			
			if ($this->checkPermission('post_modify') == false && $post->midx != $this->IM->getModule('member')->getLogged()) {
				header("HTTP/1.1 403 Forbidden");
				return $this->getError($this->getLanguage('error/forbidden'));
			}
			
			$post->content = $this->getArticleContent($post->content);
			
			$post->attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
			for ($i=0, $loop=count($post->attachments);$i<$loop;$i++) {
				$post->attachments[$i] = $post->attachments[$i]->idx;
			}
			
			$post->logo = $post->logo == 0 ? null : $this->IM->getModule('attachment')->getFileInfo($post->logo);
		} else {
			if (isset($config->category) == true) $default->category = $config->category;
			$post = null;
		}
		
		$formName = 'ModuleDataroomWriteForm-'.$did;
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Dataroom.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="did" value="'.$did.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($post !== null) echo '<input type="hidden" name="idx" value="'.$post->idx.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Dataroom.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getVersionWriteContext($did,$config) {
		ob_start();
		
		$this->IM->setView('version');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$dataroom = $this->getDataroom($did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		
		if ($post == null) {
			header("HTTP/1.1 404 Not Found");
			return $this->getError($this->getLangauge('error/notFound'));
		}
		
		if ($this->checkPermission('write') == false && $post->midx != $this->IM->getModule('member')->getLogged()) {
			header("HTTP/1.1 403 Forbidden");
			return $this->getError($this->getLanguage('error/forbidden'));
		}
		
		$formName = 'ModuleDataroomWriteForm-'.rand(10000,99999);
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Dataroom.version.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="did" value="'.$did.'">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$idx.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/version.php') == true) {
			INCLUDE $templetPath.'/version.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Dataroom.version.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getDownload($idx,$version) {
		ob_start();
		
		$post = $this->getPost($idx);
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$title = $this->getLanguage('download/download_'.($post->price == 0 ? 'free' : 'buy'));
		
		echo '<form name="ModuleDataroomModalForm" onsubmit="return Dataroom.download('.$idx.',\''.$version.'\',true);">'.PHP_EOL;
		
		$content = '<div class="message">';
		$content.= '<div class="title">'.$post->title.'</div>';
		if ($post->price > 0 ) $content.= '<div class="price"><i class="fa fa-rub"></i> '.number_format($post->price).'</div>';
		$content.= $this->getLanguage('download/confirm_'.($post->price == 0 ? 'free' : 'buy'));
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
			$dataroom = $this->getDataroom($post->did);
		} elseif ($type == 'ment') {
			$ment = $this->getMent($idx);
			$dataroom = $this->getDataroom($ment->did);
		} elseif ($type == 'answer') {
			$answer = $this->db()->select($this->table->answer)->where('idx',$idx)->getOne();
			$dataroom = $this->getDataroom($answer->did);
		}
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$title = $this->getLanguage($type.'Delete/title');
		
		echo '<form name="ModuleDataroomDeleteForm" onsubmit="return Dataroom.delete(this);">'.PHP_EOL;
		echo '<input type="hidden" name="action" value="delete">'.PHP_EOL;
		echo '<input type="hidden" name="type" value="'.$type.'">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage($type.'Delete/confirm').'</div>'.PHP_EOL;
		
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
	
	function getQnaList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomQnaList-'.$parent.'" class="qnaList" data-parent="'.$parent.'">'.PHP_EOL;
		$lists = $this->getQnaPage($parent,1,$dataroom->qnalimit);
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			echo $this->getQnaItem($lists[$i]);
		}
		
		if (count($lists) == 0) echo '<div class="empty">'.$this->getLanguage('qnaList/empty').'</div>'.PHP_EOL;
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaPagination($parent,$p=null) {
		ob_start();
		
		$post = $this->getPost($parent);
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$totalQnas = $this->db()->select($this->table->question)->where('parent',$parent)->count();
		$totalPage = ceil($totalQnas/$dataroom->qnalimit) == 0 ? 1 : ceil($totalQnas/$dataroom->qnalimit);
		
		$pagination = GetPagination($p == null ? 1 : $p,$totalPage,$dataroom->pagelimit,'LEFT','@Dataroom.qna.loadPage');
		
		echo '<div id="ModuleDataroomQnaPagination-'.$parent.'" class="qnaPagination" data-parent="'.$parent.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/qna.pagination.php') == true) {
			INCLUDE $templetPath.'/qna.pagination.php';
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaItem($qna) {
		ob_start();
		
		$dataroom = $this->getDataroom($qna->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomQnaItem-'.$qna->idx.'" class="qnaItem" data-idx="'.$qna->idx.'" data-parent="'.$qna->parent.'">'.PHP_EOL;
		
		$qna = $this->getArticle('qna',$qna);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/qna.item.php') == true) {
			INCLUDE $templetPath.'/qna.item.php';
		}
		
		echo '<div id="ModuleDataroomQnaView-'.$qna->idx.'" class="qnaView" style="display:none;"></div>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaView($idx) {
		ob_start();
		
		$qna = $this->db()->select($this->table->question)->where('idx',$idx)->getOne();
		$qna = $this->getArticle('qna',$qna);
		
		$post = $this->getPost($qna->parent);
		
		$dataroom = $this->getDataroom($qna->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$qna->idx)->where('type','QUESTION')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/qna.view.php') == true) {
			INCLUDE $templetPath.'/qna.view.php';
		}
		
		if ($qna->has_answer == 'TRUE') {
			echo $this->getQnaAnswerView($idx);
		} elseif ($this->checkPermission('answer_write') == true || $post->midx == $this->IM->getModule('member')->getLogged()) {
			echo $this->getQnaAnswerWrite($idx);
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaAnswerView($parent) {
		ob_start();
		
		$answer = $this->db()->select($this->table->answer)->where('parent',$parent)->getOne();
		$answer = $this->getArticle('answer',$answer);
		
		$dataroom = $this->getDataroom($answer->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$answer->idx)->where('type','ANSWER')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div id="ModuleDataroomQnaAnswer-'.$parent.'">'.PHP_EOL;
		if (file_exists($templetPath.'/qna.answer.view.php') == true) {
			INCLUDE $templetPath.'/qna.answer.view.php';
		}
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaAnswerWrite($parent,$idx=null) {
		ob_start();
		
		$qna = $this->db()->select($this->table->question)->where('idx',$parent)->getOne();
		
		$dataroom = $this->getDataroom($qna->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomQnaWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleDataroomQnaForm-'.$parent.'" onsubmit="return Dataroom.qna.answer(this);">'.PHP_EOL;
		if ($idx != null) echo '<input type="hidden" name="idx" value="">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/qna.answer.write.php') == true) {
			INCLUDE $templetPath.'/qna.answer.write.php';
		}
			
		echo '</form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Dataroom.qna.init("ModuleDataroomQnaForm-'.$parent.'"); });</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaWrite($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$title = $this->getLanguage('qnaWrite/title');
		
		echo '<div id="ModuleDataroomQnaWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleDataroomQnaForm-'.$parent.'" onsubmit="return Dataroom.qna.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/qna.write.php') == true) {
			INCLUDE $templetPath.'/qna.write.php';
		}
		
		echo '</form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Dataroom.qna.init("ModuleDataroomQnaForm-'.$parent.'"); });</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getQnaPage($parent,$p=1,$qnalimit) {
		$startPosition = ($p-1) * $qnalimit;
		$lists = $this->db()->select($this->table->question)->where('parent',$parent)->orderBy('reg_date','desc')->limit($startPosition,$qnalimit)->get();
		
		return $lists;
	}
	
	function getMentList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomMentList-'.$parent.'" class="mentList">'.PHP_EOL;
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
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		$totalMents = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$totalPage = ceil($totalMents/$dataroom->mentlimit) == 0 ? 1 : ceil($totalMents/$dataroom->mentlimit);
		
		$pagination = GetPagination($p == null ? $totalPage : $p,$totalPage,$dataroom->pagelimit,'LEFT','@Dataroom.ment.loadPage');
		
		echo '<div id="ModuleDataroomMentPagination-'.$parent.'" class="mentPagination" data-parent="'.$parent.'"'.($totalPage == 1 ? ' style="display:none;"' : '').'>'.PHP_EOL;
		
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
		
		$dataroom = $this->getDataroom($ment->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomMentItem-'.$ment->idx.'" class="mentItem ment'.($ment->depth == 0 ? 'Parent' : 'Child').'" data-idx="'.$ment->idx.'" data-parent="'.$ment->parent.'" data-modify="'.$ment->modify_date.'">'.PHP_EOL;
		
		$ment = $this->getArticle('ment',$ment);
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$ment->idx)->where('type','MENT')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
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
	
	function getMentWrite($parent,$idx=null) {
		ob_start();
		
		$post = $this->getPost($parent);
		$dataroom = $this->getDataroom($post->did);
		$templetPath = $dataroom->templetPath;
		$templetDir = $dataroom->templetDir;
		
		echo '<div id="ModuleDataroomMentWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleDataroomMentForm-'.$parent.'" onsubmit="return Dataroom.ment.submit(this);">'.PHP_EOL;
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
		echo '<script>$(document).ready(function() { Dataroom.ment.init("ModuleDataroomMentForm-'.$parent.'"); });</script>';
		
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
		
		$dataroom = $this->getDataroom($post->did);
		$totalCount = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$lastPage = $totalCount > 0 ? ceil($totalCount/$dataroom->mentlimit) : 1;
		
		return $this->getMentPage($parent,$lastPage,$dataroom->mentlimit);
	}
	
	function getMentPosition($idx) {
		$ment = $this->getMent($idx);
		if ($ment == null) return 0;
		
		$dataroom = $this->getDataroom($ment->did);
		$position = $this->db()->select($this->table->ment_depth)->where('parent',$ment->parent)->where('head',$ment->head,'<=')->where('arrange',$ment->arrange,'<=')->count();
		$page = ceil($position/$dataroom->mentlimit);
		
		return $page;
	}
	
	function getWysiwyg($name) {
		$wysiwyg = $this->IM->getModule('wysiwyg')->setName($name)->setModule('dataroom');
		
		return $wysiwyg;
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
		
		switch ($type) {
			case 'write' :
				return $this->IM->getModule('member')->isLogged();
				
			case 'write_ment' :
				return $this->IM->getModule('member')->isLogged();
				
			case 'download' :
				return $this->IM->getModule('member')->isLogged();
				
			case 'ment_write' :
				return $this->IM->getModule('member')->isLogged();
			
			case 'qna_write' :
				return $this->IM->getModule('member')->isLogged();
			
			case 'answer_write' :
				return $this->IM->getModule('member')->isLogged();
				
				
			default :
				return false;
		}
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
			
			$keyword = Request('keyword');
			$category = Request('category');
			$sort = Request('sort');
			
			$key = Request('key');
			$keyword = Request('keyword');
			$p = Request('p') ? Request('p') : 1;
			
			$queryString = 'menu='.$page->menu.'&page='.$page->page.'&keyword='.$keyword.'&category='.$category.'&sort='.$sort.'&p='.$p;
			
			if ($oKeyword != $keyword || $oCategory != $category || $oSort != $sort) $p = 1;
			
			$default = array();
			if (strlen($keyword) == 0) {
				$default['keyword'] = '';
			}
			if ($sort == 'update') $default['sort'] = '';
			if (isset($page->context->config->category) == true && $page->context->config->category == $category) $default['category'] = '';
			
			$url = $this->IM->getUrl($page->menu,$page->page,'list',$p).$this->IM->getQueryString($default,$queryString);
			
			$results->success = true;
			$results->url = $url;
		}
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->did = Request('did');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->category = Request('category');
			$values->homepage = Request('homepage');
			$values->license = Request('license') ? Request('license') : $values->errors['license'] = $this->getLanguage('postWrite/help/license/error');
			$values->price = Request('price') ? (preg_match('/[1-9]+[0-9]*/',Request('price')) == true ? Request('price') : $values->errors['price'] = $this->getLanguage('postWrite/help/price/error')) : 0;
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			$values->dataroom = $this->getDataroom($values->did);
			
			if ($values->dataroom->use_category == 'USED') {
				if ($values->category == null || preg_match('/^[1-9]+[0-9]*$/',$values->category) == false) {
					$values->errors['category'] = $this->getLanguage('postWrite/help/category/error');
				}
			} else {
				$values->category = 0;
			}
			
			if (isset($_FILES['logo']['tmp_name']) == true && $_FILES['logo']['tmp_name']) {
				$checkImage = getimagesize($_FILES['logo']['tmp_name']);
				if (in_array($checkImage[2],array(1,2,3)) == false) {
					$values->errors['logo'] = $this->getLanguage('postWrite/help/logo/error');
				}
			}
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['did'] = $values->did;
				$insert['category'] = $values->category;
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				$insert['homepage'] = $values->homepage;
				$insert['license'] = $values->license;
				$insert['price'] = $values->price;
				
				if ($values->idx == null) {
					$post = null;
					
					if ($this->checkPermission('write') == false) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} else {
						$oCategory = null;
						$reg_date = time();
						
						$insert['reg_date'] = $reg_date;
						$insert['last_update'] = 0;
						$insert['ip'] = $_SERVER['REMOTE_ADDR'];
						$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
					}
					
					$this->IM->setArticle('dataroom',$values->did,'post',$values->idx,0);
					$this->IM->getModule('member')->sendPoint(null,$values->dataroom->post_point,'dataroom','post',array('idx'=>$values->idx));
					$this->IM->getModule('member')->addActivity(null,$values->dataroom->post_exp,'dataroom','post',array('idx'=>$values->idx));
				} else {
					$post = $this->getPost($values->idx);
					
					$oCategory = $post->category;
					$reg_date = $post->reg_date;
					
					if ($this->checkPermission('modify') == false && $post->midx != $this->IM->getModule('member')->getLogged()) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					}
					
					if ($results->success == true) {
						$this->db()->update($this->table->post,$insert)->where('idx',$post->idx)->execute();
						
						$this->IM->setArticle('dataroom',$values->did,'post',$values->idx,$post->last_update);
						if ($post->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($post->midx,'dataroom','post_modify',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
						$this->IM->getModule('member')->addActivity(null,0,'dataroom','post_modify',array('idx'=>$values->idx));
					}
				}
				
				if ($results->success == true) {
					if (isset($_FILES['logo']['tmp_name']) == true && $_FILES['logo']['tmp_name']) {
						$fileName = $_FILES['logo']['name'];
						$tempFileName = $this->IM->getModule('attachment')->getTempPath(true).'/'.md5_file($_FILES['logo']['tmp_name']);
						if ($this->IM->getModule('attachment')->createThumbnail($_FILES['logo']['tmp_name'],$tempFileName,500,0,true) == true) {
							if ($post == null || $post->logo == 0) {
								$logoIdx = $this->IM->getModule('attachment')->fileSave($fileName,$tempFileName,'dataroom','logo');
							} else {
								$logoIdx = $this->IM->getModule('attachment')->fileReplace($post->logo,$fileName,$tempFileName);
							}
							$this->db()->update($this->table->post,array('logo'=>$logoIdx))->where('idx',$values->idx)->execute();
							$this->IM->getModule('attachment')->filePublish($logoIdx);
						}
					}
					
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'did'=>$values->did,'type'=>'POST','parent'=>$values->idx))->execute();
						}
						$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
					}
					
					if ($oCategory != 0 && $oCategory != $values->category) {
						$lastPost = $this->db()->select($this->table->post)->where('category',$oCategory)->orderBy('last_update','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->last_update : 0;
						$this->db()->update($this->table->category,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$oCategory)->execute();
					}
					
					if ($values->category != 0 && $oCategory != $values->category) {
						$lastPost = $this->db()->select($this->table->post)->where('category',$values->category)->orderBy('last_update','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->last_update : 0;
						$this->db()->update($this->table->category,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('idx',$values->category)->execute();
					}
					
					$postnum = $this->db()->select($this->table->post)->where('did',$values->did)->count();
					$this->db()->update($this->table->dataroom,array('postnum'=>$postnum))->where('did',$values->did)->execute();
					
					$page = $this->IM->getPages($values->menu,$values->page);
					
					if ($page->context->config == null) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,($post == null ? 'version' : 'view'),$values->idx);
					} elseif ($page->context->config->category == $values->category) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,($post == null ? 'version' : 'view'),$values->idx);
					} else {
						$redirectPage = $this->getPostPage($values->idx);
						$results->redirect = $this->IM->getUrl($redirectPage->menu,$redirectPage->page,($post == null ? 'version' : 'view'),$values->idx);
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
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
		
		if ($action == 'versionWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->parent = Request('parent');
			$values->did = Request('did');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->version = preg_match('/^[0-9]+\.[0-9]+(\.[0-9]+)?$/',Request('version')) == true ? Request('version') : $values->errors['version'] = $this->getLanguage('versionWrite/help/history/error');
			$values->history = Request('history') ? Request('history') : $values->errors['history'] = $this->getLanguage('versionWrite/help/history/error');
			
			$values->dataroom = $this->getDataroom($values->did);
			$values->post = $this->getPost($values->parent);
			
			if (version_compare($values->post->last_version,$values->version,'>=') == true) {
				$values->errors['version'] = $this->getLanguage('versionWrite/help/version/lowVersion');
			}
			
			if (isset($_FILES['file']['tmp_name']) == true && $_FILES['file']['tmp_name']) {
				if ($this->IM->getModule('attachment')->getFileMime($_FILES['file']['tmp_name']) != 'application/zip') {
					$errors['file'] = $this->getLanguage('versionWrite/help/file/notzip');
				}
			}
			
			if (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['did'] = $values->did;
				$insert['parent'] = $values->parent;
				$insert['version'] = $values->version;
				$insert['history'] = $values->history;
				
				if ($values->idx == null) {
					if ($this->checkPermission('write') == false && $values->post->midx != $this->IM->getModule('member')->getLogged()) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} else {
						$reg_date = time();
						
						$insert['reg_date'] = $reg_date;
						$values->idx = $this->db()->insert($this->table->post_version,$insert)->execute();
						
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'view',$values->parent);
					}
					// Action Register
					
				} else {
					$oCategory = $post->category;
					$reg_date = $post->reg_date;
					
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
					}
					
					$insert['reg_date'] = $post['reg_date'];
				}
				
				if ($results->success == true) {
					if (isset($_FILES['file']['tmp_name']) == true && $_FILES['file']['tmp_name']) {
						$fileName = $_FILES['file']['name'];
						$fileIdx = $this->IM->getModule('attachment')->fileSave($fileName,$_FILES['file']['tmp_name'],'dataroom','file');
						$this->db()->update($this->table->post_version,array('file'=>$fileIdx))->where('idx',$values->idx)->execute();
					}
					
					$lastVersion = $this->db()->select($this->table->post_version)->where('parent',$values->parent)->orderBy('reg_date','desc')->getOne();
					$this->db()->update($this->table->post,array('last_version'=>$lastVersion->version,'last_update'=>$lastVersion->reg_date))->where('idx',$values->parent)->execute();
					
					if ($values->post->category != 0) {
						$this->db()->update($this->table->category,array('last_post'=>$lastVersion->reg_date))->where('idx',$values->post->category)->execute();
					}
					
					$lastPost = $this->db()->select($this->table->post)->where('did',$values->did)->orderBy('last_update','desc')->getOne();
					$this->db()->update($this->table->dataroom,array('last_post'=>$lastPost->last_update))->where('did',$values->did)->execute();
					
					$this->IM->setArticle('dataroom',$values->did,'post',$values->post->idx,time());
					
					$page = $this->IM->getPages($values->menu,$values->page);
					
					$results->redirect = $this->IM->getUrl($values->menu,$values->page,'view',$values->parent);
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'getQna') {
			$values->get = Request('get');
			
			if ($values->get == 'page') {
				$values->parent = Request('parent');
				$values->post = $this->getPost($values->parent);
				$values->dataroom = $this->getDataroom($values->post->did);
				$values->qnalimit = $values->dataroom->qnalimit;
				
				$values->page = Request('page');
				$values->qnas = $this->getQnaPage($values->parent,$values->page,$values->qnalimit);
				
				if ($values->page > 1 && count($values->qnas) == 0) {
					while ($values->page > 1) {
						$values->page = $values->page - 1;
						$values->qnas = $this->getMentPage($values->parent,$values->page,$values->qnalimit);
						if (count($values->qnas) > 0) break;
					}
				}
				
				if (count($values->qnas) > 0) $results->page = $values->page;
			} elseif ($values->get == 'idx') {
				$values->idx = Request('idx');
				$qna = $this->db()->select($this->table->question)->where('idx',$values->idx)->getOne();
				$values->dataroom = $this->getDataroom($qna->did);
				$values->qnalimit = $values->dataroom->qnalimit;
				$values->parent = $qna->parent;
				
				if ($qna != null) {
					$values->post = $this->getPost($values->parent);
					$prevCount = $this->db()->select($this->table->question)->where('parent',$qna->parent)->where('idx',$qna->idx,'>')->count();
					$values->page = floor($prevCount/$values->qnalimit) + 1;
					
					$values->qnas = $this->getQnaPage($values->parent,$values->page,$values->qnalimit);
					$results->page = $values->page;
				} else {
					$results->page = null;
					$results->mentHtml = '<div class="empty">'.$this->getLanguage('qnaList/empty').'</div>';
				}
			}
			
			if (count($values->qnas) == 0) {
				$results->page = null;
				$results->qnaHtml = '<div class="empty">'.$this->getLanguage('qnaList/empty').'</div>';
			} else {
				$results->qnaHtml = '';
				for ($i=0, $loop=count($values->qnas);$i<$loop;$i++) {
					$results->qnaHtml.= $this->getQnaItem($values->qnas[$i]);
				}
			}
			
			$results->success = true;
			$results->parent = $values->parent;
			if ($results->page != null) $results->qnaCount = number_format($values->post->qna);
			$results->pagination = $this->getQnaPagination($results->parent,$results->page);
		}
		
		if ($action == 'getQnaView') {
			$values->idx = Request('idx');
			
			$results->success = true;
			$results->idx = $values->idx;
			$results->qnaHtml = $this->getQnaView($values->idx);
		}
		
		if ($action == 'getQnaWrite') {
			$values->parent = Request('parent') ? Request('parent') : null;
			$values->post = $this->getPost($values->parent);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($this->checkPermission('qna_write') == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = $this->getLanguage('error/mypost');
			} else {
				$results->success = true;
				$results->parent = $values->parent;
				$results->qnaHtml = $this->getQnaWrite($values->parent);
			}
		}
		
		if ($action == 'qnaWrite') {
			$values->errors = array();
			
			$values->parent = Request('parent');
			$values->post = $this->getPost($values->parent);
			$values->dataroom = $this->getDataroom($values->post->did);
			$values->did = $values->dataroom->did;
			
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['did'] = $values->did;
				$insert['parent'] = $values->parent;
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				
				if ($this->IM->getModule('member')->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				} elseif ($this->checkPermission('qna_write') == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = false;
					$results->message = $this->getLanguage('error/mypost');
				} else {
					$insert['reg_date'] = time();
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					$values->idx = $this->db()->insert($this->table->question,$insert)->execute();
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'did'=>$values->did,'type'=>'QUESTION','parent'=>$values->idx))->execute();
						}
					}
					
					$qnanum = $this->db()->select($this->table->question)->where('parent',$values->parent)->count();
					$this->db()->update($this->table->post,array('qna'=>$qnanum))->where('idx',$values->parent)->execute();
					
					$this->IM->getModule('push')->sendPush($values->post->midx,'dataroom','question',$values->post->idx,array('from'=>$this->IM->getModule('member')->getLogged(),'idx'=>$values->idx));
					$results->idx = $values->idx;
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'qnaAnswer') {
			$values->errors = array();
			
			$values->parent = Request('parent');
			$values->question = $this->db()->select($this->table->question)->where('idx',$values->parent)->getOne();
			$values->post = $this->getPost($values->question->parent);
			$values->dataroom = $this->getDataroom($values->post->did);
			$values->did = $values->dataroom->did;
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['did'] = $values->did;
				$insert['parent'] = $values->parent;
				$insert['content'] = $values->content;
				
				if ($this->IM->getModule('member')->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				} elseif ($this->checkPermission('qna_answer') == false && $values->post->midx != $this->IM->getModule('member')->getLogged()) {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				} elseif ($values->question->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = false;
					$results->message = $this->getLanguage('error/myquestion');
				} else {
					$insert['reg_date'] = time();
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					$values->idx = $this->db()->insert($this->table->answer,$insert)->execute();
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'did'=>$values->did,'type'=>'ANSWER','parent'=>$values->idx))->execute();
						}
					}
					$this->db()->update($this->table->question,array('has_answer'=>'TRUE'))->where('idx',$values->parent)->execute();
					$this->IM->getModule('push')->sendPush($values->question->midx,'dataroom','answer',$values->question->idx,array('from'=>$this->IM->getModule('member')->getLogged(),'idx'=>$values->idx));
					
					$results->idx = $values->idx;
					$results->parent = $values->parent;
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'getMent') {
			$values->get = Request('get');
			
			if ($values->get == 'page') {
				$values->parent = Request('parent');
				$values->post = $this->getPost($values->parent);
				$values->dataroom = $this->getDataroom($values->post->did);
				$values->mentlimit = $values->dataroom->mentlimit;
				
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
					$values->dataroom = $this->getDataroom($values->post->did);
					$values->mentlimit = $values->dataroom->mentlimit;
					
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
			$values->dataroom = $this->getDataroom($values->post->did);
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
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
			} elseif ($this->checkPermission('ment_write') == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			} elseif (empty($values->errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['did'] = $values->post->did;
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
						$this->IM->getModule('push')->sendPush($values->post->midx,'dataroom','ment',$values->post->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
					}
					$this->IM->getModule('member')->sendPoint(null,$values->dataroom->ment_point,'dataroom','ment',array('idx'=>$values->idx));
					$this->IM->getModule('member')->addActivity(null,$values->dataroom->ment_exp,'dataroom','ment',array('idx'=>$values->idx));
					
					if ($source != 0 && $sourceData->midx != 0 && $sourceData->midx != $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($sourceData->midx,'dataroom','replyment',$values->post->idx,array('idx'=>$values->idx,'from'=>$this->IM->getModule('member')->getLogged()));
					}
					
					$results->success = true;
				} else {
					$ment = $this->getMent($values->idx);
					$values->dataroom = $this->getDataroom($ment->did);
					
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
							$this->IM->getModule('push')->sendPush($ment->midx,'dataroom','ment_modify',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
						$this->IM->getModule('member')->addActivity(null,$values->dataroom->ment_exp,'dataroom','ment_modify',array('idx'=>$values->idx));
					}
					
					$results->success = true;
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'did'=>$values->post->did,'type'=>'MENT','parent'=>$values->idx))->execute();
						}
					}
					
					$mentnum = $this->db()->select($this->table->ment)->where('parent',$values->parent)->where('is_delete','FALSE')->count();
					$this->db()->update($this->table->post,array('ment'=>$mentnum))->where('idx',$values->parent)->execute();
					
					$this->IM->setArticle('dataroom',$values->dataroom->did,'ment',$values->idx,time());
					
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
			$values->password = Request('password');
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
		
		if ($action == 'downloadConfirm') {
			$values->idx = Request('idx');
			$values->version = Request('version');
			$values->confirm = Request('confirm');
			$values->post = $this->getPost($values->idx);
			
			$results->success = true;
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($this->checkPermission('download') == false && $values->post->midx != $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
			
			if ($results->success == true) {
				if ($values->version == 'latest') {
					$values->post_version = $this->db()->select($this->table->post_version)->where('parent',$values->idx)->orderBy('reg_date','desc')->getOne();
				} else {
					$values->post_version = $this->db()->select($this->table->post_version)->where('parent',$values->idx)->where('version',$values->version)->getOne();
				}
				
				if ($values->post_version == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = true;
					$results->downloadUrl = $this->IM->getProcessUrl('dataroom','download',array('idx'=>$values->idx,'version'=>$values->post_version->version));
				} else {
					$values->purchase = $this->db()->select($this->table->purchase)->where('parent',$values->idx)->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
					if ($values->purchase == null) {
						if ($values->confirm == 'TRUE') {
							$price = $values->post->price;
							if ($price == 0 || $this->IM->getModule('member')->sendPoint(null,$price * -1,'dataroom','purchase',array('idx'=>$values->idx)) == true) {
								$this->db()->insert($this->table->purchase,array('parent'=>$values->idx,'midx'=>$this->IM->getModule('member')->getLogged(),'price'=>$price,'reg_date'=>time()))->execute();
								if ($price > 0) $this->IM->getModule('member')->sendPoint($values->post->midx,round($price * 0.7),'dataroom','sale',array('idx'=>$values->idx),true);
								$results->success = true;
								$results->downloadUrl = $this->IM->getProcessUrl('dataroom','download',array('idx'=>$values->idx,'version'=>$values->post_version->version));
								
								$this->IM->getModule('member')->addActivity(null,0,'dataroom','purchase',array('idx'=>$values->idx));
							} else {
								$results->success = false;
								$results->message = $this->getLanguage('error/notEnoughPoint');
							}
						} else {
							$results->success = true;
							$results->modalHtml = $this->getDownload($values->idx,$values->post_version->version);
						}
					} else {
						$results->success = true;
						$results->downloadUrl = $this->IM->getProcessUrl('dataroom','download',array('idx'=>$values->idx,'version'=>$values->post_version->version));
					}
				}
			}
		}
		
		if ($action == 'download') {
			$values->idx = Request('idx');
			$values->version = Request('version');
			$values->post = $this->getPost($values->idx);
			
			if ($this->checkPermission('download') == false && $values->post->midx != $this->IM->getModule('member')->getLogged()) {
				header("HTTP/1.1 403 Forbidden");
				exit;
			}
		
			$values->post_version = $this->db()->select($this->table->post_version)->where('parent',$values->idx)->where('version',$values->version)->getOne();
			
			if ($values->post_version == null) {
				header("HTTP/1.1 404 Not Found");
			} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
				$this->IM->getModule('attachment')->fileDownload($values->post_version->file,false);
			} else {
				$values->purchase = $this->db()->select($this->table->purchase)->where('parent',$values->idx)->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
				
				if ($values->purchase == null) {
					header("HTTP/1.1 403 Forbidden");
				} else {
					$this->db()->update($this->table->post,array('download'=>$this->db()->inc()))->where('idx',$values->idx)->execute();
					$this->IM->getModule('attachment')->fileDownload($values->post_version->file);
				}
			}
			exit;
		}
		
		if ($action == 'vote') {
			$values->type = in_array(Request('type'),array('post','ment')) == true ? Request('type') : 'post';
			$values->idx = Request('idx');
			$values->vote = in_array(Request('vote'),array('good','bad')) == true ? Request('vote') : 'good';
			
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
					$check = $this->db()->select($this->table->history)->where('parent',$values->idx)->where('action','VOTE')->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
					if ($check == null) {
						$this->db()->update($this->table->{$values->type},array($values->vote=>$this->db()->inc()))->where('idx',$values->idx)->execute();
						$this->db()->insert($this->table->history,array('parent'=>$values->idx,'action'=>'VOTE','midx'=>$this->IM->getModule('member')->getLogged(),'result'=>strtoupper($values->vote),'reg_date'=>time()))->execute();
						$results->success = true;
						$results->message = $this->getLanguage('vote/'.$values->vote);
						$results->liveUpdate = 'liveUpdateDataroom'.ucfirst($values->type).ucfirst($values->vote).$values->idx;
						$results->liveValue = number_format($values->vote + 1);
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
					$this->db()->update($this->table->post,array('is_delete'=>'TRUE','FALSE'))->where('idx',$values->idx)->execute();
				}
				
				$results->success = true;
			} elseif ($values->type == 'ment') {
				$ment = $this->getMent($values->idx);
				$post = $this->getPost($ment->parent);
				
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
					$this->IM->deleteArticle('dataroom','ment',$values->idx);
					$results->message = $this->getLanguage('mentDelete/success');
				}
			} elseif ($values->type == 'answer') {
				$answer = $this->db()->select($this->table->answer)->where('idx',$values->idx)->getOne();
				
				if ($answer == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->checkPermission('answer_delete') == true || $answer->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = true;
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				}
				
				if ($results->success == true) {
					$this->db()->delete($this->table->answer)->where('idx',$values->idx)->execute();
					$attachments = $this->db()->select($this->table->attachment)->where('parent',$answer->idx)->where('type','ANSWER')->get();
					for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
						$attachments[$i] = $attachments[$i]->idx;
					}
					$this->IM->getModule('attachment')->fileDelete($attachments);
					
					$this->db()->update($this->table->question,array('has_answer'=>'FALSE'))->where('idx',$answer->parent)->execute();
					$results->parent = $answer->parent;
					$results->message = $this->getLanguage('answerDelete/success');
				}
			}
			$results->type = $values->type;
		}
		
		$this->IM->fireEvent('afterDoProcess','dataroom',$action,$values,$results);
		
		return $results;
	}
	
	function deleteAttachment($idx) {
		$this->db()->delete($this->table->attachment)->where('idx',$idx)->execute();
	}
	
	function resetArticle() {
		$posts = $this->db()->select($this->table->post)->get();
		for ($i=0, $loop=count($posts);$i<$loop;$i++) {
			$this->IM->setArticle('dataroom',$posts[$i]->did,'post',$posts[$i]->idx,$posts[$i]->last_update);
		}
		
		$ments = $this->db()->select($this->table->ment)->where('is_delete','FALSE')->get();
		for ($i=0, $loop=count($ments);$i<$loop;$i++) {
			$this->IM->setArticle('dataroom',$ments[$i]->did,'ment',$ments[$i]->idx,$ments[$i]->modify_date != 0 ? $ments[$i]->modify_date : $ments[$i]->reg_date);
		}
	}
}
?>