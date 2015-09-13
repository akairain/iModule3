<?php
class iModule {
	public $DB;
	
	public $language;
	public $menu;
	public $page;
	public $view;
	public $idx;
	
	public $sites = array();
	public $menus = array();
	public $pages = array();
	
	public $Event;
	public $Addon;
	public $Module;
	public $Cache;
	
	private $initTime = 0;
	private $table;
	
	public $modules = array();
	public $timezone;
	public $domain;
	public $site;
	private $siteTitle = null;
	private $siteDescription = null;
	private $siteCanonical = null;
	private $siteImage = null;
	private $siteHeader = array();
	private $templetPath = null;
	private $templetDir = null;
	private $useTemplet = true;
	
	function __construct() {
		global $_CONFIGS;
		
		$this->initTime = $this->getMicroTime();
		$this->DB = new DB();
		
		if ($_CONFIGS->installed === true) {
			$this->Event = new Event($this);
			$this->Addon = new Addon($this);
			$this->Module = new Module($this);
			$this->Cache = new Cache($this);
		}
		
		$this->table = new stdClass();
		$this->table->site = 'site_table';
		$this->table->page = 'page_table';
		$this->table->article = 'article_table';

		$this->timezone = 'Asia/Seoul';
		$this->domain = strtolower($_SERVER['HTTP_HOST']);
		$this->site = null;
		$this->language = Request('language');
		$this->menu = Request('menu') == null ? 'index' : Request('menu');
		$this->page = Request('page') == null ? null : Request('page');
		$this->view = Request('view') == null ? null : Request('view');
		$this->idx = Request('idx') == null ? null : Request('idx');
		
		date_default_timezone_set($this->timezone);
		
		$this->addSiteHeader('script',__IM_DIR__.'/scripts/jquery.1.11.2.min.js');
		$this->addSiteHeader('script',__IM_DIR__.'/scripts/default.js');
		$this->addSiteHeader('script',__IM_DIR__.'/scripts/moment.js');
	}
	
	function db($code='default',$prefix=null) {
		$db = new DB();
		$prefix = $prefix == null ? __IM_DB_PREFIX__ : $prefix;
		return $db->db($code,$prefix);
	}
	
	function cache() {
		return $this->Cache;
	}
	
	function fireEvent($event,$target,$caller,$values=null,$results=null,&$context=null) {
		$this->Event->fireEvent($event,$target,$caller,$values,$results,$context);
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getMicroTime() {
		$microtimestmp = explode(" ",microtime());
		return $microtimestmp[0]+$microtimestmp[1];
	}
	
	function getLoadTime() {
		return sprintf('%0.5f',$this->getMicroTime() - $this->initTime);
	}
	
	function getHost($isDir=false) {
		$url = isset($_SERVER['HTTPS']) == true ? 'https://' : 'http://';
		$url.= $_SERVER['HTTP_HOST'];
		if ($isDir == true) $url.= __IM_DIR__;
		
		return $url;
	}
	
	function getAttachmentPath() {
		return __IM_PATH__.'/attachments';
	}
	
	function getAttachmentDir() {
		return __IM_DIR__.'/attachments';
	}
	
	function getProcessUrl($module,$action,$params=array(),$isFullUrl=false) {
		$queryStrings = array();
		foreach ($params as $key=>$value) $queryStrings[] = $key.'='.urlencode($value);
		
		if ($isFullUrl == true) {
			$url = isset($_SERVER['HTTPS']) == true ? 'https://' : 'http://';
			$url.= $_SERVER['HTTP_HOST'].__IM_DIR__;
		} else {
			$url = '';
		}
		
		return $url.__IM_DIR__.'/'.$this->language.'/process/'.$module.'/'.$action.(count($queryStrings) > 0 ? '?'.implode('&',$queryStrings) : '');
	}
	
	function getUrl($menu=null,$page=null,$view=null,$number=null,$isFullUrl=false,$domain=null,$language=null) {
		$menu = $menu === null ? $this->menu : $menu;
		$page = $page === null && $menu == $this->menu ? $this->page : $page;
		$view = $view === null && $menu == $this->menu && $page == $this->page ? $this->view : $view;
		
		if ($isFullUrl == true || $domain !== $this->site->domain) {
			$check = $this->db()->select($this->table->site)->where('domain',$domain)->getOne();
			if ($check == null) {
				$url = isset($_SERVER['HTTPS']) == true ? 'https://' : 'http://';
				$url.= ($domain === null ? $_SERVER['HTTP_HOST'] : $domain).__IM_DIR__;
			} else {
				$url = $check->is_ssl == 'TRUE' ? 'https://' : 'http://';
				$url.= ($domain === null ? $_SERVER['HTTP_HOST'] : $domain).__IM_DIR__;
			}
		} else {
			$url = __IM_DIR__;
		}
		$url.= '/'.($language == null ? $this->language : $language);
		if ($menu === null || $menu === false) return $url;
		$url.= '/'.$menu;
		if ($page === null || $page === false) return $url;
		$url.= '/'.$page;
		if ($view === null || $view === false) return $url;
		$url.= '/'.$view;
		if ($number === null || is_numeric($number) == false) return $url;
		$url.= '/'.$number;
		
		return $url;
	}
	
	function getQueryString($query=array(),$queryString=null) {
		$queryString = $queryString == null ? $_SERVER['QUERY_STRING'] : $queryString;
		$query = array_merge(array('menu'=>'','page'=>'','view'=>'','idx'=>'','p'=>'','language'=>''),$query);
		$querys = explode('&',$queryString);
		
		for ($i=0, $total=count($querys);$i<$total;$i++) {
			$temp = explode('=',$querys[$i]);
			if (isset($temp[1]) == true) {
				$arg[$temp[0]] = $temp[1];
			}
		}
	
		//replace
		foreach ($query as $key=>$value) {
			$arg[$key] = $value;
		}
	
		//sum
		$queryString = '';
	
		foreach ($arg as $key=>$value) {
			if (strlen($value) > 0) {
				$queryString.= $queryString == '' ? '?' : '&';
				$queryString .= $key."=".$value;
			}
		}
		
		return $queryString;
	}
	/*
	function getMenuTitle($menu=null) {
		$menu = $menu == null ? $this->menu : $menu;
		$menu = $this->db()->select($this->table->page)->where('menu',$menu)->where('page','')->getOne();
		if ($menu == null) return '';
		else return $menu->title;
	}
	*/
	
	function getSites($domain=null) {
		if ($domain == null) return $this->sites;
		for ($i=0, $loop=count($this->sites);$i<$loop;$i++) {
			if ($this->sites[$i]->domain == $menu) return $this->sites[$i];
		}
		return null;
	}
	
	function getMenus($menu=null,$domain=null) {
		$domain = $domain === null ? $this->domain : $domain;
		if (count(explode('@',$domain)) == 1) $domain = $domain.'@'.$this->language;
		if (empty($this->menus[$domain]) == true) return $menu == null ? array() : null;
		if ($menu == null) return $this->menus[$domain];
		
		for ($i=0, $loop=count($this->menus[$domain]);$i<$loop;$i++) {
			if ($this->menus[$domain][$i]->menu == $menu) return $this->menus[$domain][$i];
		}
		return null;
	}
	
	function getPages($menu=null,$page=null,$domain=null) {
		$domain = $domain === null ? $this->domain : $domain;
		if (count(explode('@',$domain)) == 1) $domain = $domain.'@'.$this->language;
		if (empty($this->menus[$domain]) == true) return $page == null ? array() : null;
		if ($menu == null) return $this->pages[$domain];
		if ($page == null) return $this->pages[$domain][$menu];
		
		for ($i=0, $loop=count($this->pages[$domain][$menu]);$i<$loop;$i++) {
			if ($this->pages[$domain][$menu][$i]->page == $page) return $this->pages[$domain][$menu][$i];
		}
		return null;
	}
	
	function getPageCountInfo($page) {
		if ($page->type == 'module') {
			$module = $this->getModule($page->context->module);
			if (method_exists($module,'getCountInfo') == true) {
				return $module->getCountInfo($page->context->context,$page->context->config);
			} else {
				return null;
			}
		}
		
		return null;
	}
	
	function setView($view) {
		$this->view = $view;
	}
	
	function setIdx($idx) {
		$this->view = $view;
	}
	
	function removeTemplet() {
		$this->useTemplet = false;
	}
	
	function getModule($module) {
		if (isset($this->modules[$module]) == false) {
			$class = new Module($this);
			$this->modules[$module] = $class->load($module);
		}
		
		if ($this->modules[$module] === false) $this->printError('LOAD_MODULE_FAIL');
		return $this->modules[$module];
	}
	
	function getWidget($widget) {
		$class = new Widget($this);
		return $class->load($widget);
	}
	
	function getSite() {
		if (count($this->sites) == 0) {
			$this->sites = $this->db()->select($this->table->site)->orderBy('sort','asc')->get();
			for ($i=0, $loop=count($this->sites);$i<$loop;$i++) {
				$this->menus[$this->sites[$i]->domain] = array();
				$this->pages[$this->sites[$i]->domain] = array();
			}
		}
		if ($this->site != null) return $this->site;
		
		if ($this->language === null) {
			$site = $this->db()->select($this->table->site)->where('domain',$this->domain)->where('is_default','TRUE')->getOne();
			if ($site != null && $site->is_ssl == 'TRUE' && empty($_SERVER['HTTPS']) == true && preg_match('/\/(api|process)\/index\.php/',$_SERVER['PHP_SELF']) == false) {
				header("location:https://".$site->domain.$_SERVER['REQUEST_URI']);
				exit;
			}
			
			if ($site == null && preg_match('/\/(api|process)\/index\.php/',$_SERVER['PHP_SELF']) == false) {
				$alias = $this->db()->select($this->table->site,'*')->where('alias','','!=')->where('is_default','TRUE')->orderBy('sort','asc')->get();
				for ($i=0, $loop=count($alias);$i<$loop;$i++) {
					$domains = explode(',',$alias[$i]->alias);
					for ($j=0, $loopj=count($domains);$j<$loopj;$j++) {
						if ($domains[$j] == $this->domain) {
							header('location://'.$alias[$i]->domain.__IM_DIR__.'/');
							exit;
						}
						
						if (preg_match('/\*\./',$domains[$j]) == true) {
							$aliasToken = explode('.',$domains[$j]);
							$domainToken = explode('.',$this->domain);
							$isMatch = true;
							while (count($aliasToken) > 0) {
								$token = array_pop($aliasToken);
								if ($token != '*' && $token != array_pop($domainToken)) {
									$isMatch = false;
								}
							}
							if ($isMatch == true) {
								header('location://'.$alias[$i]->domain.__IM_DIR__.'/');
								exit;
							}
						}
					}
				}
				$this->printError('');
			}
		} else {
			$site = $this->db()->select($this->table->site)->where('domain',$this->domain)->where('language',$this->language)->getOne();
			if ($site != null && $site->is_ssl == 'TRUE' && empty($_SERVER['HTTPS']) == true && preg_match('/\/(api|process)\/index\.php/',$_SERVER['PHP_SELF']) == false) {
				header("location:https://".$site->domain.$_SERVER['REQUEST_URI']);
				exit;
			}
			
			if ($site == null && preg_match('/\/(api|process)\/index\.php/',$_SERVER['PHP_SELF']) == false) {
				$alias = $this->db()->select($this->table->site,'*')->where('alias','','!=')->where('language',$this->language)->orderBy('sort','asc')->get();
				for ($i=0, $loop=count($alias);$i<$loop;$i++) {
					$domains = explode(',',$alias[$i]->alias);
					for ($j=0, $loopj=count($domains);$j<$loopj;$j++) {
						if ($domains[$j] == $this->domain) {
							header('location://'.$alias[$i]->domain.__IM_DIR__.'/'.$this->language.'/');
							exit;
						}
						
						if (preg_match('/\*\./',$domains[$j]) == true) {
							$aliasToken = explode('.',$domains[$j]);
							$domainToken = explode('.',$this->domain);
							$isMatch = true;
							while (count($aliasToken) > 0) {
								$token = array_pop($aliasToken);
								if ($token != '*' && $token != array_pop($domainToken)) {
									$isMatch = false;
								}
							}
							if ($isMatch == true) {
								header('location://'.$alias[$i]->domain.__IM_DIR__.'/'.$this->language.'/');
								exit;
							}
						}
					}
				}
				header('location://'.$this->domain.__IM_DIR__.'/');
			}
		}
		
		if ($site == null) return;
		
		$this->site = $site;
		$this->site->logo = json_decode($this->site->logo);
		$this->site->emblem = $this->site->emblem == 0 ? null : __IM_DIR__.'/attachment/view/'.$this->site->emblem.'/emblem.png';
		$this->site->favicon = $this->site->favicon == 0 ? null : __IM_DIR__.'/attachment/view/'.$this->site->favicon.'/favicon.ico';
		$this->site->image = $this->site->image == 0 ? null : __IM_DIR__.'/attachment/view/'.$this->site->image.'/preview.png';
		$this->site->description = $this->site->description ? $this->site->description : null;
		$this->language = $this->language == null ? $this->site->language : $this->language;
		
		$this->menus[$this->domain.'@'.$this->language] = array();
		
		$pages = $this->db()->select($this->table->page)->orderBy('sort','asc')->get();
		for ($i=0, $loop=count($pages);$i<$loop;$i++) {
			if ($pages[$i]->page == '') {
				$pages[$i]->context = $pages[$i]->context == '' ? null : json_decode($pages[$i]->context);
				if ($pages[$i]->type == 'module') {
					$pages[$i]->context->config = isset($pages[$i]->context->config) == true ? $pages[$i]->context->config : null;
				}
				$pages[$i]->description = isset($pages[$i]->description) == true && $pages[$i]->description ? $pages[$i]->description : null;
				$pages[$i]->image = isset($pages[$i]->image) == true && $pages[$i]->image ? $pages[$i]->image : null;
				
				if ($pages[$i]->domain == '*') $pages[$i]->domain = $this->domain;
				if ($pages[$i]->language == '*') $pages[$i]->language = $this->language;
				$this->menus[$pages[$i]->domain.'@'.$pages[$i]->language][] = $pages[$i];
				$this->pages[$pages[$i]->domain.'@'.$pages[$i]->language][$pages[$i]->menu] = array();
			}
		}
		
		for ($i=0, $loop=count($pages);$i<$loop;$i++) {
			if ($pages[$i]->page != '') {
				$pages[$i]->context = $pages[$i]->context == '' ? null : json_decode($pages[$i]->context);
				$pages[$i]->description = isset($pages[$i]->description) == true && $pages[$i]->description ? $pages[$i]->description : null;
				$pages[$i]->image = isset($pages[$i]->image) == true && $pages[$i]->image ? $pages[$i]->image : null;
				$pages[$i]->context->config = isset($pages[$i]->context->config) == true ? $pages[$i]->context->config : null;
				if ($pages[$i]->domain == '*') $pages[$i]->domain = $this->domain;
				if ($pages[$i]->language == '*') $pages[$i]->language = $this->language;
				$this->pages[$pages[$i]->domain.'@'.$pages[$i]->language][$pages[$i]->menu][] = $pages[$i];
			}
		}
		
		return $this->site;
	}
	
	function getSiteLogo($type='default') {
		if ($type == 'default' && empty($this->site->logo->default) == true) return null;
		if (empty($this->site->logo->$type) == true) return $this->getSiteLogo();
		
		return __IM_DIR__.'/attachment/view/'.$this->site->logo->$type.'/logo.png';
	}
	
	function getSiteEmblem() {
		return $this->site->emblem;
	}
	
	function getSiteTitle() {
		if ($this->siteTitle == null) {
			$site = $this->getSite();
			$this->siteTitle = $site->title;
		}
		
		return $this->siteTitle;
	}
	
	function setSiteTitle($title,$isSiteTitle=true) {
		$this->siteTitle = $isSiteTitle == true ? $this->site->title.' - '.$title : $title;
	}
	
	function getSiteDescription() {
		if ($this->siteDescription !== null) return $this->siteDescription;
		
		if ($this->menu != 'index' && $this->menu != 'account') {
			$menu = $this->getMenus($this->menu);
			$page = $this->page !== null ? $this->getPages($this->menu,$this->page) : null;
			$description = $page !== null && $page->description !== null ? $page->description : $menu->description;
			return $description !== null ? $description : $this->site->description;
		}
		return $this->site->description;
	}
	
	function setSiteDescription($description) {
		$this->siteDescription = $description;
	}
	
	function getSiteCanonical() {
		return $this->siteCanonical !== null ? $this->siteCanonical : $this->getHost(false).$_SERVER['REQUEST_URI'];
	}
	
	function setSiteCanonical($canonical) {
		$this->siteCanonical = $canonical;
	}
	
	function getSiteImage($isFullUrl=false) {
		$url = $isFullUrl == true ? isset($_SERVER['HTTPS']) == true ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'] : '';
		
		if ($this->siteImage !== null) return $url.$this->siteImage;
		
		if ($this->menu != 'index' && $this->menu != 'account') {
			$page = $this->page ? $this->getPages($this->menu,$this->page) : $this->getMenus($this->menu);
			return $page->image !== null ? $url.$page->image : $url.$this->site->image;
		}
		return $url.$this->site->image;
	}
	
	function setSiteImage($image) {
		$this->siteImage = $image;
	}
	
	function getSiteHeader() {
		return implode(PHP_EOL,$this->siteHeader).PHP_EOL;
	}
	
	function getTempletPath() {
		if ($this->templetPath == null) {
			$site = $this->getSite();
			$this->templetPath = __IM_PATH__.'/templets/'.$site->templet;
		}
		
		return $this->templetPath;
	}
	
	function getTempletDir() {
		if ($this->templetDir == null) {
			$site = $this->getSite();
			$this->templetDir = __IM_DIR__.'/templets/'.$site->templet;
		}
		
		return $this->templetDir;
	}
	
	function setArticle($module,$context,$type,$idx,$reg_date) {
		$check = $this->db()->select($this->table->article)->where('module',$module)->where('type',$type)->where('idx',$idx)->get();
		if ($check == null) {
			$this->db()->insert($this->table->article,array('module'=>$module,'context'=>$context,'type'=>$type,'idx'=>$idx,'reg_date'=>$reg_date,'update_date'=>$reg_date))->execute();
		} else {
			$this->db()->update($this->table->article,array('context'=>$context,'update_date'=>$reg_date))->where('module',$module)->where('type',$type)->where('idx',$idx)->execute();
		}
	}
	
	function deleteArticle($module,$type,$idx) {
		$this->db()->delete($this->table->article)->where('module',$module)->where('type',$type)->where('idx',$idx)->execute();
	}
	
	function resetArticle() {
		$this->db()->delete($this->table->article)->execute();
		$this->Module->resetArticle();
	}
	
	function addSiteHeader($type,$value) {
		switch ($type) {
			case 'style' :
				$tag = '<link rel="stylesheet" href="'.$value.'" type="text/css">';
				break;
				
			case 'script' :
				$tag = '<script src="'.$value.'"></script>';
				break;
				
			default :
				$tag = '<';
				$tag.= $type;
				foreach ($value as $tagName=>$tagValue) {
					$tag.= ' '.$tagName.'="'.$tagValue.'"';
				}
				$tag.= '>';
		}
		
		if (in_array($tag,$this->siteHeader) == false) $this->siteHeader[] = $tag;
	}
	
	function printError($code,$message='') {
		if ($this->site == null) {
			echo 'ERROR!';
		} else {
			echo $this->printHeader();
			echo '<b>ERROR : </b>'.$code;
			echo $this->printFooter();
		}
		exit;
	}
	
	function printHeader() {
		if (defined('__IM_HEADER_INCLUDED__') == true) return;
		$site = $this->getSite();
		
		if (file_exists(__IM_PATH__.'/styles/'.$this->language.'.css') == true) $this->addSiteHeader('style',__IM_DIR__.'/styles/'.$this->language.'.css');
		$this->addSiteHeader('style',__IM_DIR__.'/styles/default.css');
		$this->addSiteHeader('style',__IM_DIR__.'/styles/font-awesome.min.css');
		
		$IM = $this;
		$values = new stdClass();
		$values->header = '';
		
		if ($this->getSiteDescription()) $this->addSiteHeader('meta',array('name'=>'description','content'=>$this->getSiteDescription()));
		$this->addSiteHeader('link',array('rel'=>'canonical','href'=>$this->getSiteCanonical()));
		
		if ($this->site->emblem !== null) {
			$IM->addSiteHeader('link',array('rel'=>'apple-touch-icon','sizes'=>'57x57','href'=>$this->site->emblem));
			$IM->addSiteHeader('link',array('rel'=>'apple-touch-icon','sizes'=>'114x114','href'=>$this->site->emblem));
			$IM->addSiteHeader('link',array('rel'=>'apple-touch-icon','sizes'=>'72x72','href'=>$this->site->emblem));
			$IM->addSiteHeader('link',array('rel'=>'apple-touch-icon','sizes'=>'144x144','href'=>$this->site->emblem));
		}
		
		if ($this->site->favicon !== null) {
			$IM->addSiteHeader('link',array('rel'=>'shortcut icon','type'=>'image/x-icon','href'=>$this->site->favicon));
		}
		
		ob_start();
		if ($this->useTemplet == false || file_exists($this->getTempletPath().'/header.php') == false) {
			INCLUDE __IM_PATH__.'/includes/header.php';
		} else {
			INCLUDE $this->getTempletPath().'/header.php';
		}
		
		$values->header = ob_get_contents();
		ob_end_clean();
		
		return $values->header;
	}
	
	function printFooter() {
		if (defined('__IM_FOOTER_INCLUDED__') == true) return;
		$site = $this->getSite();
		
		$IM = $this;
		$values = new stdClass();
		$values->footer = '';
		
		ob_start();
		if ($this->useTemplet == false || file_exists($this->getTempletPath().'/footer.php') == false) {
			INCLUDE __IM_PATH__.'/includes/footer.php';
		} else {
			INCLUDE $this->getTempletPath().'/footer.php';
		}
		
		$values->footer = ob_get_contents();
		ob_end_clean();
		
		return $values->footer;
	}
	
	function getContext($page) {
		$context = '';
		
		if ($page != null) {
			$this->setSiteTitle($page->title);
			
			if ($page->type == 'page') {
				$this->page = $page->context->page;
				$context = $this->getContext($this->getPages($page->menu,$page->context->page));
			} elseif ($page->type == 'external') {
				$context = $this->getExternalContext($page->context->external);
			} elseif ($page->type == 'widget') {
				$context = $this->getWidgetContext($page->context->widget);
			} elseif ($page->type == 'module') {
				$context = $this->getModule($page->context->module)->getContext($page->context->context,$page->context->config);
			}
		}
		
		return $context;
	}
	
	function getPage($config,$context) {
		$IM = $this;
		$page = '';
		
		ob_start();
		if ($this->useTemplet == false || file_exists($this->getTempletPath().'/layouts/'.$config->layout.'.php') == false) {
			echo $context;
		} else {
			INCLUDE $this->getTempletPath().'/layouts/'.$config->layout.'.php';
		}
		$page = ob_get_contents();
		ob_end_clean();
		
		return $page;
	}
	
	function getExternalContext($external) {
		$IM = $this;
		$values = new stdClass();
		$values->context = '';
		
		if (file_exists(__IM_PATH__.'/externals/'.$external) == true) {
			ob_start();
			INCLUDE __IM_PATH__.'/externals/'.$external;
			$values->context = ob_get_contents();
			ob_end_clean();
		}
		
		return $values->context;
	}
	
	function getWidgetContext($widgets) {
		ob_start();
		
		foreach ($widgets as $row) {
			echo '<div class="row">'.PHP_EOL;
			foreach ($row as $col) {
				echo '<div class="col-sm-'.$col->col.'">'.PHP_EOL;
				
				$widget = $this->getWidget($col->widget)->setTemplet($col->templet);
				foreach ($col->values as $key=>$value) {
					$widget->setValue($key,$value);
				}
				$widget->doLayout();
				
				echo '</div>'.PHP_EOL;
			}
			echo '</div>'.PHP_EOL;
		}
		
		$widget = ob_get_contents();
		ob_end_clean();
		
		return $widget;
	}
	
	function doLayout() {
		$this->Module->loadGlobals();

		$IM = $this;

		$site = $this->getSite();
		$values = new stdClass();
		
		if ($this->menu == 'account') {
			$page = $this->getModule('member')->getAccountPage();
		} else {
			$page = $this->page == null ? $this->getMenus($this->menu) : $this->getPages($this->menu,$this->page);
			
			if ($page == null) {
				header("HTTP/1.1 404 Not Found");
				$this->printError('PAGE_NOT_FOUND');
			}
		}
		
		$context = $this->getContext($page);
		
		$this->fireEvent('afterGetContext','core','doLayout',$values,null,$context);
		
		$values->page = $this->getPage($page,$context);
		
		$this->addSiteHeader('script',__IM_DIR__.'/scripts/php2js.js.php?language='.$this->language.'&menu='.($this->menu != null ? $this->menu : '').'&page='.($this->page != null ? $this->page : '').'&view='.($this->view != null ? $this->view : ''));
		
		$values->footer = $this->printFooter();
		$values->header = $this->printHeader();
		
		echo $values->header;
		echo $values->page;
		echo $values->footer;
		
		echo '<script>console.log("Load Time : '.$this->getLoadTime().'");</script>';
		echo PHP_EOL.'<!-- Load Time : '.$this->getLoadTime().' -->';
	}
	
	function getCurrentURL($menu=false,$page=false,$view=false,$isParameter=false) {
		$menu = $menu == false ? $this->menu : $menu;
		$page = $page == false ? $this->page : $page;
		$view = $view == false ? $this->view : $view;
		
		$baseURL = '';
		if ($menu == '') return '/';
		else $baseURL.= '/'.$menu;
		
		if ($page == '') return $baseURL;
		else $baseURL.= '/'.$page;
		
		if ($view == '') return $baseURL;
		else $baseURL.= '/'.$view;
		
		if ($isParameter == true) {
			$baseURL.= '';
		}
		
		return $baseURL;
	}
}
?>