<?php
class ModuleMinitalk {
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
		$this->table->server = 'minitalk_server_table';
		$this->table->price = 'minitalk_price_table';
		$this->table->hosting = 'minitalk_service_hosting_table';

		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/minitalk.js');
		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
	}
	
	function getApi($api) {
		$data = new stdClass();
		$version = Request('version');
		$serverVersion = preg_replace('/\.[0-9]+$/','',$version);
		
		if (version_compare($version,'6.3','>=') == true) {
			if ($api == 'register') {
				$email = Request('email');
				$password = Request('password');
				$client_id = strtoupper(Request('client_id'));
				$server_id = strtoupper(Request('server_id'));
				$callback = Request('callback');
				
				$midx = $this->IM->getModule('member')->isValidate($email,$password);
				if ($midx !== false) {
					$service = $this->db()->select($this->table->hosting)->where('midx',$midx)->where('client_id',$client_id)->getOne();
					if ($service == null) {
						$data->success = false;
						$data->message = '등록되어 있는 접속키가 아닙니다.<br />접속키를 한번더 확인하여 주십시오.';
					} elseif ($service->server_id && $service->server_id != $server_id) {
						$data->success = false;
						$data->message = '이미 다른 미니톡과 연동되어 있는 접속키입니다.<br />다른 미니톡클라이언트와 이미 연동이 되어있거나, 연동 후 미니톡클라이언트의 접속주소가 변경된 경우입니다.<br />미니톡 홈페이지에서 해당 접속키 연동정보를 초기화한 뒤 다시 시도하여 주십시오.';
					} elseif ($service->expire_date < time()) {
						$data->success = false;
						$data->message = '채팅호스팅 서비스 만료일이 지났습니다.<br>만료일을 연장하시거나, 새로운 클라이언트 ID를 발급받아 주십시오.';
					} else {
						$this->db()->update($this->table->hosting,array('server_id'=>$server_id,'callback'=>$callback,'check_date'=>time()))->where('idx',$service->idx)->execute();
						$data->success = true;
						$data->client_id = $client_id;
					}
				} else {
					$data->success = false;
					$data->message = '로그인에 실패하였습니다.<br />미니톡 홈페이지의 이메일주소와 패스워드를 정확히 입력하여 주십시오.'.$email;
				}
			}
			
			if ($api == 'getInfo') {
				$client_id = strtoupper(Request('client_id'));
				$server_id = strtoupper(Request('server_id'));
				
				$service = $this->db()->select($this->table->hosting)->where('client_id',$client_id)->getOne();
				
				if ($service == null) {
					$data->success = false;
				} else {
					$data->success = true;
					$data->user = $service->user;
					$data->channel = $service->channel;
					$data->maxuser = $service->maxuser;
					$data->expire_date = $service->expire_date < time() ? '' : date('Y-m-d H:i:s',$service->expire_date).'(KST)';
	
					if ($service->server == '0') {
						$data->check_date = date('Y-m-d H:i:s').'(KST)';
					} else {
						$server = $this->db()->select($this->table->server)->where('idx',$service->server)->getOne();
						$data->check_date = date('Y-m-d H:i:s',$server->check_date).'(KST)';
					}
					$data->status = $this->db()->select($this->table->server)->where('status','ONLINE')->where('version',$serverVersion)->count() > 0 ? 'ONLINE' : 'OFFLINE';
					$data->auth = $server_id == $service->server_id;
				}
			}
			
			if ($api == 'getConnect') {
				$client_id = strtoupper(Request('client_id'));
				$server_id = strtoupper(Request('server_id'));
				$ip = Request('ip');
				$key = Request('key');
				$isForce = Request('force') == 'TRUE';
				
				$service = $this->db()->select($this->table->hosting)->where('client_id',$client_id)->getOne();
				if ($service == null || $service->server_id != $server_id) {
					$data->success = false;
					$data->error = 202;
				} elseif ($service->expire_date < time()) {
					$data->success = false;
					$data->error = 203;
				} else {
					$this->updateServer($serverVersion);
					
					if ($service->server == 0 || $this->checkOnline($service->server) == false) {
						$server = $this->db()->select($this->table->server)->where('version',$serverVersion)->orderBy('user','asc')->where('status','ONLINE')->getOne();
						if ($server != null) {
							$service->server = $server->idx;
							$this->db()->update($this->table->hosting,array('server'=>$server->idx))->where('idx',$service->idx)->execute();
						} else {
							$service->server = 0;
							$this->db()->update($this->table->hosting,array('server'=>0))->where('idx',$service->idx)->execute();
						}
					}
					
					if ($service->server == 0) {
						$data->success= false;
						$data->error = 201;
					} else {
						$server = $this->db()->select($this->table->server)->where('idx',$service->server)->getOne();
						$server->channelCode = 'H'.sprintf('%09d',$service->idx);
						$server->serverCode = urlencode(Encoder(json_encode(
							array(
								'ip'=>$ip,
								'expire_date'=>$service->expire_date,
								'maxuser'=>$service->maxuser,
								'key'=>$key
							)
						),'com.arzz.program.kr.minitalk.www'));
						
						$data->success = true;
						unset($server->user,$server->channel,$server->status,$server->check_date);
						$data->server = $server;
					}
				}
			}
			
			if ($api == 'callback') {
				$d = json_decode(Request('d'));
				
				if ($d->action == 'save_channel') {
					$sIdx = preg_replace('/H0+/','',$d->code);
					$check = $this->db()->select($this->table->hosting)->where('idx',$sIdx)->getOne();
					
					if ($check != null) {
						$curlsession = curl_init();
						curl_setopt($curlsession,CURLOPT_URL,$check->callback);
						curl_setopt($curlsession,CURLOPT_POST,1);
						curl_setopt($curlsession,CURLOPT_POSTFIELDS,array('action'=>'save_channel','mcode'=>$check->client_id,'list'=>json_encode($d->list)));
						curl_setopt($curlsession,CURLOPT_TIMEOUT,10);
						curl_setopt($curlsession,CURLOPT_RETURNTRANSFER,1);
						$buffer = curl_exec($curlsession);
						curl_close($curlsession);
						
						if ($buffer) exit(json_encode(json_decode($buffer,true)));
						else exit(json_encode(array('success'=>false)));
					} else {
						exit(json_encode(array('success'=>false)));
					}
				}
				
				if ($d->action == 'banip') {
					$sIdx = preg_replace('/H0+/','',$d->code);
					$check = $this->db()->select($this->table->hosting)->where('idx',$sIdx)->getOne();
					
					print_r($check);
					$memo = 'from '.$d->from;
					
					if ($check != null) {
						$curlsession = curl_init();
						curl_setopt($curlsession,CURLOPT_URL,$check->callback);
						curl_setopt($curlsession,CURLOPT_POST,1);
						curl_setopt($curlsession,CURLOPT_POSTFIELDS,array('action'=>'banip','mcode'=>$check->client_id,'ip'=>$d->ip,'memo'=>$memo));
						curl_setopt($curlsession,CURLOPT_TIMEOUT,10);
						curl_setopt($curlsession,CURLOPT_RETURNTRANSFER,1);
						$buffer = curl_exec($curlsession);
						curl_close($curlsession);
						
						if ($buffer) exit(json_encode(json_decode($buffer,true)));
						else exit(json_encode(array('success'=>false)));
					} else {
						exit(json_encode(array('success'=>false)));
					}
				}
			}
		}
		
		/*
		if ($api == 'register') {
			if (version_compare('))
			$domain = $server->is_ssl == 'TRUE' ? 'https://'.$server->domain.':'.$server->port : 'http://'.$server->domain.':'.$server->port;
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$domain.'/health');
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_TIMEOUT,10);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			$data = curl_exec($ch);
			$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code == 200) {
				$server->status = 'ONLINE';
				$health = json_decode($data);
				$server->user = 0;
				$server->channel = 0;
				$server->memory = round($health->memory->rss / 1024);
				$server->uptime = round($health->uptime);
			} else {
				$server->status = 'OFFLINE';
				$server->user = 0;
				$server->channel = 0;
				$server->memory = 0;
				$server->uptime = 0;
			}
			$server->check_date = time();
			
			$this->db()->update($this->table->server,array('status'=>$server->status,'channel'=>$server->channel,'user'=>$server->user,'memory'=>$server->memory,'uptime'=>$server->uptime,'check_date'=>$server->check_date))->where('idx',$server->idx)->execute();
		} else {
			$fs = @fsockopen($server->ip,$server->port,$errorno,$errorstr,10);
			if (!$fs) {
				$server->status = 'OFFLINE';
				$server->user = 0;
				$server->channel = 0;
				$server->memory = 0;
				$server->uptime = 0;
				$this->db()->update($this->table->server,array('status'=>'OFFLINE','user'=>0,'channel'=>0,'check_date'=>time()))->where('idx',$idx)->execute();
			} else {
				$server->status = 'ONLINE';
				$this->db()->update($this->table->server,array('status'=>'ONLINE','check_date'=>time()))->where('idx',$idx)->execute();
			}
		}
		*/
		
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
		return null;
		/*
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
		*/
	}
	
	function getContext($view) {
		ob_start();
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div class="ModuleMinitalk">'.PHP_EOL;
		
		if (file_exists($this->Module->getPath().'/views/'.$this->IM->language.'/'.$view.'.php') == true) {
			INCLUDE $this->Module->getPath().'/views/'.$this->IM->language.'/'.$view.'.php';
		} else {
			INCLUDE $this->Module->getPath().'/views/ko/'.$view.'.php';
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getRefundPrice($data) {
		$refund = floor($data->price / floor(($data->expire_date - $data->reg_date) / 60 / 60 / 24)) * floor(($data->expire_date - time()) / 60 / 60 / 24);
		$refund = $refund > 0 ? $refund : 0;
		
		return $refund;
	}
	
	function getDiscountPrice($price,$time) {
		$timeDiscountRate = 0;
		$timeDiscountRate = $time == 90 ? 5 : $timeDiscountRate;
		$timeDiscountRate = $time == 180 ? 10 : $timeDiscountRate;
		
		$memberDiscountRate = $this->IM->getModule('member')->getMember() == null ? 0 : floor($this->IM->getModule('member')->getMember()->level->level / 2);
		
		if ($price > 0) {
			$discount_time = floor($price * $timeDiscountRate / 100);
			$discount_member = floor(($price - $discount_time) * $memberDiscountRate / 100);
		} else {
			$discount_time = 0;
			$discount_member = 0;
		}
		
		return $price - $discount_time - $discount_member;
	}
	
	function getMyHosting() {
		$services = $this->db()->select($this->table->hosting)->where('midx',$this->IM->getModule('member')->getLogged())->orderBy('expire_date','asc')->get();
		
		ob_start();
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div id="ModuleMinitalkMyHosting">'.PHP_EOL;
		
		if (file_exists($this->Module->getPath().'/views/'.$this->IM->language.'/myhosting.php') == true) {
			INCLUDE $this->Module->getPath().'/views/'.$this->IM->language.'/myhosting.php';
		} else {
			INCLUDE $this->Module->getPath().'/views/ko/myhosting.php';
		}
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getServerList() {
		$servers = $this->db()->select($this->table->server)->orderBy('idx','asc')->get();
		
		ob_start();
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div id="ModuleMinitalkServerList">'.PHP_EOL;
		
		if (file_exists($this->Module->getPath().'/views/'.$this->IM->language.'/serverlist.php') == true) {
			INCLUDE $this->Module->getPath().'/views/'.$this->IM->language.'/serverlist.php';
		} else {
			INCLUDE $this->Module->getPath().'/views/ko/serverlist.php';
		}
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getDisconnect($data) {
		ob_start();
		
		if ($data->server_id == '') {
			$title = '서비스 삭제하기';
			$text = '서비스를 삭제하면 잔여기간에 대한 이용요금 <span class="fontRed"><i class="fa fa-rub"></i> '.number_format($this->getRefundPrice($data)).'</span>이 환불됩니다.<br>서비스를 정말 삭제하시겠습니까?';
		} else {
			$title = '서비스 연결해제';
			$text = '서비스 연결해제시 연결된 미니톡 클라이언트에서 더이상 해당 클라이언트 ID로 미니톡 채팅호스팅서버에 접속하지 못합니다.<br>서비스를 삭제하려면 먼저 연결해제 후 삭제할 수 있으며, 서비스 삭제시 잔여기간에 대한 이용요금은 포인트로 환불됩니다.<br>정말 클라이언트와의 연결을 해제하시겠습니까?';
		}
		
		echo '<form name="ModuleMinitalkDisconnect" onsubmit="return Minitalk.hosting.disconnect('.$data->idx.',true);">'.PHP_EOL;
		echo '<input type="hidden" name="action" value="disconnect">'.PHP_EOL;
		
		$content = '<div class="message">'.$text.'</div>'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		if (file_exists($this->Module->getPath().'/views/'.$this->IM->language.'/modal.php') == true) {
			INCLUDE $this->Module->getPath().'/views/'.$this->IM->language.'/modal.php';
		} else {
			INCLUDE $this->Module->getPath().'/views/modal.php';
		}
		
		echo '</form>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function isServerOnline($idx,$isForce=false) {
		$server = $this->db()->select($this->table->server)->where('idx',$idx)->getOne();
		if ($server == null) return false;
		if ($server->status == 'ONLINE' && $server->check_date > time() - 120) return true;
		
		$fs = @fsockopen($server->ip,$server->port,$errorno,$errorstr,10);
		if (!$fs) {
			$this->db()->update($this->table->server,array('status'=>'OFFLINE','user'=>0,'channel'=>0,'check_date'=>time()))->where('idx',$idx)->execute();
			return false;
		} else {
			$this->db()->update($this->table->server,array('status'=>'ONLINE','check_date'=>time()))->where('idx',$idx)->execute();
			return true;
		}
	}
	
	function checkOnline($idx) {
		$server = $this->db()->select($this->table->server)->where('idx',$idx)->getOne();
		if ($server == null) return false;
		if ($server->status == 'ONLINE' && $server->check_date > time() - 120) return true;
		
		$server = $this->checkServer($server);
		return $server->status == 'ONLINE';
	}
	
	function checkServer($server) {
		if (is_numeric($server) == true) $server = $this->db->select($this->table->server)->where('idx',$server)->getOne();
		
		if (version_compare($server->version,'6.3','>=') == true) {
			$domain = $server->is_ssl == 'TRUE' ? 'https://'.$server->domain.':'.$server->port : 'http://'.$server->domain.':'.$server->port;
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$domain.'/health');
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_TIMEOUT,10);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			$data = curl_exec($ch);
			$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
			curl_close($ch);
			
			if ($http_code == 200) {
				$server->status = 'ONLINE';
				$health = json_decode($data);
				$server->user = 0;
				$server->channel = 0;
				$server->memory = round($health->memory->rss / 1024);
				$server->uptime = round($health->uptime);
			} else {
				$server->status = 'OFFLINE';
				$server->user = 0;
				$server->channel = 0;
				$server->memory = 0;
				$server->uptime = 0;
			}
			$server->check_date = time();
			
			$this->db()->update($this->table->server,array('status'=>$server->status,'channel'=>$server->channel,'user'=>$server->user,'memory'=>$server->memory,'uptime'=>$server->uptime,'check_date'=>$server->check_date))->where('idx',$server->idx)->execute();
		} else {
			$fs = @fsockopen($server->ip,$server->port,$errorno,$errorstr,10);
			if (!$fs) {
				$server->status = 'OFFLINE';
				$server->user = 0;
				$server->channel = 0;
				$server->memory = 0;
				$server->uptime = 0;
				$this->db()->update($this->table->server,array('status'=>'OFFLINE','user'=>0,'channel'=>0,'check_date'=>time()))->where('idx',$idx)->execute();
			} else {
				$server->status = 'ONLINE';
				$this->db()->update($this->table->server,array('status'=>'ONLINE','check_date'=>time()))->where('idx',$idx)->execute();
			}
		}
		
		return $server;
	}
	
	function updateServer($version=null,$isForce=false) {
		$server = $this->db()->select($this->table->server);
		if ($version != null) $server->where('version',$version);
		$server = $server->get();
		
		for ($i=0, $loop=count($server);$i<$loop;$i++) {
			if ($isForce == true || $server[$i]->check_date < time() - 120) {
				$this->checkServer($server[$i]);
			}
		}
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'getSelectUser') {
			$service = Request('service');
			if ($service == 'BETA') {
				$lists = array(array(
					'usernum'=>2000,
					'html'=>'채널통합 <span class="fontBlue">2,000</span>명 (<i class="fa fa-rub"></i> <span class="fontRed">FREE</span>) - 베타서비스기간중에만 제공',
					'selected'=>true
				));
			} elseif ($service == 'FREE') {
				$lists = array(array(
					'usernum'=>100,
					'html'=>'채널통합 <span class="fontBlue">100</span>명 (<i class="fa fa-rub"></i> <span class="fontRed">FREE</span>) - 15일마다 무료기간연장필요',
					'selected'=>true
				));
			} else {
				$lists = $this->db()->select($this->table->price)->orderBy('usernum','asc')->get();
				for ($i=0, $loop=count($lists);$i<$loop;$i++) {
					$lists[$i]->html = '채널통합 <span class="fontBlue">'.number_format($lists[$i]->usernum).'</span>명 (<i class="fa fa-rub"></i> <span class="fontRed">'.number_format($lists[$i]->price).'</span>/1개월)'.($lists[$i]->discount != 0 ? ' - '.$lists[$i]->discount.'% 할인가' : '');
				}
			}
			
			$results->success = true;
			$results->lists = $lists;
		}
		
		if ($action == 'getSelectTime') {
			$service = Request('service');
			if ($service == 'BETA') {
				$lists = array(array(
					'time'=>180,
					'html'=>'베타서비스기간중 <span class="fontBlue">무제한</span> (<i class="fa fa-rub"></i> <span class="fontRed">FREE</span>) - 베타서비스기간중에만 제공',
					'selected'=>true
				));
			} elseif ($service == 'FREE') {
				$lists = array(array(
					'time'=>15,
					'html'=>'신청일로부터 <span class="fontBlue">15</span>일 (<i class="fa fa-rub"></i> <span class="fontRed">FREE</span>) - 15일마다 무료기간연장필요',
					'selected'=>true
				));
			} else {
				$lists = array(
					array(
						'time'=>30,
						'html'=>'신청일로부터 <span class="fontBlue">30</span>일'
					),
					array(
						'time'=>60,
						'html'=>'신청일로부터 <span class="fontBlue">60</span>일'
					),
					array(
						'time'=>90,
						'html'=>'신청일로부터 <span class="fontBlue">90</span>일 - 5% 할인'
					),
					array(
						'time'=>180,
						'html'=>'신청일로부터 <span class="fontBlue">180</span>일 - 10% 할인'
					)
				);
			}
			
			$results->success = true;
			$results->lists = $lists;
		}
		
		if ($action == 'getPrice') {
			$idx = Request('idx');
			$type = Request('type');
			$service = Request('service');
			
			$price = new stdClass();
			$price->refund = 0;
			
			if ($idx) {
				$data = $this->db()->select($this->table->hosting)->where('idx',$idx)->getOne();
				if ($data != null && $data->midx == $this->IM->getModule('member')->getLogged()) {
					$price->refund = $this->getRefundPrice($data);
				}
			}
			
			if ($service == 'BETA' || $service == 'FREE') {
				$price->monthly = 0;
				$price->time = $service == 'BETA' ? 180 : 15;
				$price->price = 0;
				$price->discount_time = 0;
				$price->discount_member = 0;
				$price->total = 0;
				
				$results->success = true;
				$results->price = $price;
			} elseif ($service == 'PAID') {
				$maxuser = Request('maxuser');
				$time = intval(Request('time'));
				$monthly = $this->db()->select($this->table->price)->where('usernum',$maxuser)->getOne();
				
				if ($monthly == null || in_array($time,array(30,60,90,180)) == false) {
					$results->success = false;
				} else {
					$timeDiscountRate = 0;
					$timeDiscountRate = $time == 90 ? 5 : $timeDiscountRate;
					$timeDiscountRate = $time == 180 ? 10 : $timeDiscountRate;
					
					$memberDiscountRate = $this->IM->getModule('member')->getMember() == null ? 0 : floor($this->IM->getModule('member')->getMember()->level->level / 2);
					
					$price->monthly = $monthly->price;
					$price->time = $time;
					$price->price = $price->monthly * ($time / 30) - $price->refund;
					if ($price->price > 0) {
						$price->discount_time = floor($price->price * $timeDiscountRate / 100);
						$price->discount_member = floor(($price->price - $price->discount_time) * $memberDiscountRate / 100);
					} else {
						$price->discount_time = 0;
						$price->discount_member = 0;
					}
					$price->total = $price->price - $price->discount_time - $price->discount_member;
				
					$results->success = true;
					$results->price = $price;
				}
			}
		}
		
		if ($action == 'getExpireDate') {
			$time = Request('time');
			$results->success = true;
			$results->expire_date = $time ? date('Y년 m월 d일 H시 i분 s초',strtotime(date('Y-m-d 23:59:59',time() + 60 * 60 * 24 * $time))).' KST' : '신청기간을 선택하시면 예상만료일이 계산됩니다.';
		}
		
		if ($action == 'getMyHosting') {
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = '먼저 로그인을 하여주십시오.';
			} else {
				$lists = $this->db()->select($this->table->hosting)->where('midx',$this->IM->getModule('member')->getLogged())->get();
				if (count($lists) == 0) {
					$results->success = false;
					$results->message = '회원님의 신청내역이 없습니다. 신규로 신청하여 주시기 바랍니다.';
				} else {
					$results->success = true;
					$results->lists = $lists;
				}
			}
		}
		
		if ($action == 'getService') {
			$idx = Request('idx');
			$data = $this->db()->select($this->table->hosting)->where('idx',$idx)->getOne();
			
			if ($data == null || $data->midx != $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = '해당 서비스신청내역을 찾을 수 없습니다.';
			} else {
				$results->success = true;
				$results->data = $data;
			}
		}
		
		if ($action == 'hostingSubmit') {
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = '먼저 로그인을 하여주십시오.';
			} else {
				$isError = false;
				
				$idx = Request('idx');
				$title = Request('title') ? Request('title') : 'SERVICE #'.date('Ymd');
				$type = in_array(Request('type'),array('NEW','EXTEND')) == true ? Request('type') : $isError = true;
				$service = in_array(Request('service'),array('BETA','FREE','PAID')) == true ? Request('service') : $isError = true;
				$maxuser = in_array(Request('maxuser'),array(50,100,200,300,500,1000,2000)) == true ? Request('maxuser') : $isError = true;
				$time = in_array(Request('time'),array(15,30,60,90,180)) == true ? Request('time') : $isError = true;
				
				if ($service == 'PAID') {
					$price = $this->db()->select($this->table->price)->where('usernum',$maxuser)->getOne()->price;
					$price = $price * ($time / 30);
				} else {
					$price = 0;
				}
				
				$refund = 0;
				if ($idx) {
					$data = $this->db()->select($this->table->hosting)->where('idx',$idx)->getOne();
					if ($data != null && $data->midx == $this->IM->getModule('member')->getLogged()) {
						$refund = floor($data->price / floor(($data->expire_date - $data->reg_date) / 60 / 60 / 24)) * floor(($data->expire_date - time()) / 60 / 60 / 24);
						$refund = $refund > 0 ? $refund : 0;
					} else {
						$isError = true;
					}
				}
				
				if ($isError == false) {
					$paid = $this->getDiscountPrice($price - $refund,$time);
					$price = $this->getDiscountPrice($price,$time);
					
					$member = $this->IM->getModule('member')->getMember();
					if ($paid <= 0 || $member->point >= $paid) {
						if ($idx) {
							$this->db()->update($this->table->hosting,array('service'=>$service,'maxuser'=>$maxuser,'price'=>$price,'reg_date'=>time(),'expire_date'=>strtotime(date('Y-m-d 23:59:59',time() + 60* 60 * 24 * $time))))->execute();
							
							$this->IM->getModule('member')->sendPoint($member->idx,$paid * -1,'minitalk','change',array('idx'=>$idx),true);
						} else {
							$idx = $this->db()->insert($this->table->hosting,array('midx'=>$member->idx,'service'=>$service,'title'=>$title,'client_id'=>strtoupper(md5($member->idx.time())),'maxuser'=>$maxuser,'price'=>$price,'reg_date'=>time(),'expire_date'=>strtotime(date('Y-m-d 23:59:59',time() + 60* 60 * 24 * $time))))->execute();
							
							$this->IM->getModule('member')->sendPoint($member->idx,$paid * -1,'minitalk','apply',array('idx'=>$idx),true);
						}
						
						$results->success = true;
					} else {
						$results->success = false;
						$results->message = '포인트가 부족합니다.';
					}
				} else {
					$results->success = false;
					$results->message = '신청내역에 문제가 있습니다.';
				}
			}
		}
		
		if ($action == 'getServerList') {
			$results->success = true;
			$results->html = $this->getServerList();
		}
		
		if ($action == 'getMyHosting') {
			$results->success = true;
			$results->html = $this->getMyHosting();
		}
		
		if ($action == 'disconnect') {
			$idx = Request('idx');
			$confirm = Request('confirm') == 'TRUE';
			
			$service = $this->db()->select($this->table->hosting)->where('idx',$idx)->getOne();
			
			if ($service == null || $service->midx != $this->IM->getModule('member')->getLogged()) {
				$results->success = false;
				$results->message = '권한이 없습니다. 먼저 로그인을 하여주시기 바랍니다.';
			} elseif ($confirm == true) {
				if ($service->server_id == '') {
					$refund = $this->getRefundPrice($service);
					$this->IM->getModule('member')->sendPoint($service->midx,$refund,'minitalk','refund',array(),true);
					$this->db()->delete($this->table->hosting)->where('idx',$idx)->execute();
					$results->success = true;
					$results->message ='성공적으로 서비스를 삭제하였습니다.';
				} else {
					$this->db()->update($this->table->hosting,array('server_id'=>''))->where('idx',$idx)->execute();
					$results->success = true;
					$results->message = '성공적으로 클라이언트 연결을 해제하였습니다.';
				}
			} else {
				$results->success = true;
				$results->modalHtml = $this->getDisconnect($service);
			}
		}
		
		if ($action == 'service') {
			$protocol = Request('protocol');
			$version = Request('version');
			$data = json_decode(Request('data'));

			if (version_compare($version,'7.0.0','>=') == true) {
				if ($protocol == 'checkServer' || $protocol == 'connectServer') {
					$client_id = $data->client_id;
					$server_id = strtoupper(md5($data->key.$data->domain));
					$isForce = isset($data->isForce) == true && $data->isForce == true;
					
					$hosting = $this->db()->select($this->table->hosting)->where('client_id',$client_id)->getOne();
					if ($hosting == null || $hosting->server_id != $server_id) {
						$results->success = false;
						$results->error = 202;
					} elseif ($hosting->expire_date < time()) {
						$results->success = false;
						$results->error = 203;
					} else {
						$tempVersion = explode('.',$version);
						$serverVersion = $tempVersion[0].'.'.$tempVersion[1];
						
						$this->updateServer($serverVersion);
						
						if ($hosting->server == 0 || $this->checkOnline($hosting->server) == false) {
							$server = $this->db()->select($this->table->server)->where('version',$serverVersion)->orderBy('user','asc')->where('status','ONLINE')->getOne();
							if ($server != null) {
								$hosting->server = $server->idx;
								$this->db()->update($this->table->hosting,array('server'=>$server->idx))->where('idx',$hosting->idx)->execute();
							} else {
								$hosting->server = 0;
								$this->db()->update($this->table->hosting,array('server'=>0))->where('idx',$hosting->idx)->execute();
							}
						}
						
						if ($hosting->server == 0) {
							$results->success= false;
							$results->error = 201;
						} else {
							$server = $this->db()->select($this->table->server)->where('idx',$hosting->server)->getOne();
							
							$results->success = true;
							if ($protocol == 'connectServer') {
								$results->domain = $server->is_ssl == 'TRUE' ? 'https://'.$server->domain.':'.$server->port : 'http://'.$server->domain.':'.$server->port;
								$results->secure = $server->is_ssl == 'TRUE';
								$results->serverCode = Encoder(json_encode(array('group'=>$hosting->idx,'maxuser'=>$hosting->maxuser,'ip'=>$data->ip,'time'=>time())),'com.arzz.program.kr.minitalk.www');
								$results->channelCode = Encoder(json_encode(array('maxuser'=>$data->maxuser,'ip'=>$data->ip,'time'=>time())),'com.arzz.program.kr.minitalk.www');
							
								if ($data->opperCode && Decoder($data->opperCode,$data->key) !== false) $results->opperCode = Encoder(Decoder($data->opperCode,$data->key),'com.arzz.program.kr.minitalk.www');
								else $results->opperCode = null;
							} else {
								$results->user = 0;
								$results->channel = 0;
								$results->status = 'ONLINE';
							}
						}
					}
				}
			} elseif (version_compare($version,'6.2.0','>=') == true) {
				if ($protocol == 'callback') {
					$d = json_decode(Request('d'));
					
					if ($d->action == 'save_channel') {
						$sIdx = preg_replace('/H0+/','',$d->code);
						$check = $this->db()->select($this->table->hosting)->where('idx',$sIdx)->getOne();
						
						if ($check != null) {
							$curlsession = curl_init();
							curl_setopt($curlsession,CURLOPT_URL,$check->callback);
							curl_setopt($curlsession,CURLOPT_POST,1);
							curl_setopt($curlsession,CURLOPT_POSTFIELDS,array('action'=>'save_channel','mcode'=>$check->client_id,'list'=>json_encode($d->list)));
							curl_setopt($curlsession,CURLOPT_TIMEOUT,10);
							curl_setopt($curlsession,CURLOPT_RETURNTRANSFER,1);
							$buffer = curl_exec($curlsession);
							curl_close($curlsession);
							
							if ($buffer) exit(json_encode(json_decode($buffer,true)));
							else exit(json_encode(array('success'=>false)));
						} else {
							exit(json_encode(array('success'=>false)));
						}
					}
					
					if ($d->action == 'banip') {
						$sIdx = preg_replace('/H0+/','',$d->code);
						$check = $this->db()->select($this->table->hosting)->where('idx',$sIdx)->getOne();
						$memo = 'from '.$d->from;
						
						if ($check != null) {
							$curlsession = curl_init();
							curl_setopt($curlsession,CURLOPT_URL,$check->callback);
							curl_setopt($curlsession,CURLOPT_POST,1);
							curl_setopt($curlsession,CURLOPT_POSTFIELDS,array('action'=>'banip','mcode'=>$check->client_id,'ip'=>$d->ip,'memo'=>$memo));
							curl_setopt($curlsession,CURLOPT_TIMEOUT,10);
							curl_setopt($curlsession,CURLOPT_RETURNTRANSFER,1);
							$buffer = curl_exec($curlsession);
							curl_close($curlsession);
							
							if ($buffer) exit(json_encode(json_decode($buffer,true)));
							else exit(json_encode(array('success'=>false)));
						} else {
							exit(json_encode(array('success'=>false)));
						}
					}
				}
				
				if ($protocol == 'register_server') {
					$email = $data->user_id;
					$password = $data->password;
					$client_id = $data->mcode;
					$server_id = strtoupper($data->scode);
					$dbpath = $data->dbpath;
					
					$midx = $this->IM->getModule('member')->isValidate($email,$password);
					if ($midx !== false) {
						$service = $this->db()->select($this->table->hosting)->where('midx',$midx)->where('client_id',$client_id)->getOne();
						if ($service == null) {
							$results->success = false;
							$results->message = '등록되어 있는 접속키가 아닙니다.<br />접속키를 한번더 확인하여 주십시오.';
						} elseif ($service->server_id && $service->server_id != $server_id) {
							$results->success = false;
							$results->message = '이미 다른 미니톡과 연동되어 있는 접속키입니다.<br />다른 미니톡클라이언트와 이미 연동이 되어있거나, 연동 후 미니톡클라이언트의 접속주소가 변경된 경우입니다.<br />미니톡 홈페이지에서 해당 접속키 연동정보를 초기화한 뒤 다시 시도하여 주십시오.';
						} else {
							$this->db()->update($this->table->hosting,array('server_id'=>$server_id,'callback'=>$dbpath,'check_date'=>time()))->where('idx',$service->idx)->execute();
							$results->success = true;
							$results->mcode = $client_id;
						}
					} else {
						$results->success = false;
						$results->message = '로그인에 실패하였습니다.<br />미니톡 홈페이지의 이메일주소와 패스워드를 정확히 입력하여 주십시오.';
					}
				}
				
				if ($protocol == 'server_info') {
					$client_id = strtoupper($data->mcode);
					$server_id = strtoupper($data->scode);
					
					$service = $this->db()->select($this->table->hosting)->where('client_id',$client_id)->where('server_id',$server_id)->getOne();
					if ($service == null) {
						$results->success = false;
					} else {
						$results->success = true;
						$results->user = $service->user;
						$results->channel = $service->channel;
						$results->maxuser = $service->maxuser;
						$results->expire_time = $service->expire_date < time() ? '' : date('Y-m-d H:i:s',$service->expire_date).'(KST)';
		
						if ($service->server == '0') {
							$results->check_time = date('Y-m-d H:i:s').'(KST)';
						} else {
							$server = $this->db()->select($this->table->server)->where('idx',$service->server)->getOne();
							$results->check_time = date('Y-m-d H:i:s',$server->check_date).'(KST)';
						}
						$results->status = $this->db()->select($this->table->server)->where('status','ONLINE')->where('is_select','TRUE')->count() > 0 ? 'ONLINE' : 'OFFLINE';
						$results->auth = $service->server_id == $server_id;
					}
				}
				
				if ($protocol == 'check_server') {
					$client_id = strtoupper($data->mcode);
					$server_id = strtoupper($data->scode);
					$key = $data->key;
					$ip = $data->ip;
					$tempVersion = explode('.',$version);
					$serverVersion = $tempVersion[0].'.'.$tempVersion[1];
					
					$service = $this->db()->select($this->table->hosting)->where('client_id',$client_id)->where('server_id',$server_id)->getOne();
					if ($service == null) {
						$results->success = false;
					} else {
						$server = null;
						if ($service->server == 0) {
							$servers = $this->db()->select($this->table->server)->where('status','ONLINE')->where('is_select','TRUE')->where('version',$serverVersion)->orderBy('user','asc')->get();
							for ($i=0, $loop=count($servers);$i<$loop;$i++) {
								if ($this->isServerOnline($servers[$i]->idx) == true) {
									$server = $servers[$i];
									break;
								}
							}
							
							if ($server !== null) {
								$this->db()->update($this->table->hosting,array('server'=>$server->idx))->where('idx',$service->idx)->execute();
							}
						} else {
							if ($this->isServerOnline($service->server) == true) {
								$server = $this->db()->select($this->table->server)->where('idx',$service->server)->getOne();
							} else {
								$this->db()->update($this->table->hosting,array('server'=>0,'user'=>0,'channel'=>0))->where('idx',$service->idx)->execute();
								
								$servers = $this->db()->select($this->table->server)->where('status','ONLINE')->where('version',$serverVersion)->orderBy('user','asc')->get();
								for ($i=0, $loop=count($servers);$i<$loop;$i++) {
									if ($this->isServerOnline($servers[$i]->idx) == true) {
										$server = $servers[$i];
										break;
									}
								}
								
								if ($server !== null) {
									$this->db()->update($this->table->hosting)->where('server',$server->idx)->where('idx',$service->idx)->execute();
								}
							}
						}
						
						if ($server !== null) {
							$server->channelCode = 'H'.sprintf('%09d',$service->idx);
							$server->serverCode = urlencode(Encoder(json_encode(
								array(
									'ip'=>$ip,
									'expire_time'=>$service->expire_date,
									'maxuser'=>$service->maxuser,
									'key'=>$key
								)
							),'com.arzz.program.kr.minitalk.www'));
							
							$results->success = true;
							unset($server->user,$server->channel,$server->status,$server->check_date);
							$results->server = $server;
						} else {
							$results->success = false;
						}
					}
				}
			}
		}
		
		return $results;
	}
}
?>