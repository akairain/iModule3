<?php
class ModuleQna {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $qnaPages = array();
	private $postPages = array();
	private $labelUrls = array();
	
	private $qnas = array();
	private $labels = array();
	private $posts = array();
	private $ments = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->qna = 'qna_table';
		$this->table->label = 'qna_label_table';
		$this->table->post = 'qna_post_table';
		$this->table->ment = 'qna_ment_table';
		$this->table->post_label = 'qna_post_label_table';
		$this->table->attachment = 'qna_attachment_table';
		$this->table->history = 'qna_history_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/qna.js');
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
	
	function getCountInfo($qid,$config) {
		$qna = $this->getQna($qid);
		if ($qna == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $qna->postnum;
			$info->last_time = $qna->last_post;
		} elseif (isset($config->category) == true) {
			if ($qna->use_category == 'USEDALL') {
				$count = $qna->postnum;
				$categorys = $this->db()->select($this->table->category)->where('qid',$qid)->get();
				for ($i=0, $loop=count($categorys);$i<$loop;$i++) {
					if ($categorys[$i]->idx != $config->category) $count = $count - $categorys[$i]->postnum;
				}
				$info->count = $count;
			} else {
				$info->count = $this->getCategory($config->category)->postnum;
			}
			$info->last_time = $this->getCategory($config->category)->last_post;
		} elseif (isset($config->label) == true) {
			$info->count = $this->getLabel($config->label)->postnum;
			$info->last_time = $this->getLabel($config->label)->last_post;
		}
		
		return $info;
	}
	
	function getContext($qid,$config=null) {
		$values = new stdClass();
		
		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		$qna = $this->getQna($qid);
		if ($qna == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($qid,$config);
		
		switch ($view) {
			case 'list' :
				$context.= $this->getListContext($qid,$config);
				break;
				
			case 'view' :
				$context.= $this->getViewContext($qid,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($qid,$config);
				break;
		}
		
		$context.= $this->getFooter($qid,$config);
		
		$this->IM->fireEvent('afterGetContext','qna',$view,null,null,$context);
		
		return $context;
	}
	
	function getQna($qid) {
		if (isset($this->qnas[$qid]) == true) return $this->qnas[$qid];
		$qna = $this->db()->select($this->table->qna)->where('qid',$qid)->getOne();
		if ($qna == null) {
			$this->qnas[$qid] = null;
		} else {
			$qna->templetPath = $this->Module->getPath().'/templets/'.$qna->templet;
			$qna->templetDir = $this->Module->getDir().'/templets/'.$qna->templet;
			
			$this->qnas[$qid] = $qna;
		}
		
		return $this->qnas[$qid];
	}
	
	function getPush($code,$fromcode,$content) {
		$latest = array_pop($content);
		$count = count($content);
		
		$push = new stdClass();
		$push->image = null;
		$push->link = null;
		if ($count > 0) $push->content = $this->getLanguage('push/'.$code.'s');
		else $push->content = $this->getLanguage('push/'.$code);
		
		if ($code == 'answer') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->parent,false,$page->domain);
		}
		
		if ($code == 'question_good' || $code == 'question_bad' || $code == 'answer_good' || $code == 'answer_bad') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			
			if ($code == 'question_bad' || $code == 'answer_bad') {
				$from = '';
				$push->image = $this->IM->getModule('member')->getMember(0)->photo;
			}
			
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->parent,false,$page->domain);
		}
		
		if ($code == 'selected') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->parent,false,$page->domain);
		}
		
		if ($code == 'ment') {
			$member = $this->IM->getModule('member')->getMember($latest->from);
			$from = $member->nickname;
			$push->image = $member->photo;
			$post = $this->getPost($fromcode);
			$title = GetCutString($post->title,15);
			$push->content = str_replace(array('{from}','{title}'),array('<b>'.$from.'</b>','<b>'.$title.'</b>'),$push->content);
			$page = $this->getPostPage($post->idx);
			$push->link = $this->IM->getUrl($page->menu,$page->page,'view',$post->parent,false,$page->domain);
		}
		
		$push->content = str_replace('{count}','<b>'.$count.'</b>',$push->content);
		return $push;
	}
	
	function getPost($idx) {
		if (isset($this->posts[$idx]) == true) return $this->posts[$idx];
		
		$this->posts[$idx] = $this->db()->select($this->table->post)->where('idx',$idx)->getOne();
		return $this->posts[$idx];
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
			$this->postPages[$idx] = $this->getQnaPage($post->qid,null,$domain);
			return $this->postPages[$idx];
		}
		
		$this->postPages[$idx] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'qna' && $pages[$i]->context->context == $post->qid && $pages[$i]->context->config != null && in_array($pages[$i]->context->config->label,$labels) == true) {
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
		
		$this->postPages[$idx] = $this->getQnaPage($post->qid);
		
		return $this->postPages[$idx];
	}
	
	function getQnaPage($qid,$label=null,$domain=null) {
		if (isset($this->qnaPages[$qid]) == true && $label == null) return $this->qnaPages[$qid];
		
		$this->qnaPages[$qid] = null;
		$sitemap = $this->IM->getPages(null,null,$domain);
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module' && $pages[$i]->context->module == 'qna' && $pages[$i]->context->context == $qid) {
					if ($label != null && $pages[$i]->context->config != null && $pages[$i]->context->config->label == $label) {
						return $pages[$i];
					}
					
					if ($label == null && $pages[$i]->context->config == null) {
						$this->qnaPages[$qid] = $pages[$i];
						return $this->qnaPages[$qid];
					}
					
					$this->qnaPages[$qid] = $this->qnaPages[$qid] == null ? $pages[$i] : $this->qnaPages[$qid];
				}
			}
		}
		
		if ($domain === null && $this->qnaPages[$qid] === null) {
			$sites = $this->IM->getSites();
			for ($i=0, $loop=count($sites);$i<$loop;$i++) {
				if ($this->getQnaPage($qid,$label,$sites[$i]->domain.'@'.$sites[$i]->language) !== null) {
					$this->qnaPages[$qid] = $this->getQnaPage($qid,$label,$sites[$i]->domain.'@'.$sites[$i]->language);
					break;
				}
			}
		}
		
		return $this->qnaPages[$qid];
	}
	
	function getLabelUrl($idx) {
		if (isset($this->labelUrls[$idx]) == true) return $this->labelUrls[$idx];
		
		$label = $this->getLabel($idx);
		$qna = $this->getQna($label->qid);
		
		$page = null;
		$sitemap = $this->IM->getPages();
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module') {
					if ($pages[$i]->context->module == 'qna' && $pages[$i]->context->context == $qna->qid && $pages[$i]->context->config != null && isset($pages[$i]->context->config->label) == true && $pages[$i]->context->config->label == $idx) {
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
	
	function getHeader($qid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$qna = $this->getQna($qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($qid,$config) {
		ob_start();
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$qna = $this->getQna($qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getArticle($type,$article,$isLink=false) {
		if (is_numeric($article) == true) $article = $this->getPost($article);
		$article->title = GetString($article->title,'replace');
		$article->is_image = preg_match('/<img(.*?)>/',$article->content);
		$article->is_file = $this->db()->select($this->table->attachment)->where('parent',$article->idx)->where('type','POST')->count() > 0;
		$article->is_link = preg_match('/<a(.*?)href(.*?)>/',$article->content);
		$article->is_video = preg_match('/<iframe(.*?)src=(.*?)(youtube|vimeo|daum|naver)(.*?)>/',$article->content);
		
		if ($isLink == true) {
			$page = $this->getPostPage($article->parent);
			$article->link = $this->IM->getUrl($page->menu,$page->page,'view',$article->parent,false,$page->domain,$page->language);
		}
		
		$article->member = $this->IM->getModule('member')->getMember($article->midx);
		$article->name = $this->IM->getModule('member')->getMemberNickname($article->midx,true);
		$article->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($article->content)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
		$article->ip = $this->getArticleIp($article->ip);
		
		return $article;
	}
	
	function getLabels($idx) {
		return $this->db()->select($this->table->post_label.' p')->join($this->table->label.' l','p.label=l.idx','LEFT')->where('p.idx',$idx)->get();
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
		return $this->db()->select($this->table->attachment)->where('idx',$file->idx)->getOne();
	}
	
	function getListContext($qid,$config) {
		ob_start();
		
		$this->IM->setView('list');
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'noindex,follow'));
		
		$label = empty($config->label) == true ? Request('label') : $config->label;
		
		$qna = $this->getQna($qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		if ($label == null) {
			$strQuery = $this->db()->select($this->table->post.' p','p.*')->where('p.qid',$qid);
		} else {
			$strQuery = $this->db()->select($this->table->post_label.' l','p.*, l.label')->join($this->table->post.' p','l.idx=p.idx','LEFT')->where('l.label',$label);
		}
		
		$strQuery = $strQuery->where('p.type','QUESTION');
		
		$keyword = Request('keyword');
		
		if ($keyword != null && strlen($keyword) > 0) {
			$strQuery = $strQuery->where('p.title,p.search',$keyword,'FULLTEXT');
		}
		
		$sort = Request('sort') ? Request('sort') : 'idx';
		
		if ($sort == 'new') {
			$strQuery = $strQuery->where('p.answer',0);
		} elseif ($sort == 'answer') {
			$strQuery = $strQuery->where('p.answer',0,'>');
		} elseif ($sort == 'mypost') {
			$strQuery = $strQuery->where('p.midx',$this->IM->getModule('member')->getLogged());
		}
		
		$p = Request('p') != null && is_numeric(Request('p')) == true ? Request('p') : 1;
		$p = $p < 1 ? 1 : $p;
		
		$startPosition = ($p-1) * $qna->postlimit;
		
		$totalCount = $strQuery->copy()->count();
		if ($sort == 'answer') $strQuery = $strQuery->orderBy('p.last_answer','desc');
		else $strQuery = $strQuery->orderBy('p.idx','desc');
		$totalPage = ceil($totalCount/$qna->postlimit);
		$lists = $strQuery->limit($startPosition,$qna->postlimit)->get();
		
		$pagination = GetPagination($p,$totalPage,$qna->pagelimit,'LEFT');
		
		$loopnum = $totalCount - ($p - 1) * $qna->postlimit;
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i] = $this->getArticle('post',$lists[$i]);
			$lists[$i]->loopnum = $loopnum - $i;
			$lists[$i]->link = $this->IM->getUrl(null,null,'view',$lists[$i]->idx).$this->IM->getQueryString();
		}
		
		echo '<form name="ModuleQnaListForm" onsubmit="return Qna.getListUrl(this);">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.$this->IM->page.'">'.PHP_EOL;
		echo '<input type="hidden" name="oKeyword" value="'.$keyword.'">'.PHP_EOL;
		echo '<input type="hidden" name="oLabel" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="oSort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="label" value="'.$label.'">'.PHP_EOL;
		echo '<input type="hidden" name="sort" value="'.$sort.'">'.PHP_EOL;
		echo '<input type="hidden" name="p" value="'.$p.'">'.PHP_EOL;
		
		
		$values = new stdClass();
		$values->lists = $lists;
		$this->IM->fireEvent('afterInitContext','qna',__FUNCTION__,$values);
		
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
	
	function getViewContext($qid,$config) {
		ob_start();
		
		$this->IM->setView('view');
		
		$idx = Request('idx');
		$post = $this->getArticle('post',$this->getPost($idx));
		
		$this->IM->addSiteHeader('link',array('rel'=>'canonical','href'=>$this->IM->getUrl(null,null,'view',$idx,true)));
		$this->IM->addSiteHeader('meta',array('name'=>'robots','content'=>'nofollow'));
		$this->IM->setSiteTitle($post->title);
		
		$qna = $this->getQna($qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		$this->db()->update($this->table->post,array('hit'=>$this->db()->inc()))->where('idx',$idx)->execute();
		
		if ($this->IM->getModule('member')->isLogged() == true) {
			$vote = $this->db()->select($this->table->history)->where('parent',$idx)->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
			$voted = $vote == null ? null : $vote->result;
		} else {
			$voted = null;
		}
		
		$attachments = $this->db()->select($this->table->attachment)->where('parent',$idx)->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$values = new stdClass();
		$values->post = $post;
		$values->attachments = $attachments;
		$this->IM->fireEvent('afterInitContext','qna',__FUNCTION__,$values);
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/view.php') == true) {
			INCLUDE $templetPath.'/view.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWriteContext($qid,$config) {
		ob_start();
		
		$this->IM->setView('write');
		
		$qna = $this->getQna($qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		if ($this->IM->getModule('member')->isLogged() == false) {
			return $this->getError($this->getLanguage('error/notLogged'));
		}
		
		if ($this->checkPermission('write') == false) {
			return $this->getError($this->getLanguage('error/forbidden'));
		}
		
		if ($qna->use_label != 'NONE') {
			$labels = $this->db()->select($this->table->label)->where('qid',$qid)->orderBy('postnum','desc')->get();
		} else {
			$labels = array();
		}
		
		$formName = 'ModuleQnaWriteForm-'.rand(10000,99999);
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Qna.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="qid" value="'.$qid.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Qna.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getAnswerList($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		
		$qna = $this->getQna($post->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		echo '<div id="ModuleQnaAnswerList-'.$parent.'" class="answerList">'.PHP_EOL;
		$lists = $this->db()->select($this->table->post)->where('parent',$parent)->where('type','ANSWER')->orderBy('vote','desc')->get();
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			echo $this->getAnswerItem($lists[$i]);
		}
		
		if (count($lists) == 0) echo '<div class="empty">'.$this->getLanguage('answerList/empty').'</div>'.PHP_EOL;
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getAnswerItem($answer) {
		ob_start();
		
		$qna = $this->getQna($answer->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		echo '<div id="ModuleQnaAnswerItem-'.$answer->idx.'" data-idx="'.$answer->idx.'" class="answerItem'.($answer->is_select == 'TRUE' ? ' selected' : '').'">'.PHP_EOL;
		
		$post = $this->getPost($answer->parent);
		$answer = $this->getArticle('post',$answer);
		
		$attachments = $this->db()->select($this->table->attachment)->where('type','POST')->where('parent',$answer->idx)->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		if ($this->IM->getModule('member')->isLogged() == true) {
			$vote = $this->db()->select($this->table->history)->where('parent',$answer->idx)->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
			$voted = $vote == null ? null : $vote->result;
		} else {
			$voted = null;
		}
		
		if ($post->is_select == 'FALSE' && ($this->checkPermission('select') == true || $post->midx == $this->IM->getModule('member')->getLogged())) $use_select = true;
		else $use_select = false;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/answer.item.php') == true) {
			INCLUDE $templetPath.'/answer.item.php';
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentList($parent) {
		ob_start();
		
		$answer = $this->getPost($parent);
		$question = $this->getPost($answer->parent);
		$qna = $this->getQna($answer->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		echo '<div id="ModuleQnaMentList-'.$parent.'" class="mentList">'.PHP_EOL;
		
		$lists = $this->db()->select($this->table->ment)->where('parent',$parent)->orderBy('idx','asc')->get();
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			echo $this->getMentItem($lists[$i]);
		}
		
		if (count($lists) == 0) echo '<div class="empty">'.$this->getLanguage('mentList/empty').'</div>'.PHP_EOL;
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getMentItem($ment) {
		ob_start();
		
		$answer = $this->getPost($ment->parent);
		$question = $this->getPost($answer->parent);
		$qna = $this->getQna($answer->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		$attachments = $this->db()->select($this->table->attachment)->where('type','MENT')->where('parent',$ment->idx)->get();
		for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
			$attachments[$i] = $this->IM->getModule('attachment')->getFileInfo($attachments[$i]->idx);
		}
		
		$ment->member = $this->IM->getModule('member')->getMember($ment->midx);
		$ment->name = $this->IM->getModule('member')->getMemberNickname($ment->midx,true);
		$ment->content = '<div class="wrapContent">'.AntiXSS($this->getArticleContent($ment->content)).'</div>'.PHP_EOL.'<div style="clear:both;"></div>';
		$ment->ip = $this->getArticleIp($ment->ip);
		
		if ($ment->is_secret == 'TRUE' && $answer->midx != $this->IM->getModule('member')->getLogged() && $question->midx != $this->IM->getModule('member')->getLogged()) {
			$ment->content = '<i class="fa fa-lock"></i> 질문자 및 답변자에게만 공개된 댓글입니다.';
		}
		
		echo '<div id="ModuleQnaMentItem-'.$ment->idx.'" class="mentItem">'.PHP_EOL;
		
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
		if ($this->IM->getModule('member')->getLogged() != 1 && $this->IM->getModule('member')->getLogged() != 2) return;
		
		$answer = $this->getPost($parent);
		$question = $this->getPost($answer->parent);
		
		if ($answer->midx != $this->IM->getModule('member')->getLogged() && $question->midx != $this->IM->getModule('member')->getLogged()) return;
		
		ob_start();
		
		$post = $this->getPost($parent);
		$qna = $this->getQna($post->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		echo '<div id="ModuleQnaMentWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleQnaMentForm-'.$parent.'" onsubmit="return Qna.ment.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/ment.write.php') == true) {
			INCLUDE $templetPath.'/ment.write.php';
		}
		
		echo '</form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Qna.ment.init("ModuleQnaMentForm-'.$parent.'"); });</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getAnswerWrite($parent) {
		ob_start();
		
		$post = $this->getPost($parent);
		$qna = $this->getQna($post->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		echo '<div id="ModuleQnaAnswerWrite-'.$parent.'">'.PHP_EOL;
		echo '<form name="ModuleQnaAnswerForm-'.$parent.'" onsubmit="return Qna.answer.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="">'.PHP_EOL;
		echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/answer.write.php') == true) {
			INCLUDE $templetPath.'/answer.write.php';
		}
		
		echo '</form>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Qna.answer.init("ModuleQnaAnswerForm-'.$parent.'"); });</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getSelect($idx) {
		ob_start();
		
		$post = $this->getPost($idx);
		$qna = $this->getQna($post->qid);
		$templetPath = $qna->templetPath;
		$templetDir = $qna->templetDir;
		
		$title = $this->getLanguage('answerSelect/title');
		echo '<form name="ModuleBoardSelectForm" onsubmit="return Qna.answer.select('.$idx.',true);">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage('answerSelect/confirm').'</div>'.PHP_EOL;
		
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
		$wysiwyg = $this->IM->getModule('wysiwyg')->setName($name)->setModule('qna');
		
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
	
	function checkPermission($type) {
		if ($this->IM->getModule('member')->isLogged() == true && $this->IM->getModule('member')->getMember()->type == 'ADMINISTRATOR') return true;
		
		switch ($type) {
			case 'write' :
				return $this->IM->getModule('member')->isLogged();
				
			case 'answer' :
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
			
			$oKeyword = Request('oKeyword');
			$oLabel = Request('oLabel');
			$oSort = Request('oSort');
			
			$keyword = Request('keyword');
			$label = Request('label');
			$sort = Request('sort');
			
			$keyword = Request('keyword');
			$p = Request('p') ? Request('p') : 1;
			
			$queryString = 'menu='.$page->menu.'&page='.$page->page.'&keyword='.$keyword.'&label='.$label.'&sort='.$sort.'&p='.$p;
			
			if ($oKeyword != $keyword || $oLabel != $label || $oSort != $sort) $p = 1;
			
			$default = array();
			if ($sort == 'idx') $default['sort'] = '';
			if (isset($page->context->config->label) == true && $page->context->config->label == $label) $default['label'] = '';
			
			$url = $this->IM->getUrl($page->menu,$page->page,'list',$p).$this->IM->getQueryString($default,$queryString);
			
			$results->success = true;
			$results->url = $url;
		}
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->qid = Request('qid');
			$values->qna = $this->getQna($values->qid);
			
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->title = Request('title') ? Request('title') : $values->errors['title'] = $this->getLanguage('postWrite/help/title/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			if (strlen(Request('point')) > 0 && Request('point') != '0') {
				$values->point = preg_match('/^[1-9]+[0-9]*$/',Request('point')) == true ? Request('point') : $values->errors['point'] = $this->getLanguage('postWrite/help/point/error');
			} else {
				$values->point = 0;
			}
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if ($values->qna->use_label != 'NONE') {
				$values->labels = is_array(Request('labels')) == true ? Request('labels') : array();
				if ($values->qna->use_label == 'FORCE' && count($values->labels) == 0) {
					$values->errors['labels'] = $this->getLanguage('postWrite/help/labels/error');
				}
			} else {
				$values->labels = array();
			}
			
			$this->IM->fireEvent('beforeDoProcess','qna',$action,$values,null);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} else {
				if ($values->point > $this->IM->getModule('member')->getMember()->point) {
					$values->errors['point'] = $this->getLanguage('error/notEnoughPoint');
				}
				
				if (empty($values->errors) == true) {
					$results->success = true;
					$mHash = new Hash();
					
					$insert = array();
					$insert['qid'] = $values->qid;
					$insert['type'] = 'QUESTION';
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['title'] = $values->title;
					$insert['content'] = $values->content;
					$insert['search'] = GetString($values->content,'index');
					$insert['point'] = $values->point;
					
					if ($this->checkPermission('write') == false) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} else {
						$results->success == true;
						$reg_date = time();
	
						$insert['reg_date'] = $reg_date;
						$insert['last_answer'] = $reg_date;
						$insert['ip'] = $_SERVER['REMOTE_ADDR'];
						
						$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
						$this->db()->update($this->table->post,array('parent'=>$values->idx))->where('idx',$values->idx)->execute();
						
						$this->IM->getModule('member')->sendPoint(null,$values->point,'qna','give',array('idx'=>$values->idx));
						$this->IM->getModule('member')->sendPoint(null,$values->qna->post_point,'qna','post',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->qna->post_exp,'qna','post',array('idx'=>$values->idx));
					}
					
					if ($results->success == true) {
						for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
							if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
								$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'qid'=>$values->qid,'type'=>'POST','parent'=>$values->idx))->execute();
							}
							$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
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
						
						$lastPost = $this->db()->select($this->table->post)->where('qid',$values->qid)->where('type','QUESTION')->orderBy('reg_date','desc')->get();
						$postnum = count($lastPost);
						$lastPostTime = $postnum > 0 ? $lastPost[0]->reg_date : 0;
						$this->db()->update($this->table->qna,array('postnum'=>$postnum,'last_post'=>$lastPostTime))->where('qid',$values->qid)->execute();
						
						$this->IM->setArticle('qna',$values->qid,'post',$values->idx,time());
						
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
		}
		
		if ($action == 'getAnswer') {
			$values->parent = Request('parent');
			
			$values->answers = array();
			$lists = $this->db()->select($this->table->post)->where('parent',$values->parent)->where('type','ANSWER')->orderBy('vote','desc')->get();
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$values->answers[$i] = array(
					'idx'=>$lists[$i]->idx,
					'vote'=>$lists[$i]->vote,
					'html'=>$this->getAnswerItem($lists[$i])
				);
			}
			
			$results->success = true;
			$results->parent = $values->parent;
			$results->answerCount = number_format($loop);
			$results->answers = $values->answers;
		}
		
		if ($action == 'answerWrite') {
			$values->errors = array();
			
			$values->parent = Request('parent');
			$values->post = $this->getPost($values->parent);
			$values->qna = $this->getQna($values->post->qid);
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($values->post->midx == $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = $this->getLanguage('error/mypost');
			} else {
				if (empty($values->errors) == true) {
					$mHash = new Hash();
					
					$insert = array();
					$insert['qid'] = $values->post->qid;
					$insert['type'] = 'ANSWER';
					$insert['parent'] = $values->parent;
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['title'] = $values->post->title;
					$insert['content'] = $values->content;
					$insert['search'] = GetString($values->content,'index');
					
					$insert['reg_date'] = time();
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
						
					if ($this->checkPermission('answer') == false) {
						$results->success = false;
						$results->message = $this->getLanguage('error/forbidden');
					} else {
						$results->success = true;
						$values->idx = $this->db()->insert($this->table->post,$insert)->execute();
					
						$this->IM->getModule('member')->sendPoint(null,$values->qna->answer_point,'qna','answer',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->qna->answer_exp,'qna','answer',array('idx'=>$values->idx));
						$this->IM->getModule('push')->sendPush($values->post->midx,'qna','answer',$values->parent,array('from'=>$this->IM->getModule('member')->getLogged()));
					}
					
					if ($results->success == true) {
						for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
							if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
								$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'qid'=>$values->post->qid,'type'=>'POST','parent'=>$values->idx))->execute();
							}
							$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
						}
						
						$lastAnswer = $this->db()->select($this->table->post)->where('parent',$values->parent)->where('type','ANSWER')->orderBy('reg_date','desc')->get();
						$answernum = count($lastAnswer);
						$lastAnswerTime = $answernum > 0 ? $lastAnswer[0]->reg_date : $values->post->reg_date;
						$this->db()->update($this->table->post,array('answer'=>$answernum,'last_answer'=>$lastAnswerTime))->where('idx',$values->parent)->execute();
						
						$this->IM->setArticle('qna',$values->post->qid,'post',$values->idx,time());
						
						$results->message = $this->getLanguage('answerWrite/success');
						$results->idx = $values->idx;
						$results->parent = $values->parent;
					}
				} elseif (count($values->errors) > 0) {
					$results->success = false;
					$results->message = $this->getLanguage('error/required');
					$results->errors = $values->errors;
				}
			}
		}
		
		if ($action == 'answerSelect') {
			$values->idx = Request('idx');
			$values->confirm = Request('confirm');
			
			$values->answer = $this->getPost($values->idx);
			
			if ($values->answer == null || $values->answer->type != 'ANSWER') {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} else {
				$values->post = $this->getPost($values->answer->parent);
				
				if ($this->checkPermission('select') == false && $values->post->midx != $this->getModule('member')->getLogged()) {
					$results->success = false;
					$results->message = $this->getLanguage('error/forbidden');
				} elseif ($values->post->is_select == 'TRUE') {
					$results->success = false;
					$results->message = $this->getLanguage('error/hasSelect');
				} else {
					$results->success = true;
					if ($values->confirm == 'TRUE') {
						$values->qna = $this->getQna($values->post->qid);
						$this->db()->update($this->table->post,array('is_select'=>'TRUE'))->where('idx',$values->idx)->execute();
						$this->db()->update($this->table->post,array('is_select'=>'TRUE'))->where('idx',$values->post->idx)->execute();
						
						$this->IM->getModule('member')->sendPoint(null,ceil($values->qna->select_point/2),'qna','select',array('idx'=>$values->idx));
						$this->IM->getModule('member')->sendPoint($values->answer->midx,$values->qna->select_point + $values->post->point,'qna','selected',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,ceil($values->qna->select_exp/2),'qna','select',array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity($values->answer->midx,$values->qna->select_exp,'qna','selected',array('idx'=>$values->idx));
						$this->IM->getModule('push')->sendPush($values->answer->midx,'qna','selected',$values->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
					} else {
						$results->modalHtml = $this->getSelect($values->idx);
					}
				}
			}
		}
		
		if ($action == 'getMent') {
			$values->parent = Request('parent');
			
			$values->ments = array();
			$lists = $this->db()->select($this->table->ment)->where('parent',$values->parent)->orderBy('idx','asc')->get();
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$values->ments[$i] = array(
					'idx'=>$lists[$i]->idx,
					'html'=>$this->getMentItem($lists[$i])
				);
			}
			
			$results->success = true;
			$results->parent = $values->parent;
			$results->mentCount = number_format($loop);
			$results->ments = $values->ments;
		}
		
		if ($action == 'mentWrite') {
			$values->errors = array();
			
			$values->parent = Request('parent');
			$values->answer = $this->getPost($values->parent);
			$values->question = $this->getPost($values->answer->parent);
			$values->qna = $this->getQna($values->question->qid);
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('postWrite/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif ($values->question->midx != $this->IM->getModule('member')->getLogged() && $values->answer->midx != $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			} else {
				if (empty($values->errors) == true) {
					$mHash = new Hash();
					
					$insert = array();
					$insert['qid'] = $values->qna->qid;
					$insert['parent'] = $values->parent;
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['content'] = $values->content;
					$insert['reg_date'] = time();
					$insert['is_secret'] = Request('is_secret') == 'TRUE' ? 'TRUE' : 'FALSE';
					$insert['ip'] = $_SERVER['REMOTE_ADDR'];
					
					$results->success = true;
					$values->idx = $this->db()->insert($this->table->ment,$insert)->execute();
					
					$this->IM->getModule('member')->addActivity(null,5,'qna','ment',array('idx'=>$values->idx));
					
					if ($values->question->midx == $this->IM->getModule('member')->getLogged()) {
						$this->IM->getModule('push')->sendPush($values->answer->midx,'qna','ment',$values->parent,array('from'=>$this->IM->getModule('member')->getLogged()));
					} else {
						$this->IM->getModule('push')->sendPush($values->question->midx,'qna','ment',$values->parent,array('from'=>$this->IM->getModule('member')->getLogged()));
					}
					
					if ($results->success == true) {
						for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
							if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
								$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'qid'=>$values->qna->qid,'type'=>'MENT','parent'=>$values->idx))->execute();
							}
							$this->IM->getModule('attachment')->filePublish($values->attachments[$i]);
						}
						
//						$this->IM->setArticle('qna',$values->post->qid,'post',$values->idx,time());
						
						$results->message = $this->getLanguage('answerWrite/success');
						$results->idx = $values->idx;
						$results->parent = $values->parent;
					}
				} elseif (count($values->errors) > 0) {
					$results->success = false;
					$results->message = $this->getLanguage('error/required');
					$results->errors = $values->errors;
				}
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
					$values->qna = $this->getQna($values->post->qid);
					$check = $this->db()->select($this->table->history)->where('parent',$values->idx)->where('midx',$this->IM->getModule('member')->getLogged())->getOne();
					if ($check == null) {
						if ($values->vote == 'good') {
							$this->db()->update($this->table->post,array('vote'=>$this->db()->inc()))->where('idx',$values->idx)->execute();
						} else {
							$this->db()->update($this->table->post,array('vote'=>$this->db()->dec()))->where('idx',$values->idx)->execute();
						}
						$this->db()->insert($this->table->history,array('parent'=>$values->idx,'midx'=>$this->IM->getModule('member')->getLogged(),'result'=>strtoupper($values->vote),'reg_date'=>time()))->execute();
						$results->success = true;
						$results->message = $this->getLanguage('vote/'.$values->vote);
						$results->liveUpdate = 'liveUpdateQnaVote'.$values->idx;
						$results->liveValue = number_format($values->vote == 'good' ? $values->post->vote + 1 : $values->post->vote - 1);
						
						$this->IM->getModule('member')->sendPoint(null,$values->qna->vote_point,'qna',strtolower($values->post->type).'_'.$values->vote,array('idx'=>$values->idx));
						$this->IM->getModule('member')->addActivity(null,$values->qna->vote_exp,'qna',strtolower($values->post->type).'_'.$values->vote,array('idx'=>$values->idx));
						$this->IM->getModule('push')->sendPush($values->post->midx,'qna',strtolower($values->post->type).'_'.$values->vote,$values->post->idx,array('from'=>$this->IM->getModule('member')->getLogged()));
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('vote/duplicated');
						$results->result = $check->result;
					}
				}
			}
		}
		
		$this->IM->fireEvent('afterDoProcess','qna',$action,$values,$results);
		
		return $results;
	}
	
	function deleteAttachment($idx) {
		$this->db()->delete($this->table->attachment)->where('idx',$idx)->execute();
	}
	
	function resetArticle() {
		$posts = $this->db()->select($this->table->post)->get();
		for ($i=0, $loop=count($posts);$i<$loop;$i++) {
			$this->IM->setArticle('qna',$posts[$i]->qid,'post',$posts[$i]->idx,$posts[$i]->reg_date);
		}
	}
}
?>