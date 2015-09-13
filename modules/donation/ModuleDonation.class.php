<?php
class ModuleDonation {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	
	public $fullPrice = 1000000;
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->list = 'donation_list_table';

		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/donation.js');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/jquery.animateNumber.min.js');
		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
	}
	
	function getApi($api) {
		$data = new stdClass();
		
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
		$fullPrice = $this->fullPrice;
		$prevMonth = date('Y-m',mktime(0,0,0,date('m')-1,1,date('Y')));
		$thisMonth = date('Y-m');

		$prevTotal = $this->getMonthTotal($prevMonth);
		$thisTotal = $this->getMonthTotal($thisMonth) + ($prevTotal > $fullPrice ? $prevTotal - $fullPrice : 0);

		$thisPercentage = $thisTotal / $fullPrice * 100 > 100 ? 100 : sprintf('%0.2f',$thisTotal / $fullPrice * 100);
		
		$info = new stdClass();
		$info->text = $thisPercentage.'%';
		$info->last_time = $this->db()->select($this->table->list)->where('status','WAIT')->count() > 0 ? time() : 0;
		
		return $info;
	}
	
	function getPush($code,$fromcode,$content) {
		$latest = array_pop($content);
		$count = count($content);
		
		$push = new stdClass();
		$push->image = null;
		$push->link = null;
		if ($count > 0) $push->content = $this->getLanguage('push/'.$code.'s');
		else $push->content = $this->getLanguage('push/'.$code);
		
		$data = $this->db()->select($this->table->list)->where('idx',$fromcode)->getOne();
		$push->image = $this->Module->getDir().'/images/push.png';
		if ($code == 'donate') {
			$push->content = str_replace(array('{point}','{exp}'),array('<b>'.number_format($data->gift_point).'</b>','<b>'.number_format($data->gift_exp).'</b>'),$push->content);
		} else {
			$push->content = str_replace(array('{date}','{price}'),array('<b>'.$data->reg_date.'</b>','<b>'.number_format($data->price).'</b>'),$push->content);
		}
		$push->link = $this->IM->getUrl('community','donation',false);
		
		return $push;
	}
	
	function getContext($view) {
		ob_start();
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div class="ModuleDonation">'.PHP_EOL;
		
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
	
	function getMonthTotal($month) {
		return $this->db()->select($this->table->list,array('sum(price) as total'))->where('reg_date',$month.'%','LIKE')->where('status','TRUE')->getOne()->total;
	}
	
	function getTotal() {
		return $this->db()->select($this->table->list,array('sum(price) as total'))->where('status','TRUE')->getOne()->total;
	}
	
	function getStartDate() {
		return strtotime($this->db()->select($this->table->list,array('min(reg_date) as start'))->getOne()->start);
	}
	
	function getList($page) {
		$lists = $this->db()->select($this->table->list);
		$total = $lists->copy()->count();
		$lists = $lists->limit(($page-1)*10,10)->orderBy('idx','desc')->get();
		
		for ($i=0, $loop=count($lists);$i<$loop;$i++) {
			$lists[$i]->name = $this->IM->getModule('member')->getMemberNickname($lists[$i]->midx,true,$lists[$i]->name);
			if ($lists[$i]->is_secret == 'TRUE') $lists[$i]->name = false;
		}
		
		$pagination = GetPagination($page,ceil($total/10),7,'LEFT','@Donation.getList');
		
		ob_start();
		
		$IM = $this->IM;
		$Module = $this;
		
		echo '<div id="ModuleDonationList">'.PHP_EOL;
		
		if (file_exists($this->Module->getPath().'/views/'.$this->IM->language.'/list.php') == true) {
			INCLUDE $this->Module->getPath().'/views/'.$this->IM->language.'/list.php';
		} else {
			INCLUDE $this->Module->getPath().'/views/ko/list.php';
		}
		
		
		
		echo '</div>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function getShow($data) {
		ob_start();
		
		echo '<form id="ModuleDonationShowForm" onsubmit="return Donation.confirm(this);">'.PHP_EOL;
		echo '<input type="hidden" name="idx" value="'.$data->idx.'">'.PHP_EOL;
		
		$title = '후원내역 처리하기';
		
		$content = '';
		
		$content.= '<div class="label">입금방법</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '	<input type="hidden" name="intype" value="'.$data->intype.'">'.PHP_EOL;
		$content.= '	<div class="selectControl" data-field="intype">'.PHP_EOL;
		$content.= '		<button type="button">입금하신 계좌를 선택하여 주십시오. <span class="arrow"></span></button>'.PHP_EOL;
		$content.= '		<ul></ul>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$content.= '<div class="blankSpace"></div>'.PHP_EOL;
		
		$content.= '<div class="row">'.PHP_EOL;
		$content.= '	<div class="col-xs-6">'.PHP_EOL;
		$content.= '		<div class="label">입금자명</div>'.PHP_EOL;
		$content.= '		<div class="inputBlock">'.PHP_EOL;
		$content.= '			<input type="text" name="name" class="inputControl" value="'.GetString($data->name,'inputbox').'">'.PHP_EOL;
		$content.= '		</div>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '	<div class="col-xs-6">'.PHP_EOL;
		$content.= '		<div class="label">입금날짜</div>'.PHP_EOL;
		$content.= '		<div class="inputBlock">'.PHP_EOL;
		$content.= '			<input type="text" name="reg_date" class="inputControl" value="'.$data->reg_date.'">'.PHP_EOL;
		$content.= '		</div>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$content.= '<div class="blankSpace"></div>'.PHP_EOL;
		
		$content.= '<div class="label">입금금액</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '	<input type="text" name="price" class="inputControl" data-type="number" value="'.number_format($data->price).'">'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$content.= '<div class="blankSpace"></div>'.PHP_EOL;
		
		$content.= '<div class="label">상태</div>'.PHP_EOL;
		$content.= '<div class="inputBlock">'.PHP_EOL;
		$content.= '	<input type="hidden" name="status" value="'.$data->status.'">'.PHP_EOL;
		$content.= '	<div class="selectControl" data-field="status">'.PHP_EOL;
		$content.= '		<button type="button"></button>'.PHP_EOL;
		$content.= '		<ul><li data-value="TRUE">처리완료</li><li data-value="FALSE">확인불가</li><li data-value="WAIT">대기중</li></ul>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$content.= '<div class="blankSpace"></div>'.PHP_EOL;
		
		$content.= '<div class="row">'.PHP_EOL;
		$content.= '	<div class="col-xs-6">'.PHP_EOL;
		$content.= '		<div class="label">혜택포인트</div>'.PHP_EOL;
		$content.= '		<div class="inputBlock">'.PHP_EOL;
		$content.= '			<input type="text" name="gift_point" class="inputControl" data-type="number" value="'.number_format($data->status == 'WAIT' ? $data->price : $data->gift_point).'">'.PHP_EOL;
		$content.= '		</div>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '	<div class="col-xs-6">'.PHP_EOL;
		$content.= '		<div class="label">혜택경험치</div>'.PHP_EOL;
		$content.= '		<div class="inputBlock">'.PHP_EOL;
		$content.= '			<input type="text" name="gift_exp" class="inputControl" data-type="number" value="'.number_format($data->status == 'WAIT' ? floor($data->price*0.02) : $data->gift_exp).'">'.PHP_EOL;
		$content.= '		</div>'.PHP_EOL;
		$content.= '	</div>'.PHP_EOL;
		$content.= '</div>'.PHP_EOL;
		
		$IM = $this->IM;
		$Module = $this;
		
		INCLUDE $this->Module->getPath().'/views/modal.php';
		
		echo '</form>'.PHP_EOL;
		echo '<script>Donation.showInit();</script>';
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $context;
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'submit') {
			$errors = array();
			$intype = in_array(Request('intype'),array('기업은행','씨티은행')) == true ? Request('intype') : $errors['intype'] = true;
			$name = Request('name') ? Request('name') : $errors['name'] = true;
			$price = preg_match('/^[1-9]+[0-9]*$/',Request('price')) == true ? Request('price') : $errors['price'] =  true;
			$reg_date = date('Y-m-d');
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif (count($errors) == 0) {
				$insert = array();
				$insert['name'] = $name;
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['price'] = $price;
				$insert['status'] = 'WAIT';
				$insert['intype'] = $intype;
				$insert['reg_date'] = $reg_date;
				$insert['is_secret'] = 'TRUE';
				
				$this->db()->insert($this->table->list,$insert)->execute();
				$results->success = true;
				$results->message = $this->getLanguage('success');
			} else {
				$results->success = false;
				$results->message = $this->getLanguage('error/required');
				$results->errors = $errors;
			}
		}
		
		if ($action == 'getList') {
			$page = Request('page');
			
			$results->success = true;
			$results->html = $this->getList($page);
		}
		
		if ($action == 'show') {
			$idx = Request('idx');
			$data = $this->db()->select($this->table->list)->where('idx',$idx)->getOne();
			
			if ($data == null) {
				$results->success = false;
				$results->message = '데이터를 찾을 수 없습니다.';
			} elseif ($this->IM->getModule('member')->getLogged() === 1) {
				$results->success = true;
				$results->modalHtml = $this->getShow($data);
			} else {
				$results->success = false;
				$results->message = '권한이 없습니다.';
			}
		}
		
		if ($action == 'confirm') {
			$idx = Request('idx');
			$data = $this->db()->select($this->table->list)->where('idx',$idx)->getOne();
			
			if ($data == null) {
				$results->success = false;
				$results->message = '데이터를 찾을 수 없습니다.';
			} elseif ($this->IM->getModule('member')->getLogged() === 1) {
				if ($data->status == 'TRUE') {
					$results->success = true;
				} else {
					$status = Request('status');
					if ($status == 'TRUE' && $data->status != 'TRUE') {
						$intype = Request('intype');
						$name = Request('name');
						$reg_date = Request('reg_date');
						$price = str_replace(',','',Request('price'));
						$gift_point = str_replace(',','',Request('gift_point'));
						$gift_exp = str_replace(',','',Request('gift_exp'));
						
						$this->IM->getModule('member')->sendPoint($data->midx,$gift_point,'donation','donate',array('idx'=>$idx));
						$this->IM->getModule('member')->addActivity($data->midx,$gift_exp,'donation','donate',array('idx'=>$idx));
						$this->IM->getModule('push')->sendPush($data->midx,'donation','donate',$idx);
						
						$this->db()->update($this->table->list,array('intype'=>$intype,'name'=>$name,'reg_date'=>$reg_date,'price'=>$price,'gift_point'=>$gift_point,'gift_exp'=>$gift_exp,'status'=>$status))->where('idx',$idx)->execute();
						
						$results->success = true;
						$results->message = '성공적으로 처리되었습니다.';
					} else {
						$this->db()->update($this->table->list,array('status'=>$status))->where('idx',$idx)->execute();
						
						if ($status != $data->status) {
							$this->IM->getModule('push')->sendPush($data->midx,'donation','cancel',$idx);
						}
						
						$results->success = true;
						$results->message = '성공적으로 처리되었습니다.';
					}
				}
			} else {
				$results->success = false;
				$results->message = '권한이 없습니다.';
			}
		}
		
		return $results;
	}
}
?>