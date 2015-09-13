<?php
class ModuleLms {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $lmsPages = array();
	private $classPages = array();
	private $postPages = array();
	private $labelUrls = array();
	
	private $lmses = array();
	private $labels = array();
	private $classes = array();
	private $attends = array();
	private $subjects = array();
	private $posts = array();
	private $ments = array();
	
	private $youtube = null;
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->lms = 'lms_table';
		$this->table->label = 'lms_label_table';
		$this->table->class = 'lms_class_table';
		$this->table->class_label = 'lms_class_label_table';
		$this->table->attend = 'lms_attend_table';
		$this->table->subject = 'lms_subject_table';
		$this->table->post = 'lms_post_table';
		$this->table->tracking = 'lms_tracking_table';
		$this->table->ment = 'lms_ment_table';
		$this->table->ment_depth = 'lms_ment_depth_table';
		$this->table->attachment = 'lms_attachment_table';
		$this->table->history = 'forum_history_table';
		
		$this->youtube = new stdClass();
		$this->youtube->client_id = '995059916144-2odfvfoh0h18fhfsid1lh25d1vpunm5n.apps.googleusercontent.com';
		$this->youtube->client_secret = 'A3G-GgF_2rsWXUuvmU1hPLOv';
		$this->youtube->auth_url = 'https://accounts.google.com/o/oauth2/auth';
		$this->youtube->token_url = 'https://accounts.google.com/o/oauth2/token';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/lms.js');
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
	
	function getCountInfo($lid,$config) {
		$lms = $this->getLms($lid);
		if ($lms == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $lms->classnum;
			$info->last_time = $lms->last_class;
		} elseif (isset($config->label) == true) {
			$info->count = $this->getLabel($config->label)->classnum;
			$info->last_time = $this->getLabel($config->label)->last_class;
		}
		
		return $info;
	}
	
	function getContext($lid,$config=null) {
		$values = new stdClass();
		
		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		$lms = $this->getLms($lid);
		if ($lms == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($lid,$config);
		
		switch ($view) {
			case 'create' :
				$context.= $this->getCreateContext($lid,$config);
				break;
				
			case 'list' :
				$context.= $this->getListContext($lid,$config);
				break;
				
			case 'class' :
				$context.= $this->getClassContext($lid,$config);
				break;
				
			case 'view' :
				$context.= $this->getViewContext($lid,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($lid,$config);
				break;
		}
		
		$context.= $this->getFooter($lid,$config);
		
		$this->IM->fireEvent('afterGetContext','lms',$view,null,null,$context);
		
		return $context;
	}
	
	function getLms($lid) {
		if (isset($this->lmses[$lid]) == true) return $this->lmses[$lid];
		$lms = $this->db()->select($this->table->lms)->where('lid',$lid)->getOne();
		if ($lms == null) {
			$this->lmses[$lid] = null;
		} else {
			$lms->templetPath = $this->Module->getPath().'/templets/'.$lms->templet;
			$lms->templetDir = $this->Module->getDir().'/templets/'.$lms->templet;
			
			$this->lmses[$lid] = $lms;
		}
		
		return $this->lmses[$lid];
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
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx);
		}
		
		if ($code == 'replyment') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx);
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
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->idx);
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
	
	function getClassPage($idx,$domain=null) {
		if (isset($this->classPages[$idx]) == true) return $this->classPages[$idx];
		
		$class = $this->getClass($idx);
		$labels = $this->db()->select($this->table->class_label)->where('idx',$idx)->get();
		for ($i=0, $loop=count($labels);$i<$loop;$i++) $labels[$i] = $labels[$i]->label;
		
		if (count($labels) == 0) {
			$this->classPages[$idx] = $this->getLmsPage($class->lid,null,$domain);
			return $this->classPages[$idx];
		}
		
		$this->classPages[$idx] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'lms' && $pages[$i]->context->context == $class->lid && $pages[$i]->context->config != null && in_array($pages[$i]->context->config->label,$labels) == true) {
					$this->classPages[$idx] = $pages[$i];
					return $this->classPages[$idx];
				}
			}
		}
		
		if ($domain === null && $this->classPages[$idx] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getClassPage($idx,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->classPages[$idx] = $this->getClassPage($idx,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		$this->classPages[$idx] = $this->getLmsPage($class->lid);
		
		return $this->classPages[$idx];
	}
	
	function getPostPage($idx,$domain=null) {
		if (isset($this->postPages[$idx]) == true) return $this->postPages[$idx];
		
		$post = $this->getPost($idx);
		$this->postPages[$idx] = $this->getClassPage($post->class);
		
		return $this->postPages[$idx];
	}
	
	function getLmsPage($lid,$label=null,$domain=null) {
		if (isset($this->lmsPages[$lid]) == true && $label == null) return $this->lmsPages[$lid];
		
		$this->lmsPages[$lid] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'lms' && $pages[$i]->context->context == $lid) {
					if ($label != null && $pages[$i]->context->config != null && $pages[$i]->context->config->label == $label) {
						return $pages[$i];
					}
					
					if ($label == null && $pages[$i]->context->config == null) {
						$this->lmsPages[$lid] = $pages[$i];
						return $this->lmsPages[$lid];
					}
					
					$this->lmsPages[$lid] = $this->lmsPages[$lid] == null ? $pages[$i] : $this->lmsPages[$lid];
				}
			}
		}
		
		if ($domain === null && $this->lmsPages[$lid] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getLmsPage($lid,$label,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->lmsPages[$lid] = $this->getLmsPage($lid,$label,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		return $this->lmsPages[$lid];
	}
	
	function getLabelUrl($idx) {
		if (isset($this->labelUrls[$idx]) == true) return $this->labelUrls[$idx];
		
		$label = $this->getLabel($idx);
		$lms = $this->getLms($label->lid);
		
		$page = null;
		$sitemap = $this->IM->getPages();
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module') {
					if ($pages[$i]->context->module == 'forum' && $pages[$i]->context->context == $lms->lid && $pages[$i]->context->config != null && isset($pages[$i]->context->config->label) == true && $pages[$i]->context->config->label == $idx) {
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
	
	function getClass($idx) {
		if (isset($this->classes[$idx]) == true) return $this->classes[$idx];
		
		$this->classes[$idx] = $this->db()->select($this->table->class)->where('idx',$idx)->getOne();
		return $this->classes[$idx];
	}
	
	function getAttend($idx) {
		if (isset($this->attends[$idx]) == true) return $this->attends[$idx];
		$class = $this->getClass($idx);
		
		if ($this->IM->getModule('member')->isLogged() == false) {
			$this->attends[$idx] = null;
		} elseif ($class->midx == $this->IM->getModule('member')->getLogged()) {
			$attend = new stdClass();
			$attend->mode = 'P';
			$attend->status = 'ACTIVE';
			$this->attends[$idx] = $attend;
		} else {
			$attend = $this->db()->select($this->table->attend)->where('midx',$this->IM->getModule('member')->getLogged())->where('class',$class->idx)->getOne();
			if ($attend == null) {
				$this->attends[$idx] = null;
			} else {
				$attend->mode = 'S';
				$this->attends[$idx] = $attend;
			}
		}
		
		return $this->attends[$idx];
	}
	
	function getSubject($idx) {
		if (isset($this->subjects[$idx]) == true) return $this->subjects[$idx];
		
		$this->subjects[$idx] = $this->db()->select($this->table->subject)->where('idx',$idx)->getOne();
		return $this->subjects[$idx];
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
	
	function getHeader($lid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($lid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getLabels($idx) {
		return $this->db()->select($this->table->class_label.' p')->join($this->table->label.' l','p.label=l.idx','LEFT')->where('p.idx',$idx)->get();
	}
	
	function getArticle($type,$article,$isLink=false) {
		if ($type == 'class') {
			if (is_numeric($article) == true) $article = $this->getClass($article);
			$article->title = GetString($article->title,'replace');
			$article->cover = $this->IM->getModule('attachment')->getFileInfo($article->cover);
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->idx);
				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->idx,false,$page->domain,$page->language);
			}
		} elseif ($type == 'subject') {
			if (is_numeric($article) == true) $article = $this->getSubject($article);
			
			$attend = $this->getAttend($article->parent);
			$percent = 0;
			$article->posts = $this->db()->select($this->table->post)->where('parent',$article->idx)->orderBy('sort','asc')->get();
			for ($i=0, $loop=count($article->posts);$i<$loop;$i++) {
				$article->posts[$i] = $this->getArticle('post',$article->posts[$i]);
				$percent+= $article->posts[$i]->percent;
			}
			$article->percent = $loop > 0 ? $percent / $loop : 0;
		} elseif ($type == 'post') {
			if (is_numeric($article) == true) $article = $this->getPost($article);
			
			$class = $this->getClass($article->class);
			$attend = $this->getAttend($article->class);
			$article->title = GetString($article->title,'replace');
			$article->context = json_decode($article->context);
			
			$article->image = null;
			if ($article->type == 'youtube') {
				$article->image = $article->context->thumbnail ? $article->context->thumbnail : '';
			}
			
			if ($article->type == 'video') {
				$article->image = isset($article->context->thumbnail) == true ? $article->context->thumbnail : '';
			}
			
			$article->percent = 0;
			if ($attend != null) {
				if ($attend->mode == 'P') {
					$trackings = $this->db()->select($this->table->tracking)->where('pidx',$article->idx)->get();
					$percent = 0;
					foreach ($trackings as $tracking) {
						$percent+= $tracking->percent;
					}
					$article->percent = $class->student > 0 ? round($percent / $class->student) : 0;
				} else {
					$tracking = $this->db()->select($this->table->tracking)->where('midx',$this->IM->getModule('member')->getLogged())->where('pidx',$article->idx)->getOne();
					if ($tracking != null) {
						$article->percent = $tracking->percent;
					}
				}
			}
			
			if ($isLink == true) {
				$page = $this->getPostPage($article->idx);
//				$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->idx,false,$page->domain);
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
		if ($type != 'subject') {
			$article->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($article->content)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
		}
		
		if ($type != 'subject' && $type != 'post') {
			$article->ip = $this->getArticleIp($article->ip);
		}
		
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
	
	function getCreateContext($lid,$config) {
		$this->IM->addSiteHeader('script',__IM_DIR__.'/scripts/jquery.cropit.min.js');
		
		ob_start();
		
		$this->IM->setView('create');
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if ($lms->use_label != 'NONE') {
			$labels = $this->db()->select($this->table->label)->where('lid',$lid)->orderBy('classnum','desc')->get();
		} else {
			$labels = array();
		}
		
		$default = new stdClass();
		
		$idx = Request('idx');
		if ($idx !== null) {
			$class = $this->getClass($idx);
			$class->cover = $this->IM->getModule('attachment')->getFileInfo($class->cover);
			
			if ($class == null) {
				header("HTTP/1.1 404 Not Found");
				return $this->getError($this->getLangauge('error/notFound'));
			}
			
			if ($this->checkPermission('class_modify') == false && $class->midx != $this->IM->getModule('member')->getLogged()) {
				header("HTTP/1.1 403 Forfidden");
				return $this->getError($this->getLanguage('error/forbidden'));
			}
			
			if ($lms->use_label != 'NONE') {
				$class->labels = $this->db()->select($this->table->class_label)->where('idx',$class->idx)->get();
				for ($i=0, $loop=count($class->labels);$i<$loop;$i++) {
					$class->labels[$i] = $class->labels[$i]->label;
				}
			} else {
				$class->labels = array();
			}
		} else {
			if (isset($config->label) == true) $default->label = $config->label;
			$class = null;
		}
		
		$formName = 'ModuleLmsCreateForm';
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Lms.create.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="lid" value="'.$lid.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($class !== null) echo '<input type="hidden" name="idx" value="'.$class->idx.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/create.php') == true) {
			INCLUDE $templetPath.'/create.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Lms.create.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getListContext($lid,$config) {
		ob_start();
		
		$this->IM->setView('list');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$label = empty($config->label) == true ? Request('label') : $config->label;
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if ($label == null) {
			$strQuery = $this->db()->select($this->table->class.' c','c.*')->where('c.lid',$lid);
		} else {
			$strQuery = $this->db()->select($this->table->class_label.' l','c.*, l.label')->join($this->table->class.' c','l.idx=c.idx','LEFT')->where('l.label',$label);
		}
		
		$key = Request('key') ? Request('key') : 'content';
		$keyword = Request('keyword');
		
		if ($keyword != null && strlen($keyword) > 0) {
			if ($key == 'content') $strQuery = $strQuery->where('c.title,c.search',$keyword,'FULLTEXT');
//			elseif ($key == 'name') $strQuery = $strQuery->where('p.name',$keyword,'LIKE');
//			elseif ($key == 'ment') $strQuery = $strQuery->join($this->table->ment.' m','p.idx=m.parent','LEFT')->groupBy('m.parent')->where('m.search',$keyword,'FULLTEXT');
		}
		
		$sort = 'idx';
		$direction = 'desc';
		
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$startPosition = ($p-1) * $lms->classlimit;
		
		$totalCount = $strQuery->copy()->count();
		$strQuery = $strQuery->orderBy('c.last_subject','desc');
		$totalPage = ceil($totalCount/$lms->classlimit);
		$lists = $strQuery->limit($startPosition,$lms->classlimit)->get();
		
		$pagination = GetPagination($p,$totalPage,$lms->pagelimit,'LEFT');
		
		$loopnum = $totalCount - ($p - 1) * $lms->classlimit;
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i] = $this->getArticle('class',$lists[$i]);
			$lists[$i]->loopnum = $loopnum - $i;
			$lists[$i]->link = $this->IM->getUrl(null,null,'class',$lists[$i]->idx).$this->IM->getQueryString();
		}
		
		echo '<form name="ModuleLmsListForm" onsubmit="return Lms.getListUrl(this);">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.$this->IM->page.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKey" value="'.$key.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKeyword" value="'.$keyword.'">'.PHP_EOL;
		echo '<input type="hidden" name="oLabel" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="oSort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="oDirection" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="label" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="sort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="direction" value="'.$direction.'">'.PHP_EOL;
		echo '<input type="hidden" name="p" value="'.$p.'">'.PHP_EOL;
		
		
		$values = new stdClass();
		$values->lists = $lists;
		$this->IM->fireEvent('afterInitContext','forum',__FUNCTION__,$values);
		
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
	
	function getClassContext($lid,$config) {
		ob_start();
		
		$this->IM->setView('class');
		
		$idx = Request('idx');
		$class = $this->getArticle('class',$this->getClass($idx));
		$this->IM->addSiteHeader('link',array('rel'=>'canonical','href'=>$this->IM->getUrl(null,null,'class',$idx,true)));
		$this->IM->setSiteTitle($class->title);
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$tab = Request('tab') ? Request('tab') : 'subject';
		$tabContext = $this->getClassTabContext($idx,$tab);
		
		$values = new stdClass();
		$this->IM->fireEvent('afterInitContext','lms',__FUNCTION__,$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/class.php') == true) {
			INCLUDE $templetPath.'/class.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getClassTabContext($idx,$tab) {
		ob_start();
		
		$idx = Request('idx');
		$class = $this->getClass($idx);
		$attend = $this->getAttend($idx);
		
		$lms = $this->getLms($class->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if ($tab == 'subject') {
			$lists = $this->db()->select($this->table->subject)->where('parent',$idx)->orderBy('sort','asc')->get();
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$lists[$i] = $this->getArticle('subject',$lists[$i]);
			}
		}
		
		$values = new stdClass();
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/class.'.$tab.'.php') == true) {
			INCLUDE $templetPath.'/class.'.$tab.'.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getViewContext($lid,$config) {
		ob_start();
		
		$this->IM->setView('view');
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		$class = $this->getClass($post->class);
		$this->IM->addSiteHeader('link',array('rel'=>'canonical','href'=>$this->IM->getUrl(null,null,'view',$idx,true)));
		$this->IM->setSiteTitle($post->title);
		
		$lms = $this->getLms($lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
//		$this->db()->update($this->table->post,array('hit'=>$this->db()->inc()))->where('idx',$idx)->execute();
		
		$attend = $this->getAttend($post->class);
		if ($attend == null) {
			return $this->getError('수강하지 않는 강의');
		} else if ($attend->status != 'ACTIVE') {
			return $this->getError('수강승인을 기다리는 강의');
		}
		
		$typeContext = $this->getViewTypeContext($post->idx);
		
		if ($attend->mode == 'P') $attendContext = $this->getViewAttendContext($post->idx);
		else $attendContext = '';
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->where('type','POST')->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$values = new stdClass();
		$values->post = $post;
		$values->attachments = $attachments;
//		$this->IM->fireEvent('afterInitContext','lms',__FUNCTION__,$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.php') == true) {
			INCLUDE $templetPath.'/view.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getViewTypeContext($idx) {
		ob_start();
		
		$post = $this->getPost($idx);
		$class = $this->getClass($post->class);
		$lms = $this->getLms($post->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$attend = $this->getAttend($post->class);
		$context = $post->context;
		
		if ($post->type == 'video') {
			if (isset($context->time) == false || isset($context->thumbnail) == false) {
				$token = $this->IM->getModule('member')->getSocialAuth('youtube',$class->midx);
				
				$youtube = new OAuthClient();
				$youtube->setClientId($this->youtube->client_id)->setClientSecret($this->youtube->client_secret)->setScope('https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtubepartner https://www.googleapis.com/auth/youtube.force-ssl')->setRefreshToken($token->refresh_token)->setAuthUrl($this->youtube->auth_url)->setTokenUrl($this->youtube->token_url);
				
				$access_token = $youtube->getAccessToken();
				
				$video = $youtube->get('https://www.googleapis.com/youtube/v3/videos?id='.$context->id.'&part=snippet%2Cstatus%2CprocessingDetails%2CcontentDetails%2Cplayer%2CfileDetails');
				$video = $video != null ? $video = $video->items[0] : null;
				
				/*
				echo '<pre>';
				print_r($video);
				echo '</pre>';
				//processingStatus : processing
				//thumbnailsAvailability : inProgress
				*/
				
				if ($video != null) {
					$status = 'waiting';
					if (isset($context->time) == false && $video->processingDetails->processingStatus == 'succeeded') {
						if (preg_match('/^PT(([0-9]+)H)?(([0-9]+)M)?(([0-9]+)S)?$/',$video->contentDetails->duration,$time) == true) {
							$context->time = $time[2] * 3600 + $time[4] * 60 + $time[6];
						}
					}
					
					if (isset($context->thumbnail) == false && $video->processingDetails->thumbnailsAvailability == 'available') {
						$context->thumbnail = isset($video->snippet->thumbnails->standard) == true ? $video->snippet->thumbnails->standard->url : $video->snippet->high->standard;
					}
					
					$this->db()->update($this->table->post,array('context'=>json_encode($context)))->where('idx',$post->idx)->execute();
				} else {
					$status = 'error';
				}
			}
			
			if (isset($context->time) == true) {
				$post->type = 'youtube';
			}
		}
		
		if (file_exists($this->Module->getPath().'/scripts/lms.'.$post->type.'.js') == true) {
			$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/lms.'.$post->type.'.js');
		}
		
		$values = new stdClass();
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.'.$post->type.'.php') == true) {
			INCLUDE $templetPath.'/view.'.$post->type.'.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getViewAttendContext($idx) {
		ob_start();
		
		$post = $this->getPost($idx);
		
		$lms = $this->getLms($post->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if (file_exists($this->Module->getPath().'/scripts/lms.'.$post->type.'.js') == true) {
			$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/lms.'.$post->type.'.js');
		}
		
		$students = $this->db()->select($this->table->attend)->where('class',$post->class)->orderBy('reg_date','asc')->get();
		for ($i=0, $loop=count($students);$i<$loop;$i++) {
			$tracking = $this->db()->select($this->table->tracking)->where('midx',$students[$i]->midx)->where('pidx',$post->idx)->getOne();
			
			if ($tracking == null) {
				if ($post->type == 'youtube') $students[$i]->tracking = str_pad('',$post->context->time,'0');
				$students[$i]->percent = 0;
				$students[$i]->update_date = 0;
			} else {
				$students[$i]->tracking = $tracking->tracking;
				$students[$i]->percent = $tracking->percent;
				$students[$i]->update_date = $tracking->update_date;
			}
			$students[$i]->user = $this->IM->getModule('member')->getMember($students[$i]->midx);
		}
		$context = $post->context;
		
		$values = new stdClass();
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.attend.php') == true) {
			INCLUDE $templetPath.'/view.attend.php';
		}
		
		echo '<script>$(document).ready(function() { Lms.youtube.drawTrackingList(); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getDelete($type,$idx) {
		ob_start();
		
		if ($type == 'post') {
			$post = $this->getPost($idx);
			$lms = $this->getLms($post->lid);
		} else {
			$ment = $this->getMent($idx);
			$lms = $this->getLms($ment->lid);
		}
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		
		$title = $this->getLanguage($type.'Delete/title');
		echo '<form name="ModuleLmsDeleteForm" onsubmit="return Lms.delete(this);">'.PHP_EOL;
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
	
	function getWriteContext($lid,$config) {
		ob_start();
		
		$type = Request('type');
		$parent = Request('parent');
		$subject = $this->getSubject($parent);
		$this->IM->setView('write');
		
		$lms = $this->getLms($subject->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
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
			
			if ($lms->use_label != 'NONE') {
				$post->labels = $this->db()->select($this->table->class_label)->where('idx',$post->idx)->get();
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
		
		$formName = 'ModuleLmsWriteForm';
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Lms.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="lid" value="'.$lid.'">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		echo '<input type="hidden" name="type" value="'.$type.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($post !== null) echo '<input type="hidden" name="idx" value="'.$post->idx.'">'.PHP_EOL;
		
		$typeContext = $this->getWriteTypeContext($parent,$type);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Lms.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWriteTypeContext($parent,$type) {
		ob_start();
		
		$type = Request('type');
		$parent = Request('parent');
		$subject = $this->getSubject($parent);
		
		$lms = $this->getLms($subject->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		if ($type == 'video') {
			$token = $this->IM->getModule('member')->getSocialAuth('youtube');
			if ($token == null) {
				$_SESSION['SOCIAL_REDIRECT_URL'] = '/class/all/write?parent='.$parent.'&type=video';
				$message = '우리는 유튜브를 사용합니다.<br>유튜브 계정연결이 필요합니다. <a href="/process/member/youtube">이곳을 눌러 유튜브 계정을 연동하여 주십시오.</a>';
				return $message;
			} else {
				$youtube = new OAuthClient();
				$youtube->setClientId($this->youtube->client_id)->setClientSecret($this->youtube->client_secret)->setScope('https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtubepartner https://www.googleapis.com/auth/youtube.force-ssl')->setRefreshToken($token->refresh_token)->setAuthUrl($this->youtube->auth_url)->setTokenUrl($this->youtube->token_url);
				
//				echo $youtube->getAccessToken();
				
				echo '<input type="hidden" name="access_token" value="'.$youtube->getAccessToken().'">'.PHP_EOL;
			}
		}
		
		if (file_exists($this->Module->getPath().'/scripts/lms.'.$type.'.js') == true) {
			$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/lms.'.$type.'.js');
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
			
			if ($lms->use_label != 'NONE') {
				$post->labels = $this->db()->select($this->table->class_label)->where('idx',$post->idx)->get();
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
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.'.$type.'.php') == true) {
			INCLUDE $templetPath.'/write.'.$type.'.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$lms = $this->getLms($post->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		echo '<div id="ModuleLmsMentList-'.$parent.'" class="mentList">'.PHP_EOL;
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
		$lms = $this->getLms($post->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$totalMents = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$totalPage = ceil($totalMents/$lms->mentlimit) == 0 ? 1 : ceil($totalMents/$lms->mentlimit);
		
		$pagination = GetPagination($p == null ? $totalPage : $p,$totalPage,$lms->pagelimit,'LEFT','@Lms.ment.loadPage');
		
		echo '<div id="ModuleLmsMentPagination-'.$parent.'" class="mentPagination" data-parent="'.$parent.'"'.($totalPage == 1 ? ' style="display:none;"' : '').'>'.PHP_EOL;
		
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
		
		$lms = $this->getLms($ment->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		echo '<div id="ModuleLmsMentItem-'.$ment->idx.'" data-idx="'.$ment->idx.'" data-parent="'.$ment->parent.'" data-modify="'.$ment->modify_date.'" class="mentItem ment'.($ment->depth == 0 ? 'Parent' : 'Child').'">'.PHP_EOL;
		
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
	
	function getMentWriteContext($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		$lms = $this->getLms($post->lid);
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		echo '<div id="ModuleLmsMentWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleLmsMentForm-'.$parent.'" onsubmit="return Lms.ment.submit(this);">'.PHP_EOL;
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
		echo '<script>$(document).ready(function() { Lms.ment.init("ModuleLmsMentForm-'.$parent.'"); });</script>';
		
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
		
		$lms = $this->getLms($post->lid);
		$totalCount = $this->db()->select($this->table->ment_depth)->where('parent',$parent)->count();
		$lastPage = $totalCount > 0 ? ceil($totalCount/$lms->mentlimit) : 1;
		
		return $this->getMentPage($parent,$lastPage,$lms->mentlimit);
	}
	
	function getMentPosition($idx) {
		$ment = $this->getMent($idx);
		if ($ment == null) return 0;
		
		$lms = $this->getLms($ment->lid);
		$position = $this->db()->select($this->table->ment_depth)->where('parent',$ment->parent)->where('head',$ment->head,'<=')->where('arrange',$ment->arrange,'<=')->count();
		$page = ceil($position/$lms->mentlimit);
		
		return $page;
	}
	
	function getAttendClassModal($idx,$post=null) {
		ob_start();
		
		$class = $this->getClass($idx);
		$lms = $this->getLms($class->lid);
		
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$title = $this->getLanguage('attendClass/title');
		echo '<form name="ModuleLmsAddSubjectForm" onsubmit="return Lms.attend.register(this);">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		if ($post != null) echo '<input type="hidden" name="post" value="'.$post.'">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage('attendClass/content').'</div>'.PHP_EOL;
		
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
	
	function getPostSubjectModal($parent,$idx=null) {
		ob_start();
		
		$class = $this->getClass($parent);
		$lms = $this->getLms($class->lid);
		
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$title = $this->getLanguage('postSubject/title');
		echo '<form name="ModuleLmsAddSubjectForm" onsubmit="return Lms.subject.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		if ($idx != null) echo '<input type="hidden" name="idx" value="'.$idx.'">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage('subject_title').'</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '<input type="text" name="title" class="inputControl" required>'.PHP_EOL;
		$content.= '<div class="helpBlock" data-error="'.$this->getLanguage('postSubject/help/title/error').'"></div>'.PHP_EOL;
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
	
	function getAddItemModal($parent) {
		ob_start();
		
		$subject = $this->getSubject($parent);
		$lms = $this->getLms($subject->lid);
		
		$templetPath = $lms->templetPath;
		$templetDir = $lms->templetDir;
		
		$title = $this->getLanguage('addItem/title');
		echo '<form name="ModuleLmsAddSubjectForm" method="post" onsubmit="return Lms.item.post(this);">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		echo '<input type="hidden" name="type" value="">'.PHP_EOL;
		
		$content = '<div class="addItemModal row">'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="webpage" onclick="Lms.item.select(this);"><div class="text">웹페이지 링크</div></div></div></div>'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="youtube" onclick="Lms.item.select(this);"><div class="text">유튜브 링크</div></div></div></div>'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="video" onclick="Lms.item.select(this);"><div class="text">동영상 업로드</div></div></div></div>'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="slideshare" onclick="Lms.item.select(this);"><div class="text">슬라이드쉐어</div></div></div></div>'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="post" onclick="Lms.item.select(this);"><div class="text">직접작성</div></div></div></div>'.PHP_EOL;
		$content.= '<div class="col-sm-4 col-xs-6"><div class="item"><div class="box" data-type="upload" onclick="Lms.item.select(this);"><div class="text">파일업로드</div></div></div></div>'.PHP_EOL;
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
		
		if ($action == 'create') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->lid = Request('lid');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('create/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('create/help/content/error');
			$values->type = Request('type') ? Request('type') : $values->errors['type'] = $this->getLanguage('create/help/type/error');
			$values->attend = Request('attend') ? Request('attend') : $values->errors['attend'] = $this->getLanguage('create/help/attend/error');
			
			$values->lms = $this->getLms($values->lid);
			if ($values->lms->use_label != 'NONE') {
				$values->labels = is_array(Request('labels')) == true ? Request('labels') : array();
				if ($values->lms->use_label == 'FORCE' && count($values->labels) == 0) {
					$values->errors['labels'] = $this->getLanguage('create/help/labels/error');
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
				$insert['lid'] = $values->lid;
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				
				if ($values->idx == null) {
					$class = null;
					$reg_date = time();
					
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['reg_date'] = $reg_date;
					$insert['last_subject'] = $reg_date;
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					$values->idx = $this->db()->insert($this->table->class,$insert)->execute();
					
//					$this->IM->getModule('member')->sendPoint(null,$values->forum->post_point,'forum','post',array('idx'=>$values->idx));
//					$this->IM->getModule('member')->addActivity(null,$values->forum->post_exp,'forum','post',array('idx'=>$values->idx));
				} else {
					$class = $this->getClass($values->idx);
					$reg_date = $class->last_subject;
					
					if ($this->checkPermission('class_modify') == false && $class->midx != $this->IM->getModule('member')->getLogged()) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					}
					
					if ($results->success == true) {
						$this->db()->update($this->table->class,$insert)->where('idx',$class->idx)->execute();
						/*
						if ($post->midx != $this->IM->getModule('member')->getLogged()) {
							$this->IM->getModule('push')->sendPush($post->midx,'forum','post_modify',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
						}
						$this->IM->getModule('member')->addActivity(null,0,'forum','post_modify',array('idx'=>$values->idx));
						*/
					}
				}
				
				if ($results->success == true) {
					$cover = Request('cover');
					if ($cover && preg_match('/^data:image/',$cover) == true) {
						$temp = explode(',',$cover);
						$type = array_shift($temp);
						$fileType = 'jpg';
						if (preg_match('/^data:image\/(.*?);/',$type,$match) == true) {
							$fileType = $match[1];
						}
						$imageData = base64_decode(implode(',',$temp));
						
						$tempFileName = $this->IM->getModule('attachment')->getTempPath(true).'/'.md5($imageData);
						file_put_contents($tempFileName,$imageData);
						
						if ($this->IM->getModule('attachment')->createThumbnail($tempFileName,$tempFileName,420,560,false) == true) {
							if ($class == null || $class->cover == 0) {
								$coverIdx = $this->IM->getModule('attachment')->fileSave('cover.'.$fileType,$tempFileName,'lms','cover');
							} else {
								$coverIdx = $this->IM->getModule('attachment')->fileReplace($class->cover,'cover.'.$fileType,$tempFileName);
							}
							$this->db()->update($this->table->class,array('cover'=>$coverIdx))->where('idx',$values->idx)->execute();
						}
					}
					
					$labels = $this->db()->select($this->table->class_label)->where('idx',$values->idx)->get();
					for ($i=0, $loop=count($labels);$i<$loop;$i++) {
						if (in_array($labels[$i]->label,$values->labels) == false) {
							$this->db()->delete($this->table->class_label)->where('idx',$values->idx)->where('label',$labels[$i]->label)->execute();
							
							$lastClass = $this->db()->select($this->table->class_label)->where('label',$labels[$i]->label)->orderBy('reg_date','desc')->get();
							$classnum = count($lastClass);
							$lastClassTime = $classnum > 0 ? $lastClass[0]->reg_date : 0;
							$this->db()->update($this->table->label,array('classnum'=>$classnum,'last_class'=>$lastClassTime))->where('idx',$labels[$i]->label)->execute();
						}
					}
					
					if (count($values->labels) > 0) {
						for ($i=0, $loop=count($values->labels);$i<$loop;$i++) {
							if ($this->db()->select($this->table->class_label)->where('idx',$values->idx)->where('label',$values->labels[$i])->count() == 0) {
								$this->db()->insert($this->table->class_label,array('idx'=>$values->idx,'label'=>$values->labels[$i],'reg_date'=>$reg_date))->execute();
								
								$lastClass = $this->db()->select($this->table->class_label)->where('label',$values->labels[$i])->orderBy('reg_date','desc')->get();
								$classnum = count($lastClass);
								$lastClassTime = $classnum > 0 ? $lastClass[0]->reg_date : 0;
								$this->db()->update($this->table->label,array('classnum'=>$classnum,'last_class'=>$lastClassTime))->where('idx',$values->labels[$i])->execute();
							}
						}
					}
					
					$lastClass = $this->db()->select($this->table->class)->where('lid',$values->lid)->orderBy('last_subject','desc')->get();
					$classnum = count($lastClass);
					$lastClassTime = $classnum > 0 ? $lastClass[0]->last_subject : 0;
					$this->db()->update($this->table->lms,array('classnum'=>$classnum,'last_class'=>$lastClassTime))->where('lid',$values->lid)->execute();
					
					$page = $this->IM->getPages($values->menu,$values->page);
					
					if ($page->context->config == null) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'class',$values->idx);
					} elseif (in_array($page->context->config->label,$values->labels) == true) {
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'class',$values->idx);
					} else {
						$redirectPage = $this->getPostPage($values->idx);
						$results->redirect = $this->IM->getUrl($redirectPage->menu,$redirectPage->page,'class',$values->idx);
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'postSubject') {
			$values->type = Request('type');
			$values->parent = Request('parent');
			$class = $this->getClass($values->parent);
			
			if ($values->type == 'add' || $values->type == 'modify') {
				if ($class == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->IM->getModule('member')->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				} elseif ($this->checkPermission('add_subject') == true || $class->midx == $this->IM->getModule('member')->getLogged()) {
					$results->success = true;
					$results->modalHtml = $this->getPostSubjectModal($values->parent);
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				}
			} else {
				$values->errors = array();
				$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('addSubject/help/title/error');
				
				if ($class == null) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notFound');
				} elseif ($this->IM->getModule('member')->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				} elseif (count($values->errors) == 0) {
					$results->success = true;
					$sort = $this->db()->select($this->table->subject)->where('parent',$values->parent)->orderBy('sort','desc')->getOne();
					$sort = $sort == null ? 0 : $sort->sort + 1;
					$this->db()->insert($this->table->subject,array('lid'=>$class->lid,'parent'=>$values->parent,'midx'=>$this->IM->getModule('member')->getLogged(),'title'=>$values->title,'reg_date'=>time(),'sort'=>$sort))->execute();
					$lastSubject = $this->db()->select($this->table->subject)->where('parent',$values->parent)->orderBy('reg_date','desc')->get();
					$subject = count($lastSubject);
					$lastSubjectTime = $subject > 0 ? $lastSubject[0]->reg_date : 0;
					$this->db()->update($this->table->class,array('last_subject'=>$lastSubjectTime,'subject'=>$subject))->where('idx',$values->parent)->execute();
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/required');
					$results->errors = $values->errors;
				}
			}
		}
		
		if ($action == 'getConfig') {
			$values->type = Request('type');
			$values->idx = Request('idx');
			
			if ($values->type == 'post') {
				$post = $this->getPost($values->idx);
				$class = $this->getClass($post->class);
			} else {
				$subject = $this->getSubject($values->idx);
				$class = $this->getClass($subject->parent);
			}
			
			if ($class->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				
				if ($values->type == 'post') {
					$results->posts = array();
					$posts = $this->db()->select($this->table->post)->where('parent',$post->parent)->orderBy('sort','asc')->get();
					for ($i=0, $loop=count($posts);$i<$loop;$i++) {
						if ($posts[$i]->idx == $values->idx) continue;
						$results->posts[] = array('idx'=>$posts[$i]->idx,'title'=>$posts[$i]->title);
					}
				}
				
				$results->subjects = array();
				$subjects = $this->db()->select($this->table->subject)->where('parent',$class->idx)->orderBy('sort','asc')->get();
				for ($i=0, $loop=count($subjects);$i<$loop;$i++) {
					if ($values->type == 'post' && $post->parent == $subjects[$i]->idx) continue;
					$results->subjects[] = array('idx'=>$subjects[$i]->idx,'title'=>$subjects[$i]->title);
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'getPostContext') {
			$values->idx = Request('idx');
			$values->post = $this->getPost($values->idx);
			$values->attend = $this->getAttend($values->post->class);
			
			if ($values->post != null) {
				$results->success = true;
				$results->context = json_decode($values->post->context);
				
				if ($values->attend->mode == "P") {
					$trackings = $this->db()->select($this->table->tracking)->where('pidx',$values->idx)->get();
					$results->tracking = new stdClass();
					$results->tracking->last_position = 0;
					$results->tracking->tracking = array();
					for ($i=0;$i<$results->context->time;$i++) {
						$results->tracking->tracking[$i] = 0;
					}
					foreach ($trackings as $tracking) {
						for ($i=0;$i<$results->context->time;$i++) {
							$count = base_convert(substr($tracking->tracking,$i,1),32,10);
							$results->tracking->tracking[$i]+= $count;
						}
					}
				} else {
					$results->tracking = $this->db()->select($this->table->tracking)->where('midx',$this->IM->getModule('member')->getLogged())->where('pidx',$values->idx)->getOne();
					if ($results->tracking == null) {
						$results->tracking = new stdClass();
						$results->tracking->last_position = 0;
						$results->tracking->type = $values->post->type;
						if ($values->post->type == 'youtube' || $values->post->type == 'video') $results->tracking->tracking = 'T'.str_pad('',$results->context->time,'0');
					} else {
						if ($values->post->type == 'youtube' || $values->post->type == 'video') $results->tracking->tracking = 'T'.$results->tracking->tracking;
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			}
		}
		
		if ($action == 'tracking') {
			$values->pidx = Request('pidx');
			$values->post = $this->getPost($values->pidx);
			$values->midx = $this->IM->getModule('member')->getLogged();
			$values->last_position = Request('last_position');
			
			if ($values->post->type == 'youtube') {
				$context = json_decode($values->post->context);
				$values->tracking = substr(Request('tracking'),1);
				
				if (strlen($values->tracking) != $context->time) {
					$values->tracking = str_pad(substr($values->tracking,0,$context->time),$context->time,STR_PAD_RIGHT);
				}
				$values->percent = round(($context->time - substr_count($values->tracking,'0')) / $context->time * 100);
			}
			
			if ($this->db()->select($this->table->tracking)->where('midx',$values->midx)->where('pidx',$values->pidx)->count() == 0) {
				$this->db()->insert($this->table->tracking,array('midx'=>$values->midx,'pidx'=>$values->pidx,'tracking'=>$values->tracking,'percent'=>$values->percent,'last_position'=>$values->last_position,'reg_date'=>time(),'update_date'=>time()))->execute();
			} else {
				$this->db()->update($this->table->tracking,array('tracking'=>$values->tracking,'percent'=>$values->percent,'last_position'=>$values->last_position,'update_date'=>time()))->where('midx',$values->midx)->where('pidx',$values->pidx)->execute();
			}
			
			$results->success = true;
		}
		
		if ($action == 'addItem') {
			$values->parent = Request('parent');
			$subject = $this->getSubject($values->parent);
			
			if ($subject == null) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($this->checkPermission('add_item') == true || $subject->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = true;
				$results->modalHtml = $this->getAddItemModal($values->parent);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->lid = Request('lid');
			$values->idx = Request('idx');
			$values->parent = Request('parent');
			
			$values->subject = $this->getSubject($values->parent);
			$values->lms = $this->getLms($values->lid);
			
			$values->type = Request('type');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			$values->context = new stdClass();
			$values->progress_check = Request('progress_check') ? 'TRUE' : 'FALSE';
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if ($values->type == 'youtube') {
				$values->context->id = Request('id') ? Request('id') : $values->errors['url'] = $this->getLanguage('youtube/help/url/error');
				$values->context->thumbnail = Request('thumbnail');
				$values->context->afk_check = Request('afk_check') ? true : false;
				if ($values->context->afk_check == true) {
					$values->context->afk_check_time = preg_match('/^[1-9]+[0-9]*$/',Request('afk_check_time')) == true && Request('afk_check_time') >= 60 ? Request('afk_check_time') : $values->errors['afk_check_time'] = $this->getLanguage('youtube/help/afk_check_time/error');
				} else {
					$values->context->afk_check_time = 0;
				}
				
				if (preg_match('/^PT(([0-9]+)H)?(([0-9]+)M)?(([0-9]+)S)?$/',Request('time'),$time) == true) {
					$values->context->time = $time[2] * 3600 + $time[4] * 60 + $time[6];
				} else {
					$values->context->time = -1;
				}
				$values->context->caption = Request('caption') == "true";
			}
			
			if ($values->type == 'video') {
				$values->context->id = Request('id') ? Request('id') : $values->errors['file'] = $this->getLanguage('video/help/file/error');
				$values->context->privacy = Request('privacy');
				$values->context->afk_check = Request('afk_check') ? true : false;
				if ($values->context->afk_check == true) {
					$values->context->afk_check_time = preg_match('/^[1-9]+[0-9]*$/',Request('afk_check_time')) == true && Request('afk_check_time') >= 60 ? Request('afk_check_time') : $values->errors['afk_check_time'] = $this->getLanguage('youtube/help/afk_check_time/error');
				} else {
					$values->context->afk_check_time = 0;
				}
			}
			
			$values->context = json_encode($values->context);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif (empty($values->errors) == true) {
				$results->success = true;
				$mHash = new Hash();
				
				$insert = array();
				$insert['lid'] = $values->lid;
				$insert['class'] = $values->subject->parent;
				$insert['parent'] = $values->parent;
				$insert['type'] = $values->type;
				$insert['title'] = $values->title;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				$insert['context'] = $values->context;
				$insert['progress_check'] = $values->progress_check;
				$insert['reg_date'] = time();
				
				if ($values->idx == null) {
					$post = null;
					
					$sort = $this->db()->select($this->table->post)->where('parent',$values->parent)->orderBy('sort','desc')->getOne();
					$sort = $sort == null ? 0 : $sort->sort + 1;
					$insert['sort'] = $sort;
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
					
//					$this->IM->getModule('member')->sendPoint(null,$values->forum->post_point,'forum','post',array('idx'=>$values->idx));
//					$this->IM->getModule('member')->addActivity(null,$values->forum->post_exp,'forum','post',array('idx'=>$values->idx));
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
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'lid'=>$values->lid,'type'=>'POST','parent'=>$values->idx))->execute();
						}
					}
					
					$results->redirect = $this->IM->getUrl($values->menu,$values->page,'class',$values->subject->parent);
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $values->errors;
			}
		}
		
		if ($action == 'postView') {
			$values->idx = Request('idx');
			$values->post = $this->getPost($values->idx);
			$attend = $this->getAttend($values->post->class);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} else if ($attend == null) {
				$results->success = true;
				$results->modalHtml = $this->getAttendClassModal($values->post->class,$values->idx);
			} else if ($attend->status == 'ACTIVE') {
				$page = $this->getClassPage($values->post->class);
				$results->success = true;
				$results->redirect = $this->IM->getUrl($page->menu,$page->page,'view',$values->idx);
			} else {
				$results->success = false;
				// To Do : Message
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
		
		if ($action == 'attendClass') {
			$values->idx = Request('idx');
			$values->class = $this->getClass($values->idx);
			$values->post = Request('post');
			
			$attend = $this->getAttend($values->class->idx);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} else if ($attend == null) {
				$results->success = true;
				$status = 'ACTIVE';
				$this->db()->insert($this->table->attend,array('midx'=>$this->IM->getModule('member')->getLogged(),'class'=>$values->class->idx,'reg_date'=>time(),'status'=>$status))->execute();
				
				$student = $this->db()->select($this->table->attend)->where('class',$values->class->idx)->where('status','ACTIVE')->count();
				$this->db()->update($this->table->class,array('student'=>$student))->where('idx',$values->class->idx)->execute();
				if ($values->post != null) {
					$results->post = $values->post;
				} else {
					$page = $this->getClassPage($values->idx);
					$results->redirect = $this->IM->getUrl($page->menu,$page->page,'class',$values->idx);
				}
			} else if ($attend->mode == 'P') {
				$results->success = false;
				$results->message = $this->getLanguage('error/attendMyClass');
			} else {
				$results->success = false;
//				$results->message = $this->getLanguage('error/attendMyClass');
			}
		}
		
		if ($action == 'mentWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->fromidx = Request('fromidx') ? Request('fromidx') : 0;
			$values->parent = Request('parent');
			$values->post = $this->getPost($values->parent);
			$values->lms = $this->getLms($values->post->lid);
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('mentWrite/help/content/error');
			$values->position = is_numeric(Request('position')) == true ? Request('position') : 0;
			
			$results->success = true;
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($results->success == true && empty($values->errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['lid'] = $values->post->lid;
				$insert['parent'] = $values->parent;
				$insert['fromidx'] = $values->fromidx;
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['content'] = $values->content;
				$insert['position'] = $values->position;
				
				if ($values->idx == null) {
					$insert['reg_date'] = time();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					
					$values->idx = $this->db()->insert($this->table->ment,$insert)->execute();
					
					if ($values->post->midx != $this->IM->getModule('member')->getLogged()) {
//						$this->IM->getModule('push')->sendPush($values->post->midx,'forum','ment',$values->post->idx,array('idx'=>$values->idx,'from'=>$this->IM->getModule('member')->getLogged()));
					}
					
//					if ($source != 0 && $sourceData->midx != $this->IM->getModule('member')->getLogged()) {
//						$this->IM->getModule('push')->sendPush($sourceData->midx,'forum','replyment',$values->post->idx,array('idx'=>$values->idx,'from'=>$this->IM->getModule('member')->getLogged()));
//					}
					
//					$this->IM->getModule('member')->sendPoint(null,$values->forum->ment_point,'forum','ment',array('idx'=>$values->idx));
//					$this->IM->getModule('member')->addActivity(null,$values->forum->ment_exp,'forum','ment',array('idx'=>$values->idx));
				} else {
/*
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
*/
				}

				if ($results->success == true) {
					$lastMent = $this->db()->select($this->table->ment)->where('parent',$values->parent)->where('is_delete','FALSE')->orderBy('reg_date','desc')->get();
					$mentnum = count($lastMent);
					$lastMentTime = $mentnum > 0 ? $lastMent[0]->reg_date : $values->post->reg_date;
					$this->db()->update($this->table->post,array('ment'=>$mentnum,'last_ment'=>$lastMentTime))->where('idx',$values->parent)->execute();
					
//					$this->IM->setArticle('forum',$values->post->lid,'post',$values->parent,time());
//					$this->IM->setArticle('forum',$values->post->lid,'ment',$values->idx,time());
					
					$results->message = $this->getLanguage('mentWrite/success');
					$results->idx = $values->idx;
					$results->parent = $values->parent;
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
					$values->forum = $this->getLms($values->post->lid);
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
						$results->liveUpdate = 'liveUpdateLmsVote'.$values->idx;
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
			$values->parent = Request('parent');
			
			$ments = $this->db()->select($this->table->ment)->where('parent',$values->parent)->get();
			for ($i=0, $loop=count($ments);$i<$loop;$i++) {
				$member = $this->IM->getModule('member')->getMember($ments[$i]->midx);
				$ments[$i]->nickname = $member->nickname;
				$ments[$i]->photo = $member->photo;
			}
			
			$results->success = true;
			$results->ments = $ments;
		}
		
		if ($action == 'getMentStatus') {
			$values->parent = Request('parent');
			
			$status = array();
			for ($i=0;$i<20;$i++) {
				$status[$i] = array(
					'total'=>0
				);
			}
			$ments = $this->db()->select($this->table->ment)->where('parent',$values->parent)->get();
			for ($i=0, $loop=count($ments);$i<$loop;$i++) {
				$position = floor($ments[$i]->position / 5);
				$position = $position == 20 ? 19 : $position;
				
				$status[$position]['total']++;
				if (isset($status[$position][$ments[$i]->type]) == true) $status[$position][$ments[$i]->type]++;
				else $status[$position][$ments[$i]->type] = 1;
			}
			
			$results->success = true;
			$results->status = $status;
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
				$values->forum = $this->getLms($post->lid);
				
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
					
					$labels = $this->db()->select($this->table->class_label)->where('idx',$post->idx)->get();
					for ($i=0, $loop=count($labels);$i<$loop;$i++) {
						$this->db()->delete($this->table->class_label)->where('idx',$values->idx)->where('label',$labels[$i]->label)->execute();
							
						$lastPost = $this->db()->select($this->table->class_label)->where('label',$labels[$i]->label)->orderBy('reg_date','desc')->get();
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
				$values->forum = $this->getLms($post->lid);
				
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
			$this->IM->setArticle('forum',$posts[$i]->lid,'post',$posts[$i]->idx,$posts[$i]->last_ment);
		}
		
		$ments = $this->db()->select($this->table->ment)->where('is_delete','FALSE')->get();
		for ($i=0, $loop=count($ments);$i<$loop;$i++) {
			$this->IM->setArticle('forum',$ments[$i]->lid,'ment',$ments[$i]->idx,$ments[$i]->modify_date != 0 ? $ments[$i]->modify_date : $ments[$i]->reg_date);
		}
	}
}
?>