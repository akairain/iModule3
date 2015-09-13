<?php
class ModuleApidocument {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	
	private $apidocuments = array();
	private $versions = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->apidocument = 'apidocument_table';
		$this->table->post = 'apidocument_post_table';
		$this->table->post_version = 'apidocument_post_version_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/apidocument.js');
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
	
	function getCountInfo($aid,$config) {
		$versions = $this->getVersions($aid);
		
		if (count($versions) > 0) {
			$info = new stdClass();
			$info->text = $versions[0];
			$info->last_time = 0;
			
			return $info;
		} else {
			return null;
		}
	}
	
	function getContext($aid,$config=null) {
		$context = '';
		$values = new stdClass();

		$view = $this->IM->view == '' ? 'list' : $this->IM->view;
		
		$apidocument = $this->getApidocument($aid);
		if ($apidocument == null) return $this->getError($this->getLanguage('error/unregistered'));
		
		$templetPath = $apidocument->templetPath;
		$templetDir = $apidocument->templetDir;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		$context.= $this->getHeader($aid,$config);
		
		switch ($view) {
			case 'list' :
				$context.= $this->getListContext($aid,$config);
				break;
				
			case 'view' :
				$context.= $this->getListContext($aid,$config,true);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($aid,$config);
				break;
		}
		
		$context.= $this->getFooter($aid,$config);
		
		$this->IM->fireEvent('afterGetContext','apidocument',$view,null,null,$context);

		return $context;
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getApiDocument($aid) {
		if (isset($this->apidocuments[$aid]) == true) return $this->apidocuments[$aid];
		$apidocument = $this->db()->select($this->table->apidocument)->where('aid',$aid)->getOne();
		if ($apidocument == null) {
			$this->apidocuments[$aid] = null;
		} else {
			$apidocument->templetPath = $this->Module->getPath().'/templets/'.$apidocument->templet;
			$apidocument->templetDir = $this->Module->getDir().'/templets/'.$apidocument->templet;
			
			$this->apidocuments[$aid] = $apidocument;
		}
		
		return $this->apidocuments[$aid];
	}
	
	function getHeader($aid,$config) {
		ob_start();
		
		$apidocument = $this->getApidocument($aid);
		$templetPath = $apidocument->templetPath;
		$templetDir = $apidocument->templetDir;
		
		$versions = $this->getVersions($aid);
		$version = Request('p') ? Request('p') : (count($versions) > 0 ? $versions[0] : '1.0.0');
		
		if (file_exists($templetPath.'/header.php') == true) {
			INCLUDE $templetPath.'/header.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getFooter($aid,$config) {
		ob_start();
		
		$apidocument = $this->getApidocument($aid);
		$templetPath = $apidocument->templetPath;
		$templetDir = $apidocument->templetDir;
		
		if (file_exists($templetPath.'/footer.php') == true) {
			INCLUDE $templetPath.'/footer.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getError($content,$title='') {
		return $content;
	}
	
	function getPropertyParse($property,$type) {
		if ($type == 'CONFIG' || $type == 'PROPERTY' || $type == 'GLOBAL') {
			$temp = explode(':',$property);
			return '<span class="name">'.$temp[0].'</span> <span class="type">: '.$temp[1].'</span>';
		} elseif ($type == 'METHOD') {
			if (preg_match('/^(.*?)\((.*?)\):(.*?)$/',$property,$match) == true) {
				return '<span class="name">'.$match[1].'</span><span class="parameter">('.implode(', ',explode(',',$match[2])).')</span> <span class="type">: '.$match[3].'</span>';
			}
		} elseif ($type == 'EVENT') {
			if (preg_match('/^(.*?):\((.*?)\)$/',$property,$match) == true) {
				return '<span class="name">'.$match[1].'</span><span class="parameter">('.implode(', ',explode(',',$match[2])).')</span>';
			}
		} elseif ($type == 'ERROR') {
			if (preg_match('/^(.*?):(.*?)$/',$property,$match) == true) {
				return '<span class="name">'.$match[1].'</span><span class="parameter"> : </span><span class="name">'.$match[2].'</span>';
			}
		}
	}
	
	function getVersions($aid) {
		if (isset($this->versions[$aid]) == true) return $this->versions[$aid];
		
		$versions = $this->db()->select($this->table->post_version)->where('aid',$aid)->groupBy('version')->get();
		for ($i=0, $loop=count($versions);$i<$loop;$i++) {
			$versions[$i] = $versions[$i]->version;
		}
		
		for ($i=0;$i<$loop;$i++) {
			for ($j=0;$j<$loop-1;$j++) {
				if (version_compare($versions[$j],$versions[$j+1],'<') == true) {
					$temp = $versions[$j];
					$versions[$j] = $versions[$j+1];
					$versions[$j+1] = $temp;
				}
			}
		}
		
		$this->versions[$aid] = $versions;
		
		return $this->versions[$aid];
	}
	
	function getListContext($aid,$config,$isView=false) {
		ob_start();
		
		$this->IM->setView('list');
		
		$apidocument = $this->getApidocument($aid);
		$templetPath = $apidocument->templetPath;
		$templetDir = $apidocument->templetDir;
		
		$versions = $this->getVersions($aid);
		if ($isView == true) {
			$this->IM->removeTemplet();
			$version = Request('idx') ? Request('idx') : (count($versions) > 0 ? $versions[0] : '1.0.0');
			
			echo PHP_EOL.'<div style="padding:10px;">'.PHP_EOL;
		} else {
			$version = Request('p') ? Request('p') : (count($versions) > 0 ? $versions[0] : '1.0.0');
		}
		
		$configs = $properties = $globals = $methods = $events = $errors = array();
		$lists = $this->db()->select($this->table->post)->where('aid',$aid)->orderBy('name','asc')->get();
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			if (version_compare($lists[$i]->defined,$version,'<=') && ($lists[$i]->deprecated == '' || version_compare($lists[$i]->deprecated,$version,'>='))) {
				$versions = $this->db()->select($this->table->post_version)->where('parent',$lists[$i]->idx)->get();
				$selectVersion = null;
				$isChanged = false;
				for ($j=0, $loopj=count($versions);$j<$loopj;$j++) {
					if (version_compare($versions[$j]->version,$version,'<=') == true) {
						if ($selectVersion == null || version_compare($selectVersion->version,$versions[$j]->version,'<') == true) {
							if ($selectVersion != null) $isChanged = true;
							$selectVersion = $versions[$j];
						}
					}
				}
				
				$lists[$i]->is_required = $lists[$i]->is_required == 'TRUE';
				
				if ($selectVersion != null) {
					$lists[$i]->is_changed = $isChanged;
					$lists[$i]->property = $this->getPropertyParse($selectVersion->property,$lists[$i]->type);
					$lists[$i]->description = $selectVersion->description;
					$lists[$i]->content = $selectVersion->content;
					$lists[$i]->version = $selectVersion->version;
					$lists[$i]->stability = $selectVersion->stability;
					$lists[$i]->is_new = $version == $lists[$i]->version && count($versions) == 1;
				}
				
				if ($lists[$i]->type == 'CONFIG') $configs[] = $lists[$i];
				if ($lists[$i]->type == 'PROPERTY') $properties[] = $lists[$i];
				if ($lists[$i]->type == 'GLOBAL') $globals[] = $lists[$i];
				if ($lists[$i]->type == 'METHOD') $methods[] = $lists[$i];
				if ($lists[$i]->type == 'EVENT') $events[] = $lists[$i];
				if ($lists[$i]->type == 'ERROR') $errors[] = $lists[$i];
			}
		}
		
		$values = new stdClass();
		$values->versions = $versions = $this->getVersions($aid);
		$values->configs = $configs;
		$values->methods = $methods;
		$this->IM->fireEvent('afterInitContext','apidocument',__FUNCTION__,$values);

		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/list.php') == true) {
			INCLUDE $templetPath.'/list.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		if ($isView == true) {
			echo PHP_EOL.'</div>'.PHP_EOL;
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWriteContext($aid,$config) {
		ob_start();
		
		$this->IM->setView('write');
		
		$apidocument = $this->getApidocument($aid);
		$templetPath = $apidocument->templetPath;
		$templetDir = $apidocument->templetDir;
		
		if ($this->checkPermission('write') == false) return $this->getError('FORBIDDEN');
		
		$idx = Request('idx');
		if ($idx !== null) {
			$post = $this->db()->select($this->table->post)->where('idx',$idx)->getOne();
			$version = Request('version');
			
			$selectVersion = null;
			$versions = $this->db()->select($this->table->post_version)->where('parent',$idx)->get();
			for ($i=0, $loop=count($versions);$i<$loop;$i++) {
				if ($selectVersion == null || version_compare($selectVersion->version,$versions[$i]->version,'<') == true) {
					$selectVersion = $versions[$i];
				}
			}
			
			if ($selectVersion != null) {
				$post->property = $selectVersion->property;
				$post->version = $selectVersion->version;
				$post->description = $selectVersion->description;
				$post->stability = $selectVersion->stability;
				$post->content = $selectVersion->content;
			}
		} else {
			$post = null;
		}
		
		$formName = 'ModuleApidocumentWriteForm-'.rand(10000,99999);
		
		echo '<form name="'.$formName.'" method="post"  onsubmit="return Apidocument.post.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="aid" value="'.$aid.'">'.PHP_EOL;
		echo '<input type="hidden" name="menu" value="'.$this->IM->menu.'">'.PHP_EOL;
		echo '<input type="hidden" name="page" value="'.($this->IM->page != null ? $this->IM->page : '').'">'.PHP_EOL;
		if ($post !== null) echo '<input type="hidden" name="idx" value="'.$post->idx.'">'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/write.php') == true) {
			INCLUDE $templetPath.'/write.php';
		}
		
		echo '</form>'.PHP_EOL.'<script>$(document).ready(function() { Apidocument.post.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getWysiwyg($name) {
		$wysiwyg = $this->IM->getModule('wysiwyg')->setUploader(false)->setName($name)->setModule('board');
		
		return $wysiwyg;
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'postWrite') {
			$values->errors = array();
			
			$values->idx = Request('idx');
			$values->aid = Request('aid');
			$values->menu = Request('menu');
			$values->page = Request('page');
			$values->type = Request('type') ? Request('type') : $values->errors['type'] = $this->getLanguage('write/help/type/error');
			$values->name = Request('name') ? Request('name') : $values->errors['name'] = $this->getLanguage('write/help/name/error');
			$values->is_required = Request('is_required') ? 'TRUE' : 'FALSE';
			$values->property = Request('property') ? Request('property') : $values->errors['property'] = $this->getLanguage('write/help/property/error');
			$values->version = Request('version') ? Request('version') : $values->errors['version'] = $this->getLanguage('write/help/version/error');
			$values->description = Request('description') ? Request('description') : $values->errors['description'] = $this->getLanguage('write/help/description/error');
			$values->stability = Request('stability') ? Request('stability') : $values->errors['stability'] = $this->getLanguage('write/help/stability/error');
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('write/help/content/error');
			
			// 권한체크하자!
			if (empty($values->errors) == true) {
				$results->success = true;
				
				if ($values->idx == null) {
					$insert = array();
					$insert['aid'] = $values->aid;
					$insert['type'] = $values->type;
					$insert['name'] = $values->name;
					$insert['is_required'] = $values->is_required;
					$insert['defined'] = $values->version;
				
					if ($this->db()->select($this->table->post)->where('aid',$values->aid)->where('type',$values->type)->where('name',$values->name)->count() > 0) {
						$results->success = false;
						$results->message = $this->getLanguage('write/help/name/duplicated');
						$values->errors['name'] = $this->getLanguage('write/help/name/duplicated');
					} else {
						$values->parent = $this->db()->insert($this->table->post,$insert)->execute();
					}
				} else {
					$values->parent = $values->idx;
				}
				
				if ($results->success == true) {
					$post = $this->db()->select($this->table->post)->where('idx',$values->parent)->getOne();
					
					if ($post == null) {
						
					} else {
						$insert = array();
						$insert['aid'] = $values->aid;
						$insert['parent'] = $values->parent;
						$insert['property'] = $values->property;
						$insert['description'] = $values->description;
						$insert['content'] = $values->content;
						$insert['stability'] = $values->stability;
						$insert['version'] = $values->version;
						
						if ($this->db()->select($this->table->post_version)->where('parent',$values->parent)->where('version',$values->version)->count() == 0) {
							$this->db()->insert($this->table->post_version,$insert)->execute();
						} else {
							$this->db()->update($this->table->post_version,$insert)->where('parent',$values->parent)->where('version',$values->version)->execute();
						}
						
						if (version_compare($post->defined,$values->version,'>') == true) {
							$this->db()->update($this->table->post,array('defined'=>$values->version))->where('idx',$values->parent)->execute();
						}
						
						if ($values->stability == 'DEPRECATED') {
							$this->db()->update($this->table->post,array('deprecated'=>$values->version))->where('idx',$values->parent)->execute();
						}
						
						$results->redirect = $this->IM->getUrl($values->menu,$values->page,'list',false);
					}
				} else {
					$results->errors = $values->errors;
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
				$results->modalHtml = $this->getPostDelete($values->idx);
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/forbidden');
			}
		}
		
		$this->IM->fireEvent('afterDoProcess','apidocument',$action,$values,$results);
		
		return $results;
	}
	
	function checkPermission($type) {
		if ($this->IM->getModule('member')->isLogged() == true && $this->IM->getModule('member')->getMember()->type == 'ADMINISTRATOR') return true;
		return false;
	}
}
?>