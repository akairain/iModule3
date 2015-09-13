<?php
class ModuleMember {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	
	private $logged = null;
	private $members = array();
	private $memberPages = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->member = 'member_table';
		$this->table->email = 'member_email_table';
		$this->table->level = 'member_level_table';
		$this->table->signup = 'member_signup_table';
		$this->table->point = 'member_point_table';
		$this->table->social = 'member_social_table';
		$this->table->token = 'member_token_table';
		$this->table->activity = 'member_activity_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/member.js');
		
		$this->logged = Request('MEMBER_LOGGED','session') != null && Decoder(Request('MEMBER_LOGGED','session')) != false ? json_decode(Decoder(Request('MEMBER_LOGGED','session'))) : false;
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
	}
	
	function getApi($api) {
		$data = new stdClass();
		
		if ($api == 'signup') {
			$errors = array();
			$gidx = Request('gidx') ? Request('gidx') : 1;
			$email = CheckEmail(Request('email')) == true ? Request('email') : $errors['email'] = $this->getLanguage('signup/help/email/error');
			$password = strlen(Request('password')) >= 4 ? Request('password') : $errors['password'] = $this->getLanguage('signup/help/password/error');
			$name = CheckNickname(Request('name')) == true ? Request('name') : $errors['name'] = $this->getLanguage('signup/help/name/error');
			$nickname = CheckNickname(Request('nickname')) == true ? Request('nickname') : $errors['nickname'] = $this->getLanguage('signup/help/nickname/error');
			$status = Request('status') ? Request('status') : 'ACTIVE';
			$cellphone = str_replace('-','',Request('cellphone'));
			$gender = in_array(Request('gender'),array('MALE','FEMALE')) == true ? Request('gender') : '';
			$birthday = Request('birthday') ? strtotime(Request('birthday')) : 0;
			$birthday = $birthday == 0 ? '' : date('m-d-Y',$birthday);
			$client_id = Request('client_id');
			
			if ($this->db()->select($this->table->member)->where('email',$email)->has() == true) {
				$errors['email'] = $this->getLanguage('signup/help/email/duplicated');
			}
			
			if ($this->db()->select($this->table->member)->where('nickname',$nickname)->has() == true) {
				$errors['nickname'] = $this->getLanguage('signup/help/nickname/duplicated');
			}
			
			if (empty($errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['gidx'] = $gidx;
				$insert['email'] = $email;
				$insert['password'] = $mHash->password_hash($password);
				$insert['name'] = $name;
				$insert['nickname'] = $nickname;
				$insert['cellphone'] = $cellphone;
				$insert['gender'] = $gender;
				$insert['birthday'] = $birthday;
				$insert['status'] = $status;
				
				$idx = $this->db()->insert($this->table->member,$insert)->execute();
				$data->success = true;
				$data->access_token = $this->makeAuthToken($client_id,$idx);
			} else {
				$data->success = false;
				$data->errors = $errors;
			}
		}
		
		if ($api == 'login') {
			$email = Request('email');
			$password = Request('password');
			$client_id = Request('client_id');
			
			$loginIdx = $this->isValidate($email,$password);
			if ($loginIdx === false) {
				$data->success = false;
			} else {
				$data->success = true;
				$data->access_token = $this->makeAuthToken($client_id,$loginIdx);
			}
		}
		
		if ($api == 'me') {
			if ($this->isLogged() == false) {
				$data->success = false;
				$data->message = 'NOT LOGGED';
			} else {
				$data->success = true;
				$data->me = $this->getMember();
				$data->me->photo = $this->IM->getHost(true).$data->me->photo;
			}
		}
		
		return $data;
	}
	
	function makeAuthToken($client_id,$midx) {
		return Encoder(json_encode(array($client_id,$midx)));
	}
	
	function getTable($table) {
		return empty($this->table->$table) == true ? null : $this->table->$table;
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
		if (sizeof($temp) == 1) {
			return isset($this->lang->$code) == true ? $this->lang->$code : '';
		} else {
			$string = $this->lang;
			for ($i=0, $loop=sizeof($temp);$i<$loop;$i++) {
				if (isset($string->$temp[$i]) == true) $string = $string->$temp[$i];
				else return '';
			}
			return $string;
		}
	}
	
	private function login($idx) {
		$_SESSION['MEMBER_LOGGED'] = Encoder(json_encode(array('idx'=>$idx,'time'=>time(),'ip'=>$_SERVER['REMOTE_ADDR'])));
		$this->logged = Request('MEMBER_LOGGED','session') != null && Decoder(Request('MEMBER_LOGGED','session')) != false ? json_decode(Decoder(Request('MEMBER_LOGGED','session'))) : false;
	}
	
	function loginByToken($token) {
		$token = explode(' ',$token);
		if (count($token) != 2 || strtoupper($token[0]) != 'BEARER' || Decoder($token[1]) === false) {
			header("HTTP/1.1 401 Unauthorized");
			header("Content-type: text/json; charset=utf-8",true);
			
			$results = new stdClass();
			$results->success = false;
			$results->message = 'Access token Error : Unauthorized';
	
			exit(json_encode($results,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
		} else {
			$data = json_decode(Decoder($token[1]));
			$midx = array_pop($data);
			$client_id = array_pop($data);
			
			$this->login($midx);
		}
		
		return true;
	}
	
	function isLogged() {
		if ($this->logged === false) return false;
		else return true;
	}
	
	function isValidate($email,$password) {
		$check = $this->db()->select($this->table->member)->where('email',$email)->getOne();
		if ($check == null) return false;
		$mHash = new Hash();
		return $mHash->password_validate($password,$check->password) == true ? $check->idx : false;
	}
	
	function getLogged() {
		return $this->logged == null ? 0 : $this->logged->idx;
	}
	
	function getLevel($exp) {
		$level = $this->db()->select($this->table->level)->where('exp',$exp,'<=')->orderBy('level','desc')->getOne();
		$level->level = $level->next == 0 ? $level->level : $level->level + 1;
		$level->next = $level->next == 0 ? $exp - $level->exp : $level->next - $level->exp;
		$level->exp = $exp - $level->exp;
		
		return $level;
	}
	
	function getMember($midx=null,$forceReload=false) {
		if ($midx === null && $this->isLogged() == false) return null;
		
		$midx = $midx === null ? $this->logged->idx : $midx;
		if ($forceReload == true || isset($this->members[$midx]) == false) {
			$member = $this->db()->select($this->table->member)->where('idx',$midx)->getOne();
			
			if ($member == null) {
				$member = new stdClass();
				$member->idx = null;
				$member->name = $member->nickname = 'Unknown';
				$member->photo = $this->Module->getDir().'/images/nophoto.png';
				$member->nickcon = null;
				$member->level = $this->getLevel(0);
			} else {
				$member->photo = file_exists($this->IM->getAttachmentPath().'/member/'.$midx.'.jpg') == true ? $this->IM->getAttachmentDir().'/member/'.$midx.'.jpg' : $this->Module->getDir().'/images/nophoto.png';
				$member->nickcon = file_exists($this->IM->getAttachmentPath().'/member/'.$midx.'.gif') == true ? $this->IM->getAttachmentDir().'/member/'.$midx.'.gif' : null;
				$member->level = $this->getLevel($member->exp);
				$temp = explode('-',$member->birthday);
				$member->birthday = count($temp) == 3 ? $temp[2].'-'.$temp[0].'-'.$temp[1] : '';
			}
			
			$this->members[$midx] = $member;
		}
		
		return $this->members[$midx];
	}
	
	function getMemberName($midx=null,$replaceName='') {
		if ($midx == null && $this->isLogged() == false) return $replaceName;

		$member = $this->getMember($midx);
		if ($member->idx == null && empty($replaceName) == false) return $replaceName;
		
		return $member->name;
	}
	
	function getMemberNickname($midx=null,$nickcon=true,$replaceName='') {
		if ($midx === null && $this->isLogged() == false) return $replaceName;

		$member = $this->getMember($midx);
		if ($member->idx == null && empty($replaceName) == false) return $replaceName;
		
		$nickname = '<span data-member-idx="'.$member->idx.'" class="ModuleMemberInfoNickname">';
		if ($nickcon == true && $member->nickcon != null) {
			$nickname.= '<img src="'.$member->nickcon.'" alt="'.$member->nickname.'" title="'.$member->nickname.'">';
		} else {
			$nickname.= $member->nickname;
		}
		$nickname.= '</span>';
		return $nickname;
	}
	
	function getMemberPhoto($midx=null,$width=null,$height=null) {
		if ($midx === null && $this->isLogged() == false) return '<img src="'.$this->Module->getDir().'/images/nophoto.png" alt="unknown">';
		
		$member = $this->getMember($midx);
		
		$photo = '<img data-member-idx="'.$member->idx.'" src="'.$member->photo.'" class="ModuleMemberInfoPhoto" alt="'.$member->nickname.'" style="';
		if ($width !== null) $photo.= 'width:'.$width.';';
		if ($height !== null) $photo.= 'height:'.$height.';';
		$photo.= '">';
		
		return $photo;
	}
	
	function getMemberPage($view) {
		if (isset($this->memberPages[$view]) == true) return $this->memberPages[$view];
		
		$this->memberPages[$view] = null;
		$sitemap = $this->IM->getPages();
		foreach ($sitemap as $menu=>$pages) {
			for ($i=0, $loop=count($pages);$i<$loop;$i++) {
				if ($pages[$i]->type == 'module') {
					if ($pages[$i]->context != null && $pages[$i]->context->module == 'member' && $pages[$i]->context->context == $view) {
						$this->memberPages[$view] = $pages[$i];
						break;
					}
				}
			}
		}
		
		if ($this->memberPages[$view] == null) return $this->getAccountPage($view == 'mypage' ? null : $view);
		return $this->memberPages[$view];
	}
	
	function getForceLoginUrl($idx,$redirectUrl='') {
		$code = Encoder(json_encode(array('idx'=>$idx,'ip'=>$_SERVER['REMOTE_ADDR'])));
		return 'Member.forceLogin(\''.$code.'\',\''.$redirectUrl.'\');';
	}
	
	function getAccountPage($view=null) {
		$page = new stdClass();
		$page->domain = $this->IM->domain;
		$page->language = $this->IM->language;
		$page->menu = 'account';
		$page->page = $view == null ? 'dashboard' : $view;
		$page->title = $this->getLanguage('account/title');
		$page->type = 'module';
		$page->layout = 'empty';
		$page->context = new stdClass();
		$page->context->module = 'member';
		$page->context->context = 'account';
		$page->context->config = new stdClass();
		$page->description = null;
		$page->image = null;
		
		return $page;
	}
	
	function getContext($type,$config=null) {
		$context = '';
		$values = new stdClass();
		
		switch ($type) {
			case 'account' :
				$context = $this->getAccountContext($config);
				break;
				
			case 'signup' :
				$context = $this->getSignUpContext($config);
				break;
				
			case 'modify' :
				$context = $this->getModifyContext($config);
				break;
				
			case 'social' :
				$context = $this->getSocial($config);
				break;
		}
		
		return $context;
	}
	
	function getError($content,$title='') {
		return $content;
	}
	
	function sendVerifyEmail($midx,$email=null) {
		$member = $this->db()->select($this->table->member)->where('idx',$midx)->getOne();
		if ($member == null) return null;
		
		$email = $email == null ? $member->email : $email;
		$check = $this->db()->select($this->table->email)->where('midx',$midx)->where('email',$email)->getOne();
		
		$code = GetRandomString(6);
		$isSendEmail = false;
		if ($check == null) {
			$this->db()->insert($this->table->email,array('midx'=>$midx,'email'=>$email,'code'=>$code,'reg_date'=>time(),'status'=>'SENDING'))->execute();
			$isSendEmail = true;
		} elseif ($check->status == 'CANCELED') {
			$this->db()->update($this->table->email,array('code'=>$code,'reg_date'=>time(),'status'=>'SENDING'))->where('midx',$midx)->where('email',$email)->execute();
			$isSendEmail = true;
		} elseif ($check->status == 'VERIFIED') {
			return 'VERIFIED';
		}
		
		if ($isSendEmail == true) {
			$subject = '[알쯔닷컴] 이메일주소 확인메일';
			$content = '회원님이 입력하신 이메일주소가 유효한 이메일주소인지 확인하기 위한 이메일입니다.<br>알쯔닷컴을 이용하지 않거나, 최근에 이메일주소변경신청을 하신적이 없다면 본 메일은 무시하셔도 됩니다.';
			if ($member->status == 'VERIFYING') {
				$content.= '<br><br>아래의 인증코드 6자리를 인증번호 확인란에 입력하시거나, 인증링크를 클릭하여 회원가입을 완료할 수 있습니다.';
			} else {
				$content.= '<br><br>아래의 인증코드 6자리를 인증번호 확인란에 입력하여 이메일주소변경을 완료할 수 있습니다.';
			}
			$content.= '<br><br>인증코드 : <b>'.$code.'</b>';
			if ($member->status == 'VERIFYING') {
				$signupPage = $this->getMemberPage('signup');
				$link = $this->IM->getUrl($signupPage->menu,$signupPage->page,'verify',false,true).'?code='.urlencode(Encoder(json_encode(array('midx'=>$midx,'email'=>$email,'code'=>$code))));
				$content.= '<br>인증주소 : <a href="'.$link.'" target="_blank">'.$link.'</a>';
			}
			$content.= '<br><br>본 메일은 발신전용메일로 회신되지 않습니다.<br>감사합니다.';
			
			$this->IM->getModule('email')->addTo($email,$member->name)->setSubject($subject)->setContent($content)->send();
		}
		
		if ($member->status == 'VERIFYING' && $member->email != $email) {
			$this->db()->update($this->table->member,array('email'=>$email))->where('idx',$member->idx)->execute();
		}
		
		return 'SENDING';
	}
	
	function getAccountContext($config) {
		ob_start();
		if ($this->Module->getConfig('accountType') == 'standalone') $this->IM->removeTemplet();
		
		$templetPath = $this->Module->getPath().'/templets/account/'.$this->Module->getConfig('accountTemplet');
		$templetDir = $this->Module->getDir().'/templets/account/'.$this->Module->getConfig('accountTemplet');
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$values = new stdClass();
		$values->title = $this->getLanguage('account/title');
		
		$values->pages = array();
		if ($this->isLogged() == true) {
			if ($this->IM->page == null) $this->IM->page = 'dashboard';
			
			$values->pages[] = array(
				'title'=>'대시보드',
				'page'=>'dashboard'
			);
			$values->pages[] = array(
				'title'=>'개인정보관리',
				'pages'=>array(
					'modify'=>'개인정보수정',
					'password'=>'패스워드변경',
					'leave'=>'회원탈퇴'
				)
			);
		} else {
			if ($this->IM->page == null) $this->IM->page = 'signup';
			
			$values->pages[] = array(
				'title'=>'계정관리',
				'pages'=>array(
					'signup'=>'회원가입',
					'help'=>'패스워드 재발급'
				)
			);
		}
		
		$values->groupTitle = '';
		$values->pageTitle = '';
		for ($i=0, $loop=count($values->pages);$i<$loop;$i++) {
			if (isset($values->pages[$i]['page']) == true && $this->IM->page == $values->pages[$i]['page']) {
				$values->pageTitle = $values->pages[$i]['title'];
				$values->groupTitle = '';
				break;
			} elseif (isset($values->pages[$i]['pages']) == true) {
				$values->groupTitle = $values->pages[$i]['title'];
				foreach ($values->pages[$i]['pages'] as $page=>$title) {
					if ($this->IM->page == $page) {
						$values->pageTitle = $title;
						break;
					}
				}
			}
		}
		
		$pageContext = $this->getAccountViewContext();
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($templetPath.'/index.php') == true) {
			INCLUDE $templetPath.'/index.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getAccountViewContext() {
		$templetPath = $this->Module->getPath().'/templets/account/'.$this->Module->getConfig('accountTemplet');
		$templetDir = $this->Module->getDir().'/templets/account/'.$this->Module->getConfig('accountTemplet');
		
		$context = '';
		if (in_array($this->IM->page,array('signup','modify','password','leave','config','social')) == true) {
			$config = new stdClass();
			$config->templet = $templetPath.'/'.$this->IM->page.'.php';
			if ($this->IM->page == 'signup') $context = $this->getSignUpContext($config);
			if ($this->IM->page == 'modify') $context = $this->getModifyContext($config);
			if ($this->IM->page == 'password') $context = $this->getPasswordContext($config);
		} else {
			ob_start();
		
			$IM = $this->IM;
			$Module = $this;
		
			if ($this->IM->view == null) {
				if (file_exists($templetPath.'/'.$this->IM->page.'.php') == true) {
					INCLUDE $templetPath.'/'.$this->IM->page.'.php';
				}
			} else {
				if (file_exists($templetPath.'/'.$this->IM->page.'.'.$this->IM->view.'.php') == true) {
					INCLUDE $templetPath.'/'.$this->IM->page.'.'.$this->IM->view.'.php';
				}
			}
			
			$context = ob_get_contents();
			ob_end_clean();
		}
		
		return $context;
	}
	
	function getSignUpContext($config) {
		ob_start();
		$config->gidx = isset($config->gidx) == true ? $config->gidx : 'default';
		$form = $this->db()->select($this->table->signup,'value')->where('gidx',$config->gidx)->orderBy('sort','asc')->get();
		
		$_SESSION['registerGIDX'] = $config->gidx;
		if ($this->Module->getConfig('signupCert') === true) {
			$step = array('agreement','cert','insert','verify','complete');
		} else {
			$step = array('agreement','insert','verify','complete');
		}
		$view = $this->IM->view == '' ? 'agreement' : $this->IM->view;
		
		$currentStep = array_search($view,$step);
		$nextStep = $currentStep !== null && isset($step[$currentStep+1]) == true ? $step[$currentStep+1] : '';
		
		if ($view == 'verify') {
			if (Request('code') != null) {
				$code = Decoder(Request('code'));
				if ($code === false) header("location: ".$this->IM->getUrl($this->IM->menu,$this->IM->page,'verify',false));
				else $code = json_decode($code);
				
				$check = $this->db()->select($this->table->email)->where('midx',$code->midx)->where('email',$code->email)->getOne();
				if ($check != null && $check->code == $code->code) {
					$this->db()->update($this->table->email,array('status'=>'VERIFIED'))->where('midx',$code->midx)->where('email',$code->email)->execute();
					$this->db()->update($this->table->member,array('status'=>'ACTIVE'))->where('idx',$code->midx)->execute();
					header("location: ".$this->IM->getUrl($this->IM->menu,$this->IM->page,'complete',false));
				} else {
					header("location: ".$this->IM->getUrl($this->IM->menu,$this->IM->page,'verify',false));
				}
			}
			
			$registerIDX = Request('MEMBER_REGISTER_IDX','session') != null ? Decoder(Request('MEMBER_REGISTER_IDX','session')) : false;
			if ($registerIDX == false) return $this->getError('ERROR!');
			$registerInfo = $this->db()->select($this->table->member)->where('idx',$registerIDX)->getOne();
			if ($registerInfo == null) return $this->getError('ERROR!');
			
			$status = $this->sendVerifyEmail($registerIDX);
			if ($registerInfo->status != 'VERIFYING') {
				header("location: ".$this->IM->getUrl($this->IM->menu,$this->IM->page,'complete',false));
				exit;
			} elseif ($status == 'VERIFIED') {
				$this->db()->update($this->table->member,array('status'=>'ACTIVE'))->where('idx',$registerIDX)->execute();
				header("location: ".$this->IM->getUrl($this->IM->menu,$this->IM->page,'complete',false));
				exit;
			}
		}

		$formName = 'ModuleMemberSignUpForm';
		echo '<form name="'.$formName.'" method="post" onsubmit="return Member.signup.submit(this);">'.PHP_EOL;
		foreach ($_POST as $key=>$value) {
			if ($key == 'step' || $key == 'next') continue;
			echo '<input type="'.$key.'" value="'.$value.'">'.PHP_EOL;
		}
		echo '<input type="hidden" name="step" value="'.$view.'">'.PHP_EOL;
		echo '<input type="hidden" name="next" value="'.($nextStep != '' ? $this->IM->getUrl(null,null,$nextStep) : '').'">'.PHP_EOL;
		if ($view == 'verify') echo '<input type="hidden" name="registerIDX" value="'.$registerIDX.'">'.PHP_EOL;
		
		if (preg_match('/\.php$/',$config->templet) == true) {
			$temp = explode('/',$config->templet);
			$templetFile = array_pop($temp);
			$templetPath = implode('/',$temp);
			$templetDir = str_replace(__IM_PATH__,__IM_DIR__,$templetPath);
		} else {
			if (preg_match('/^@/',$config->templet) == true) {
				$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/signup/'.preg_replace('/^@/','',$config->templet);
				$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/signup/'.preg_replace('/^@/','',$config->templet);
			} else {
				$templetPath = $this->Module->getPath().'/templets/signup/'.$config->templet;
				$templetDir = $this->Module->getDir().'/templets/signup/'.$config->templet;
			}
			
			if (file_exists($templetPath.'/styles/style.css') == true) {
				$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
			}
			
			$templetFile = 'templet.php';
		}
		
		$IM = $this->IM;
		$Module = $this;
		$Module->templetPath = $templetPath;
		$Module->templetDir = $templetDir;
		
		if (file_exists($templetPath.'/'.$templetFile) == true) {
			INCLUDE $templetPath.'/'.$templetFile;
		}
		
		echo '</form>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Member.signup.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getModifyContext($config) {
		ob_start();
		
		if ($this->isLogged() == false) {
			return $this->getError($this->getLanguage('error/notLogged'));
		}
		
		$member = $this->getMember();
		if (strlen($this->getMember()->password) == 0) {
			if (preg_match('/\.php$/',$config->templet) == true) {
				$temp = explode('/',$config->templet);
				$templetFile = array_pop($temp);
				$templetPath = implode('/',$temp);
				$templetDir = str_replace(__IM_PATH__,__IM_DIR__,$templetPath);
				
				$config->templet = $templetPath.'/password.php';
				return $this->getPasswordContext($config,true);
			} else {
				return $this->getPasswordContext($config,true);
			}
			
		} elseif (Request('MEMBER_MODIFY_PASSWORD','session') !== true && Request('password') == null) {
			$step = 'verify';
		} else {
			$this->IM->addSiteHeader('script',__IM_DIR__.'/scripts/jquery.cropit.min.js');
			
			$mHash = new Hash();
			$password = Decoder(Request('password'));
			
			if (Request('MEMBER_MODIFY_PASSWORD','session') !== true && ($password == false || $mHash->password_validate($password,$member->password) == false)) {
				return $this->getError($this->getLanguage('verify/help/password/error'));
			}
			
			$step = 'modify';
			
			$form = $this->db()->select($this->table->signup,'value')->where('gidx',$member->gidx)->orderBy('sort','asc')->get();
		}
		
		unset($_SESSION['MEMBER_MODIFY_PASSWORD']);
		
		$formName = 'ModuleMemberModifyForm';
		echo '<form name="'.$formName.'" method="post" onsubmit="return Member.modify.submit(this);">'.PHP_EOL;
		echo '<input type="hidden" name="templet" value="'.$config->templet.'">'.PHP_EOL;
		echo '<input type="hidden" name="step" value="'.$step.'">'.PHP_EOL;
		
		if (preg_match('/\.php$/',$config->templet) == true) {
			$temp = explode('/',$config->templet);
			$templetFile = array_pop($temp);
			$templetPath = implode('/',$temp);
			$templetDir = str_replace(__IM_PATH__,__IM_DIR__,$templetPath);
		} else {
			if (preg_match('/^@/',$config->templet) == true) {
				$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$config->templet);
				$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$config->templet);
			} else {
				$templetPath = $this->Module->getPath().'/templets/modify/'.$config->templet;
				$templetDir = $this->Module->getDir().'/templets/modify/'.$config->templet;
			}
		
			if (file_exists($templetPath.'/styles/style.css') == true) {
				$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
			}
			
			$templetFile = 'templet.php';
		}
		
		$IM = $this->IM;
		$Module = $this;
		$Module->templetPath = $templetPath;
		$Module->templetDir = $templetDir;
		
		if (file_exists($templetPath.'/'.$templetFile) == true) {
			INCLUDE $templetPath.'/'.$templetFile;
		}
		
		echo '</form>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Member.modify.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getPasswordContext($config,$isModify=false) {
		ob_start();
		
		if ($this->isLogged() == false) {
			return $this->getError($this->getLanguage('error/notLogged'));
		}
		
		$member = $this->getMember();
		
		if (strlen($this->getMember()->password) == 0) {
			$type = 'social';
		} else {
			$type = 'default';
		}
		
		unset($_SESSION['MEMBER_MODIFY_PASSWORD']);
		
		$formName = 'ModuleMemberPasswordForm';
		echo '<form name="'.$formName.'" method="post" onsubmit="return Member.password.submit(this,'.($isModify == true ? 'true' : 'false').');">'.PHP_EOL;
		
		if (preg_match('/\.php$/',$config->templet) == true) {
			$temp = explode('/',$config->templet);
			$templetFile = array_pop($temp);
			$templetPath = implode('/',$temp);
			$templetDir = str_replace(__IM_PATH__,__IM_DIR__,$templetPath);
		} else {
			if (preg_match('/^@/',$config->templet) == true) {
				$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/password/'.preg_replace('/^@/','',$config->templet);
				$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/password/'.preg_replace('/^@/','',$config->templet);
			} else {
				$templetPath = $this->Module->getPath().'/templets/password/'.$config->templet;
				$templetDir = $this->Module->getDir().'/templets/password/'.$config->templet;
			}
		
			if (file_exists($templetPath.'/styles/style.css') == true) {
				$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
			}
			
			$templetFile = 'templet.php';
		}
		
		$IM = $this->IM;
		$Module = $this;
		$Module->templetPath = $templetPath;
		$Module->templetDir = $templetDir;
		
		if (file_exists($templetPath.'/'.$templetFile) == true) {
			INCLUDE $templetPath.'/'.$templetFile;
		}
		
		echo '</form>'.PHP_EOL;
		echo '<script>$(document).ready(function() { Member.password.init("'.$formName.'"); });</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getSocial($config) {
		ob_start();
		
		$formName = 'ModuleMemberSocialForm';
		echo '<input type="hidden" name="type" value="'.$config->type.'">'.PHP_EOL;
		if ($config->type == 'duplicated') {
			echo '<form name="'.$formName.'" method="post" onsubmit="return Member.login(this);">'.PHP_EOL;
			echo '<input type="hidden" name="idx" value="'.$config->member->idx.'">'.PHP_EOL;
			
			$member = $config->member;
		} else {
			echo '<form>'.PHP_EOL;
			$photo = $config->photo;
			$redirectUrl = $config->redirectUrl;
			$accounts = array();
			for ($i=0, $loop=count($config->account);$i<$loop;$i++) {
				$accounts[$i] = $this->getMember($config->account[$i]->midx);
			}
		}
		
		if (file_exists($this->IM->getTempletPath().'/templets/modules/member/templets/social/'.$config->type) == true) {
			$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/social/'.$config->type;
			$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/social/'.$config->type;
		} else {
			$templetPath = $this->Module->getPath().'/templets/social/'.$config->type;
			$templetDir = $this->Module->getDir().'/templets/social/'.$config->type;
		}
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		$IM = $this->IM;
		$Module = $this;
		$Module->templetPath = $templetPath;
		$Module->templetDir = $templetDir;
		
		
		
		if (file_exists($templetPath.'/templet.php') == true) {
			INCLUDE $templetPath.'/templet.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getPhotoEdit($templet) {
		ob_start();
		
		if (preg_match('/^@/',$templet) == true) {
			$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$templet);
			$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$templet);
		} else {
			$templetPath = $this->Module->getPath().'/templets/modify/'.$templet;
			$templetDir = $this->Module->getDir().'/templets/modify/'.$templet;
		}
		
		$title = $this->getLanguage('photoEdit/title');
		echo '<form name="ModuleMemberPhotoEditForm" onsubmit="return Member.modify.photoSubmit(this);">'.PHP_EOL;

		$content = '<style>'.PHP_EOL;
		$content.= '.cropit-image-preview-container {width:250px; height:250px; margin:10px auto; margin-bottom:30px;}'.PHP_EOL;
		$content.= '.cropit-image-preview {background-color:#f8f8f8; background-size:cover; border:1px solid #ccc; border-radius:3px; width:250px; height:250px; cursor:move;}'.PHP_EOL;
		$content.= 'input.cropit-image-input {display:none;}'.PHP_EOL;
		$content.= '.cropit-image-background {opacity:0.3; cursor:auto;}'.PHP_EOL;
		$content.= '.cropit-image-zoom-container {height:20px; width:250px; margin:0 auto; font-size:0px; text-align:center;}'.PHP_EOL;
		$content.= '.cropit-image-zoom-container span {width:20px; height:17px; display:inline-block; vertical-align:middle; line-height:17px; margin-top:3px;}'.PHP_EOL;
		$content.= '.cropit-image-zoom-container span.cropit-image-zoom-out i {font-size:10px; margin-top:2px;}'.PHP_EOL;
		$content.= '.cropit-image-zoom-container span.cropit-image-zoom-in i {font-size:14px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input {-webkit-appearance:none; border:1px solid white; width:140px; position:relative; z-index:10; vertical-align:middle; margin:0px 10px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-webkit-slider-runnable-track {width:100%; height:5px; background:#ddd; border:none; border-radius:3px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-webkit-slider-thumb {-webkit-appearance:none; border:none; height:16px; width:16px; border-radius:50%; background:#e4232c; margin-top:-5px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input:focus {outline:none;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input:focus::-webkit-slider-runnable-track {background:#ccc;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-moz-range-track {width:100%; height:5px; background:#ddd; border:none; border-radius:3px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-moz-range-thumb {border:none; height:16px; width:16px; border-radius:50%; background:#e4232c;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input:-moz-focusring{outline:1px solid white; outline-offset:-1px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-ms-track {width:300px; height:5px; background:transparent; border-color:transparent; border-width:6px 0; color:transparent;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-ms-fill-lower {background:#777; border-radius:10px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-ms-fill-upper {background:#ddd; border-radius:10px;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input::-ms-thumb {border:none; height:16px; width:16px; border-radius:50%; background:#e4232c;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input:focus::-ms-fill-lower {background:#888;}'.PHP_EOL;
		$content.= 'input.cropit-image-zoom-input:focus::-ms-fill-upper {background:#ccc;}'.PHP_EOL;
		$content.= '</style>'.PHP_EOL;
		
		$content.= '<div class="photo-editor">'.PHP_EOL;
		$content.= '	<input type="file" class="cropit-image-input">'.PHP_EOL;
		$content.= '	<div class="cropit-image-preview-container">'.PHP_EOL;
		$content.= '		<div class="cropit-image-preview"></div>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		
		$content.= '	<div class="cropit-image-zoom-container">'.PHP_EOL;
		$content.= '		<span class="cropit-image-zoom-out"><i class="fa fa-picture-o"></i></span>'.PHP_EOL;
		$content.= '		<input type="range" class="cropit-image-zoom-input">'.PHP_EOL;
		$content.= '		<span class="cropit-image-zoom-in"><i class="fa fa-picture-o"></i></span>'.PHP_EOL;
		$content.= '	</div>';
		$content.= '</div>';
		
		$actionButton = '<button type="button" class="danger" onclick="$(\'input.cropit-image-input\').click();">사진선택</button>';
		
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
	
	function getModifyEmail($templet) {
		ob_start();
		
		if (preg_match('/^@/',$templet) == true) {
			$templetPath = $this->IM->getTempletPath().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$templet);
			$templetDir = $this->IM->getTempletDir().'/templets/modules/member/templets/modify/'.preg_replace('/^@/','',$templet);
		} else {
			$templetPath = $this->Module->getPath().'/templets/modify/'.$templet;
			$templetDir = $this->Module->getDir().'/templets/modify/'.$templet;
		}
		
		$title = $this->getLanguage('modifyEmail/title');
		echo '<form name="ModuleMemberModifyEmailForm" onsubmit="return Member.modify.modifyEmail(this);">'.PHP_EOL;
		echo '<input type="hidden" name="confirm" value="TRUE">'.PHP_EOL;
		
		$content = '<div class="message">'.$this->getLanguage('modifyEmail/email').'</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '	<input type="text" name="email" class="inputControl" required>'.PHP_EOL;
		$content.= '	<div class="helpBlock" data-default="'.$this->getLanguage('modifyEmail/help/email/default').'"></div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$content.= '<button type="button" class="btn btnRed" style="margin:5px 0px; width:100%;" data-loading="'.$this->getLanguage('modifyEmail/sending').'" onclick="Member.modify.sendVerifyEmail(this);"><i class="fa fa-check"></i> '.$this->getLanguage('modifyEmail/sendVerifyEmail').'</button>'.PHP_EOL;
		
		$content.= '<div class="message">'.$this->getLanguage('modifyEmail/code').'</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '	<input type="text" name="code" class="inputControl" required>'.PHP_EOL;
		$content.= '	<div class="helpBlock" data-default="'.$this->getLanguage('modifyEmail/help/code/default').'"></div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
//		$actionButton = '<button type="button" class="danger" onclick="$(\'input.cropit-image-input\').click();">사진선택</button>';
		
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
	
	function getSocialAuth($code,$midx=null) {
		$midx = $midx == null ? $this->getLogged() : $midx;
		if ($midx == null) return null;
		
		return $this->db()->select($this->table->social)->where('midx',$midx)->where('code',$code)->getOne();
	}
	
	function sendPoint($midx,$point,$module='',$code='',$content=array(),$isForce=false) {
		if ($point == 0) return false;
		
		$member = $this->getMember($midx);
		if ($member == null) return false;
		if ($isForce == false && $point < 0 && $member->point < $point * -1) return false;
		
		$this->db()->update($this->table->member,array('point'=>$member->point + $point))->where('idx',$member->idx)->execute();
		$this->db()->insert($this->table->point,array('midx'=>$member->idx,'point'=>$point,'module'=>$module,'code'=>$code,'content'=>json_encode($content,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK),'reg_date'=>time()))->execute();
		
		return true;
	}
	
	function addActivity($midx,$exp,$module,$code,$content=array()) {
		$member = $this->getMember($midx);
		if ($member == null) return;
		
		$this->db()->insert($this->table->activity,array('midx'=>$member->idx,'module'=>$module,'code'=>$code,'content'=>json_encode($content,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK),'exp'=>$exp,'reg_date'=>time()))->execute();
		if ($exp > 0) $this->db()->update($this->table->member,array('exp'=>$member->exp + $exp))->where('idx',$member->idx)->execute();
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'check') {
			$name = Request('name');
			$value = Request('value');
			
			if ($name == 'email') {
				if (CheckEmail($value) == true) {
					if ($this->db()->select($this->table->member)->where('email',$value)->has() == true) {
						$results->success = false;
						$results->message = $this->getLanguage('signup/help/email/duplicated');
					} else {
						$results->success = true;
					}
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('signup/help/email/error');
				}
			}
			
			if ($name == 'name') {
				if (strlen($value) > 0) {
					$results->success = true;
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('signup/help/name/error');
				}
			}
			
			if ($name == 'nickname') {
				if (CheckNickname($value) == true) {
					if ($this->db()->select($this->table->member)->where('nickname',$value)->where('idx',$this->getLogged(),'!=')->has() == true) {
						$results->success = false;
						$results->message =  $this->getLanguage('signup/help/nickname/duplicated');
					} else {
						$results->success = true;
					}
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('signup/help/nickname/error');
				}
			}
			
			if ($name == 'old_password') {
				if ($this->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				} else {
					$mHash = new Hash();
					if ($mHash->password_validate($value,$this->getMember()->password) == true) {
						$results->success = true;
						$results->message = $this->getLanguage('password/help/old_password/success');
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('password/help/old_password/error');
					}
				}
			}
		}
		
		if ($action == 'forceLogin') {
			$code = Decoder(Request('code'));
			
			if ($code === false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/invalidCode');
			} else {
				$data = json_decode($code);
				if ($data != null && $data->ip == $_SERVER['REMOTE_ADDR']) {
					$this->login($data->idx);
					$results->success = true;
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/invalidCode');
				}
			}
		}
		
		if ($action == 'login') {
			$mHash = new Hash();
			$email = Request('email');
			$password = Request('password');
			
			$results->errors = array();
			
			$loginFail = Request('loginFail','session') != null && is_array(Request('loginFail','session')) == true ? Request('loginFail','session') : array('count'=>0,'time'=>0);
			if ($loginFail['time'] > time()) {
				$results->success = false;
				$results->message = $this->getLanguage('login/error/login');
			} else {
				$check = $this->db()->select($this->table->member)->where('email',$email)->getOne();
				if ($check == null) {
					$results->success = false;
					$results->errors['email'] = $this->getLanguage('login/error/email');
					$loginFail['count']++;
					if ($loginFail['count'] == 5) {
						$loginFail['count'] = 0;
						$loginFail['time'] = time() + 60 * 60 * 5;
					}
					
					$values->email = $email;
					$values->password = $password;
				} elseif ($mHash->password_validate($password,$check->password) == false) {
					$results->success = false;
					$results->errors['password'] = $this->getLanguage('login/error/password');
					$loginFail['count']++;
					if ($loginFail['count'] == 5) {
						$loginFail['count'] = 0;
						$loginFail['time'] = time() + 60 * 60 * 5;
					}
					
					$values->email = $email;
					$values->password = $password;
				} else {
					if ($check->status == 'ACTIVE') {
						$this->db()->update($this->table->member,array('last_login'=>time()))->where('idx',$check->idx)->execute();
						$this->login($check->idx);
						$results->success = true;
					} elseif ($check->status == 'VERIFYING') {
						$_SESSION['MEMBER_REGISTER_IDX'] = Encoder($check->idx);
						$page = $this->getMemberPage('signup');
						$results->success = false;
						$results->redirect = $this->IM->getUrl($page->menu,$page->page,'verify');
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('error/'.$check->status);
					}
				}
			}
			$_SESSION['loginFail'] = $loginFail;
		}
		
		if ($action == 'logout') {
			unset($_SESSION['MEMBER_LOGGED']);
			$results->success = true;
		}
		
		if ($action == 'cert') {
			
			
			$results->success = true;
		}
		
		if ($action == 'signup') {
			$errors = array();
			$email = CheckEmail(Request('email')) == true ? Request('email') : $errors['email'] = $this->getLanguage('signup/help/email/error');
			$password = strlen(Request('password')) >= 4 ? Request('password') : $errors['password'] = $this->getLanguage('signup/help/password/error');
			if (strlen(Request('password')) < 4 || Request('password') != Request('password_confirm')) {
				$errors['password_confirm'] = $this->getLanguage('signup/help/password_confirm/error');
			}
			$name = CheckNickname(Request('name')) == true ? Request('name') : $errors['name'] = $this->getLanguage('signup/help/name/error');
			$nickname = CheckNickname(Request('nickname')) == true ? Request('nickname') : $errors['nickname'] = $this->getLanguage('signup/help/nickname/error');
			
			if ($this->db()->select($this->table->member)->where('email',$email)->has() == true) {
				$errors['email'] = $this->getLanguage('signup/help/email/duplicated');
			}
			
			if ($this->db()->select($this->table->member)->where('nickname',$nickname)->has() == true) {
				$errors['nickname'] = $this->getLanguage('signup/help/nickname/duplicated');
			}
			
			if (empty($errors) == true) {
				$mHash = new Hash();
				
				$insert = array();
				$insert['gidx'] = Request('registerGIDX','session');
				$insert['email'] = $email;
				$insert['password'] = $mHash->password_hash($password);
				$insert['name'] = $name;
				$insert['nickname'] = $nickname;
				$insert['status'] = 'VERIFYING';
				
				$idx = $this->db()->insert($this->table->member,$insert)->execute();
				
				if ($idx !== false) {
					$results->success = true;
					$_SESSION['MEMBER_REGISTER_IDX'] = Encoder($idx);
					$this->sendVerifyEmail($idx);
					unset($_SESSION['registerGIDX']);
				} else {
					$results->success = false;
				}
			} else {
				$results->success = false;
				$results->errors = $errors;
			}
		}
		
		if ($action == 'verifyEmail') {
			$registerIDX = Request('registerIDX');
			
			if ($registerIDX == null) {
				$results->success = false;
			} else {
				$email = Request('email');
				$email_verify_code = Request('email_verify_code');
				$check = $this->db()->select($this->table->email)->where('midx',$registerIDX)->where('email',$email)->getOne();
				
				if ($check == null) {
					$results->success = false;
					$results->errors = array('email'=>$this->getLanguage('verifyEmail/help/email/notFound'));
				} elseif ($check->code == $email_verify_code) {
					$this->db()->update($this->table->email,array('status'=>'VERIFIED'))->where('midx',$registerIDX)->where('email',$email)->execute();
					$this->db()->update($this->table->member,array('status'=>'ACTIVE'))->where('idx',$registerIDX)->execute();
					$results->success = true;
				} else {
					$results->success = false;
					$results->errors = array('email_verify_code'=>$this->getLanguage('verifyEmail/help/email_verify_code/error'));
				}
			}
		}
		
		if ($action == 'sendVerifyEmail') {
			$registerIDX = Request('registerIDX');
			$email = Request('email');
			
			if ($this->isLogged() == true) {
				if (CheckEmail($email) == false) {
					$results->success = false;
					$results->errors = array('email'=>$this->getLanguage('modifyEmail/help/email/error'));
				} elseif ($this->db()->select($this->table->member)->where('email',$email)->count() == 1) {
					$results->success = false;
					$results->errors = array('email'=>$this->getLanguage('modifyEmail/help/email/duplicated'));
				} else {
					$check = $this->db()->select($this->table->email)->where('midx',$this->getLogged())->where('email',$email)->getOne();
					if ($check == null || $check->status != 'SENDING' || ($check->status == 'SENDING' && $check->reg_date + 300 < time())) {
						$this->db()->delete($this->table->email)->where('midx',$this->getLogged())->where('email',$email)->execute();
						$status = $this->sendVerifyEmail($this->getLogged(),$email);
						$results->success = true;
						$results->message = $this->getLanguage('verifyEmail/sending');
					} else {
						$results->success = false;
						$results->message = $this->getLanguage('verifyEmail/error/sending');
					}
				}
			} elseif ($registerIDX != null) {
				$member = $this->db()->select($this->table->member)->where('idx',$registerIDX)->getOne();
				if ($member == null || $member->status != 'VERIFYING') {
					$results->success = false;
					$results->message = $this->getLanguage('verifyEmail/error/target');
				} else {
					if (CheckEmail($email) == false) {
						$results->success = false;
						$results->message = $this->getLanguage('verifyEmail/error/email');
					} else {
						$check = $this->db()->select($this->table->email)->where('midx',$registerIDX)->where('email',$email)->getOne();
						if ($check->status == 'VERIFIED') {
							$signupPage = $this->getMemberPage('signup');
							$results->success = true;
							$this->db()->update($this->table->member,array('status'=>'ACTIVE'))->where('idx',$registerIDX)->execute();
							$results->redirect = $this->IM->getUrl($signupPage->menu,$signupPage->page,'complete');
						} elseif ($check == null || $check->status == 'CANCELED' || ($check->status == 'SENDING' && $check->reg_date + 300 < time())) {
							$this->db()->delete($this->table->email)->where('midx',$registerIDX)->where('email',$email)->execute();
							$status = $this->sendVerifyEmail($registerIDX,$email);
							$results->success = true;
							$results->message = $this->getLanguage('verifyEmail/sending');
						} else {
							$results->success = false;
							$results->message = $this->getLanguage('verifyEmail/error/sending');
						}
					}
				}
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			}
		}
		
		if ($action == 'photoEdit') {
			$templet = Request('templet');
			if ($this->isLogged() == true) {
				$results->success = true;
				$results->modalHtml = $this->getPhotoEdit($templet);
				$results->photo = $this->getMember()->photo;
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			}
		}
		
		if ($action == 'photoUpload') {
			$photo = Request('photo');
			if ($this->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} else {
				if (preg_match('/^data:image\/(.*?);base64,(.*?)$/',$photo,$match) == true) {
					$bytes = base64_decode($match[2]);
					file_put_contents($this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg',$bytes);
					$this->IM->getModule('attachment')->createThumbnail($this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg',$this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg',250,250,false,'jpg');
					
					$results->success = true;
					$results->message = $this->getLanguage('photoEdit/success');
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('photoEdit/error');
				}
			}
		}
		
		if ($action == 'modifyEmail') {
			$confirm = Request('confirm');
			
			if ($confirm == 'TRUE') {
				$email = Request('email');
				$code = Request('code');
				$check = $this->db()->select($this->table->email)->where('midx',$this->getLogged())->where('email',$email)->getOne();
				
				if ($check == null || $check->code != $code) {
					$results->success = false;
					$results->errors = array('code'=>$this->getLanguage('modifyEmail/help/code/error'));
				} else {
					$this->db()->update($this->table->email,array('status'=>'VERIFIED'))->where('midx',$this->getLogged())->where('email',$email)->execute();
					$this->db()->update($this->table->member,array('email'=>$email))->where('idx',$this->getLogged())->execute();
					$results->success = true;
					$results->message = $this->getLanguage('modifyEmail/success');
				}
			} else {
				$templet = Request('templet');
				if ($this->isLogged() == true) {
					$results->success = true;
					$results->modalHtml = $this->getModifyEmail($templet);
				} else {
					$results->success = false;
					$results->message = $this->getLanguage('error/notLogged');
				}
			}
		}
		
		if ($action == 'modify') {
			$step = Request('step');
			
			if ($step == 'verify') {
				$member = $this->getMember();
				$password = Request('password');
				
				$mHash = new Hash();
				
				if ($mHash->password_validate($password,$member->password) == true) {
					$results->success = true;
					$results->password = Encoder($password);
				} else {
					$results->success = false;
					$results->errors = array('password'=>$this->getLanguage('verify/help/password/error'));
				}
			}
			
			if ($step == 'modify') {
				$errors = array();
				$values->name = Request('name') ? Request('name') : $errors['name'] = $this->getLanguage('signup/help/name/error');
				$values->nickname = Request('nickname') ? Request('nickname') : $errors['nickname'] = $this->getLanguage('signup/help/nickname/error');
				
				if ($this->isLogged() == false) {
					$results->success = false;
					$results->message = $this->getLangauge('error/notLogged');
				} elseif (count($errors) == 0) {
					$insert = array();
					$insert['name'] = $values->name;
					$insert['nickname'] = $values->nickname;
					
					$this->db()->update($this->table->member,$insert)->where('idx',$this->getLogged())->execute();
					$results->success = true;
					$results->message = $this->getLanguage('modify/success');
				} else {
					$results->success = false;
					$results->errors = $errors;
				}
			}
		}
		
		if ($action == 'password') {
			$errors = array();
			$password = strlen(Request('password')) >= 4 ? Request('password') : $errors['password'] = $this->getLanguage('signup/help/password/error');
			if (strlen(Request('password')) < 4 || Request('password') != Request('password_confirm')) {
				$errors['password_confirm'] = $this->getLanguage('signup/help/password_confirm/error');
			}
			
			if ($this->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLangauge('error/notLogged');
			} else {
				$mHash = new Hash();
				
				if (strlen($this->getMember()->password) == 65) {
					$old_password = Request('old_password');
					if ($old_password == '' || $mHash->password_validate($old_password,$this->getMember()->password) == false) {
						$errors['old_password'] = $this->getLanguage('password/help/old_password/error');
					}
				}
				
				if (count($errors) == 0) {
					$password = $mHash->password_hash($password);
					$this->db()->update($this->table->member,array('password'=>$password))->where('idx',$this->getLogged())->execute();
					$results->success = true;
					$results->message = $this->getLanguage('password/success');
				} else {
					$results->success = false;
					$results->errors = $errors;
				}
			}
		}
		
		if ($action == 'facebook') {
			if (Request('SOCIAL_REDIRECT_URL','session') == null) {
				$_SESSION['SOCIAL_REDIRECT_URL'] = $_SERVER['HTTP_REFERER'];
			}
			
			if ($this->IM->domain == 'www.arzz.com') {
				$CLIENT_ID = '985851538105124';
				$CLIENT_SECRET = 'c6b74ae32d4786b440bb878c46ee2998';
			} elseif ($this->IM->domain == 'www.minitalk.kr') {
				$CLIENT_ID = '418845248317025';
				$CLIENT_SECRET = '5850c198f8f4b0b254a53ae7f9049600';
			} else {
				$CLIENT_ID = '985851538105124';
				$CLIENT_SECRET = 'c6b74ae32d4786b440bb878c46ee2998';
			}
			
			$AUTH_URL = 'https://graph.facebook.com/oauth/authorize';
			$TOKEN_URL = 'https://graph.facebook.com/oauth/access_token';
			
			$facebook = new OAuthClient();
			$facebook->setClientId($CLIENT_ID)->setClientSecret($CLIENT_SECRET)->setScope('public_profile,email')->setAccessType('offline')->setAuthUrl($AUTH_URL)->setTokenUrl($TOKEN_URL);

			if (isset($_GET['code']) == true) {
				if ($facebook->authenticate($_GET['code']) == true) {
					$redirectUrl = $facebook->getRedirectUrl();
					header('location:'.$redirectUrl);
				}
				exit;
			} elseif ($facebook->getAccessToken() == null) {
				$authUrl = $facebook->getAuthenticationUrl();
				header('location:'.$authUrl);
				exit;
			}
			
			$data = $facebook->get('https://graph.facebook.com/me',array('fields'=>'id,email,name'));
			if ($data === false || empty($data->email) == true) $this->IM->printError('API ERROR');
			
			$accessToken = $facebook->getAccessToken();
			$refreshToken = $facebook->getRefreshToken() == null ? '' : $facebook->getRefreshToken();
			
			$this->socialLogin('facebook',$data->id,$data->name,$data->email,'https://graph.facebook.com/'.$data->id.'/picture?width=250&height=250',$accessToken,$refreshToken);
		}
		
		if ($action == 'google') {
			if (Request('SOCIAL_REDIRECT_URL','session') == null) {
				$_SESSION['SOCIAL_REDIRECT_URL'] = $_SERVER['HTTP_REFERER'];
			}
			
			if ($this->IM->domain == 'www.arzz.com') {
				$CLIENT_ID = '367657130146-m9ojilvf3kbsv6j24uieartls0ols8t8.apps.googleusercontent.com';
				$CLIENT_SECRET = 'GVgWL29VdBiSQIuRTlL5RZDc';
			} elseif ($this->IM->domain == 'www.minitalk.kr') {
				$CLIENT_ID = '476101389490-mug55vcsit7af2sd095m3c8fhd3agssu.apps.googleusercontent.com';
				$CLIENT_SECRET = 'CJKMFEkaWkiasXWIj42WY4HU';
			} else {
				$CLIENT_ID = '995059916144-2odfvfoh0h18fhfsid1lh25d1vpunm5n.apps.googleusercontent.com';
				$CLIENT_SECRET = 'A3G-GgF_2rsWXUuvmU1hPLOv';
			}
			$AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
			$TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
			
			$google = new OAuthClient();
			$google->setClientId($CLIENT_ID)->setClientSecret($CLIENT_SECRET)->setScope('https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email')->setAccessType('offline')->setAuthUrl($AUTH_URL)->setTokenUrl($TOKEN_URL);

			if (isset($_GET['code']) == true) {
				if ($google->authenticate($_GET['code']) == true) {
					$redirectUrl = $google->getRedirectUrl();
					header('location:'.$redirectUrl);
				}
				exit;
			} elseif ($google->getAccessToken() == null) {
				$authUrl = $google->getAuthenticationUrl();
				header('location:'.$authUrl);
				exit;
			}
			
			$data = $google->get('https://www.googleapis.com/plus/v1/people/me');
			if ($data === false || empty($data->emails) == true) $this->IM->printError('API ERROR');
			for ($i=0, $loop=count($data->emails);$i<$loop;$i++) {
				if ($data->emails[$i]->type == 'account') {
					$data->email = $data->emails[$i]->value;
					break;
				}
			}
			
			$data->photo = str_replace('sz=50','sz=250',$data->image->url);
			
			$accessToken = $google->getAccessToken();
			$refreshToken = $google->getRefreshToken() == null ? '' : $google->getRefreshToken();
			
			$this->socialLogin('google',$data->id,$data->displayName,$data->email,$data->photo,$accessToken,$refreshToken);
		}
		
		if ($action == 'youtube') {
			if (Request('SOCIAL_REDIRECT_URL','session') == null) {
				$_SESSION['SOCIAL_REDIRECT_URL'] = $_SERVER['HTTP_REFERER'];
			}
			
			if ($this->isLogged() == false) die($this->getError('NOT_LOGGED'));
			
			$CLIENT_ID = '995059916144-2odfvfoh0h18fhfsid1lh25d1vpunm5n.apps.googleusercontent.com';
			$CLIENT_SECRET = 'A3G-GgF_2rsWXUuvmU1hPLOv';
			$AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
			$TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
			
			$youtube = new OAuthClient();
			$youtube->setClientId($CLIENT_ID)->setClientSecret($CLIENT_SECRET)->setScope('https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.upload https://www.googleapis.com/auth/youtubepartner https://www.googleapis.com/auth/youtube.force-ssl')->setAccessType('offline')->setAuthUrl($AUTH_URL)->setTokenUrl($TOKEN_URL);

			if (isset($_GET['code']) == true) {
				if ($youtube->authenticate($_GET['code']) == true) {
					$redirectUrl = $youtube->getRedirectUrl();
					header('location:'.$redirectUrl);
				}
				exit;
			} elseif ($youtube->getAccessToken() == null) {
				$authUrl = $youtube->getAuthenticationUrl();
				header('location:'.$authUrl);
				exit;
			}
			
			$data = $youtube->get('https://www.googleapis.com/plus/v1/people/me');
			if ($data === false || empty($data->emails) == true) $this->IM->printError('API ERROR');
			for ($i=0, $loop=count($data->emails);$i<$loop;$i++) {
				if ($data->emails[$i]->type == 'account') {
					$data->email = $data->emails[$i]->value;
					break;
				}
			}
			
			$accessToken = $youtube->getAccessToken();
			$refreshToken = $youtube->getRefreshToken() == null ? '' : $youtube->getRefreshToken();
			
			$check = $this->db()->select($this->table->social)->where('midx',$this->getLogged())->where('code','youtube')->getOne();
			if ($check == null) {
				$this->db()->insert($this->table->social,array('midx'=>$this->getLogged(),'code'=>'youtube','user_id'=>$data->id,'email'=>$data->email,'access_token'=>$accessToken,'refresh_token'=>$refreshToken))->execute();
			} else {
				$this->db()->update($this->table->social,array('user_id'=>$data->id,'email'=>$data->email,'access_token'=>$accessToken,'refresh_token'=>$refreshToken))->where('midx',$this->getLogged())->where('code','youtube')->execute();
			}
			
			unset($_SESSION['OAUTH_ACCESS_TOKEN']);
			unset($_SESSION['OAUTH_REFRESH_TOKEN']);
			
			$redirectUrl = Request('SOCIAL_REDIRECT_URL','session') != null ? Request('SOCIAL_REDIRECT_URL','session') : '/';
			
			unset($_SESSION['SOCIAL_REDIRECT_URL']);
			
			header('location:'.$redirectUrl);
		}
		
		if ($action == 'github') {
			if (Request('SOCIAL_REDIRECT_URL','session') == null) {
				$_SESSION['SOCIAL_REDIRECT_URL'] = $_SERVER['HTTP_REFERER'];
			}
			
			if ($this->IM->domain == 'www.arzz.com') {
				$CLIENT_ID = 'b3f954eccc5378afbacf';
				$CLIENT_SECRET = '4507787bbac2f89382c5b29dc07017bbc776c218';
			} elseif ($this->IM->domain == 'www.minitalk.kr') {
				$CLIENT_ID = 'a5b5c360b237ed9de0c7';
				$CLIENT_SECRET = '0f5e658a0d05f83ee918da13cfe070ff5bc42e60';
			} else {
				$CLIENT_ID = 'b3f954eccc5378afbacf';
				$CLIENT_SECRET = '4507787bbac2f89382c5b29dc07017bbc776c218';
			}
			
			$AUTH_URL = 'https://github.com/login/oauth/authorize';
			$TOKEN_URL = 'https://github.com/login/oauth/access_token';
			
			$github = new OAuthClient();
			$github->setClientId($CLIENT_ID)->setClientSecret($CLIENT_SECRET)->setAuthUrl($AUTH_URL)->setScope('user')->setAccessType('offline')->setUserAgent('Awesome-Octocat-App')->setTokenUrl($TOKEN_URL);

			if (isset($_GET['code']) == true) {
				if ($github->authenticate($_GET['code']) == true) {
					$redirectUrl = $github->getRedirectUrl();
					header('location:'.$redirectUrl);
				}
				exit;
			} elseif ($github->getAccessToken() == null) {
				$authUrl = $github->getAuthenticationUrl();
				header('location:'.$authUrl);
				exit;
			}
			
			$data = $github->get('https://api.github.com/user');
			if ($data === false || empty($data->email) == true) $this->IM->printError('API ERROR');
			
			$accessToken = $github->getAccessToken();
			$refreshToken = $github->getRefreshToken() == null ? '' : $github->getRefreshToken();
			
			$this->socialLogin('github',$data->id,$data->name,$data->email,$data->avatar_url,$accessToken,$refreshToken);
		}
		
		$this->IM->fireEvent('afterDoProcess','member',$action,$values,$results);
		
		return $results;
	}
	
	function socialLogin($code,$user_id,$name,$email,$photo,$accessToken,$refreshToken) {
		if ($this->isLogged() == true) {
			$check = $this->db()->select($this->table->social)->where('midx',$this->getLogged())->where('code',$code)->getOne();
			if ($check == null) {
				$this->db()->insert($this->table->social,array('midx'=>$this->getLogged(),'code'=>$code,'user_id'=>$user_id,'email'=>$email,'access_token'=>$accessToken,'refresh_token'=>$refreshToken))->execute();
			} else {
				$this->db()->update($this->table->social,array('user_id'=>$user_id,'email'=>$email,'access_token'=>$accessToken,'refresh_token'=>$refreshToken))->where('midx',$this->getLogged())->where('code',$code)->execute();
			}
			
			if (file_exists($this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg') == false) {
				if (SaveFileFromUrl($photo,$this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg','image') == true) {
					$this->IM->getModule('attachment')->createThumbnail($this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg',$this->IM->getAttachmentPath().'/member/'.$this->getLogged().'.jpg',250,250,false,'jpg');
				}
			}
			
			$_SESSION['MEMBER_MODIFY_PASSWORD'] = true;
		} else {
			$check = $this->db()->select($this->table->social)->where('code',$code)->where('user_id',$user_id)->get();
			
			if (count($check) == 0) {
				$checkEmail = $this->db()->select($this->table->member)->where('email',$email)->getOne();
				
				if ($checkEmail == null) {
					$insert = array();
					$insert['type'] = 'MEMBER';
					$insert['gidx'] = 'default';
					$insert['email'] = $email;
					$insert['password'] = '';
					$insert['name'] = $insert['nickname'] = $name;
					$insert['reg_date'] = $insert['last_login'] = time();
					$insert['status'] = 'ACTIVE';
					
					$idx = $this->db()->insert($this->table->member,$insert)->execute();
					$this->login($idx);
					
					header('location:'.$this->IM->getProcessUrl('member',$code));
					exit;
				} elseif (strlen($checkEmail->password) == 65) {
					$config = new stdClass();
					$config->type = 'duplicated';
					$config->member = $this->getMember($checkEmail->idx);
					
					$this->IM->addSiteHeader('script',__IM_DIR__.'/scripts/php2js.js.php?language='.$this->IM->language);
					
					$context = $this->getContext('social',$config);
					$header = $this->IM->printHeader();
					$footer = $this->IM->printFooter();
					
					echo $header;
					echo $context;
					echo $footer;
					
					exit;
				} else {
					$this->login($checkEmail->idx);
				}
			} elseif (count($check) == 1) {
				$this->login($check[0]->midx);
			} else {
				$config = new stdClass();
				$config->type = 'select';
				$config->account = $check;
				$config->redirectUrl = Request('SOCIAL_REDIRECT_URL','session') != null ? Request('SOCIAL_REDIRECT_URL','session') : '/';
				$config->photo = $photo;
				
				$this->IM->addSiteHeader('script',__IM_DIR__.'/scripts/php2js.js.php?language='.$this->IM->language);
				
				$context = $this->getContext('social',$config);
				$header = $this->IM->printHeader();
				$footer = $this->IM->printFooter();
				
				echo $header;
				echo $context;
				echo $footer;
				
				unset($_SESSION['OAUTH_ACCESS_TOKEN']);
				unset($_SESSION['OAUTH_REFRESH_TOKEN']);
				unset($_SESSION['SOCIAL_REDIRECT_URL']);
				
				exit;
			}
		}
		
		unset($_SESSION['OAUTH_ACCESS_TOKEN']);
		unset($_SESSION['OAUTH_REFRESH_TOKEN']);
		
		$redirectUrl = Request('SOCIAL_REDIRECT_URL','session') != null ? Request('SOCIAL_REDIRECT_URL','session') : '/';
		
		unset($_SESSION['SOCIAL_REDIRECT_URL']);
		
		header('location:'.$redirectUrl);
	}
}
?>