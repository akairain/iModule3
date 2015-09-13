<?php
class ModulePush {
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
		$this->table->push = 'push_table';
		$this->table->config = 'push_config_table';

//		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/push.js');
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
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
	
	function getPushCount($type='ALL') {
		$check = $this->db()->select($this->table->push)->where('midx',$this->IM->getModule('member')->getLogged());
		if ($type == 'UNCHECK') $check->where('is_check','FALSE');
		elseif ($type == 'UNREAD') $check->where('is_read','FALSE');
		
		return $check->count();
	}
	
	function sendPush($target,$module,$code,$fromcode,$content=array()) {
		if (is_numeric($target) == true) { // for member
			$check = $this->db()->select($this->table->push)->where('midx',$target)->where('module',$module)->where('code',$code)->where('fromcode',$fromcode)->getOne();
			if ($check == null || $check->is_read == 'TRUE') {
				$this->db()->insert($this->table->push,array('midx'=>$target,'module'=>$module,'code'=>$code,'fromcode'=>$fromcode,'content'=>json_encode(array($content),JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK),'reg_date'=>time()))->execute();
			} else {
				$prevContent = json_decode($check->content);
				$prevContent[] = $content;
				$this->db()->update($this->table->push,array('content'=>json_encode($prevContent,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK),'reg_date'=>time()))->where('midx',$target)->where('module',$module)->where('code',$code)->where('fromcode',$fromcode)->execute();
			}
		} else { // for email
			
		}
	}
	
	function sendServer($channel,$data) {
		$ELEPHANTIO_PATH = $this->Module->getPath().'/classes/elephant.io/src';
		
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Client.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/AbstractPayload.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/EngineInterface.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Engine/AbstractSocketIO.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Engine/SocketIO/Session.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Engine/SocketIO/Version1X.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Exception/MalformedUrlException.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Exception/ServerConnectionFailureException.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Exception/SocketException.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Exception/UnsupportedActionException.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Exception/UnsupportedTransportException.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Payload/Decoder.php';
		REQUIRE_ONCE $ELEPHANTIO_PATH.'/Payload/Encoder.php';
		
		$EIO = new ElephantIO\Client(new ElephantIO\Engine\SocketIO\Version1X('http://127.0.0.1:3000',['timeout'=>5]));
		$EIO->initialize();
		$EIO->emit('push',array($channel,$data));
		$EIO->close();
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'recently') {
			$count = Request('count');
			$lists = $this->db()->select($this->table->push)->where('midx',$this->IM->getModule('member')->getLogged())->orderBy('reg_date','desc')->limit($count)->get();
			
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$module = $this->IM->getModule($lists[$i]->module);
				$content = $lists[$i]->midx.'/'.$lists[$i]->module.'/'.$lists[$i]->code.'/'.$lists[$i]->fromcode.'/'.$lists[$i]->content;
				$lists[$i]->image = null;
				$lists[$i]->link = null;
				if (method_exists($module,'getPush') == true) {
					$push = $module->getPush($lists[$i]->code,$lists[$i]->fromcode,json_decode($lists[$i]->content));
					$lists[$i]->image = $push->image;
					$lists[$i]->link = $push->link;
					$lists[$i]->content = $push->link == null ? $content.'/'.$push->content : $push->content;
				} else {
					$lists[$i]->content = $content;
				}
				$lists[$i]->is_read = $lists[$i]->is_read == 'TRUE';
			}
			
			$results->success = true;
			$results->lists = $lists;
		}
		
		$this->IM->fireEvent('afterDoProcess','push',$action,$values,$results);
		
		return $results;
	}
}
?>