<?php
class ModuleKeyword {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $boards = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->keyword = 'keyword_table';

		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/keyword.js');
		/*
		$o = $this->db()->select('imodule_keyword_table')->get();
		for ($i=0, $loop=sizeof($o);$i<$loop;$i++) {
			$this->db()->insert($this->table->keyword,array('keyword'=>$o[$i]->keyword,'keycode'=>$o[$i]->keycode,'engcode'=>$o[$i]->engcode,'hit'=>$o[$i]->hit,'last_search'=>$o[$i]->last_search))->execute();
		}
		*/
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
	
	function getEngcode($str) {
		$arr_kor = array('ㄱ','ㄲ','ㄴ','ㄷ','ㄹ','ㅁ','ㅂ','ㅃ','ㅅ','ㅆ','ㅇ','ㅈ','ㅉ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ','ㄳ','ㄵ','ㄶ','ㄺ','ㄻ','ㄼ','ㄽ','ㄾ','ㄿ','ㅀ','ㅄ','ㅏ','ㅐ','ㅑ','ㅒ','ㅓ','ㅔ','ㅕ','ㅖ','ㅗ','ㅘ','ㅙ','ㅚ','ㅛ','ㅜ','ㅝ','ㅞ','ㅟ','ㅠ','ㅡ','ㅢ','ㅣ');

		$arr_eng = array('r','R','s','e','f','a','q','Q','t','T','d','w','W','c','z','x','v','g','rt','sw','sg','fr','fa','fq','ft','fx','fv','fg','qt','k','o','i','O','j','p','u','P','h','hk','ho','hl','y','n','nj','np','nl','u','m','ml','l');

		$engcode = str_replace($arr_kor,$arr_eng,$str);

		return $engcode;
	}

	function getKeycode($str) {
		$arr_cho = array('ㄱ','ㄲ','ㄴ','ㄷ','ㄸ','ㄹ','ㅁ','ㅂ','ㅃ','ㅅ','ㅆ','ㅇ','ㅈ','ㅉ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');
		$arr_jung = array('ㅏ','ㅐ','ㅑ','ㅒ','ㅓ','ㅔ','ㅕ','ㅖ','ㅗ','ㅘ','ㅙ','ㅚ','ㅛ','ㅜ','ㅝ','ㅞ','ㅟ','ㅠ','ㅡ','ㅢ','ㅣ');
		$arr_jong = array('','ㄱ','ㄲ','ㄳ','ㄴ','ㄵ','ㄶ','ㄷ','ㄹ','ㄺ','ㄻ','ㄼ','ㄽ','ㄾ','ㄿ','ㅀ','ㅁ','ㅂ','ㅄ','ㅅ','ㅆ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');

		$unicode = array();
		$values = array();
		$lookingFor = 1;

		for ($i=0, $loop=strlen($str);$i<$loop;$i++) {
			$thisValue = ord($str[$i]);

			if ($thisValue < 128) {
				$unicode[] = $thisValue;
			} else {
				if (count($values)==0) $lookingFor = $thisValue < 224 ? 2 : 3;
				$values[] = $thisValue;
				if (count($values) == $lookingFor) {
					$number = $lookingFor == 3 ? (($values[0]%16)*4096)+(($values[1]%64)*64)+($values[2]%64) : (($values[0]%32)*64)+($values[1]%64);
					$unicode[] = $number;
					$values = array();
					$lookingFor = 1;
				}
			}
		}

		$splitStr = '';
		while (list($key,$code) = each($unicode)) {
			if ($code >= 44032 && $code <= 55203) {
				$temp = $code-44032;
				$cho = (int)($temp/21/28);
				$jung = (int)(($temp%(21*28)/28));
				$jong = (int)($temp%28);

				$splitStr.= $arr_cho[$cho].$arr_jung[$jung].$arr_jong[$jong];
			} else {
				$temp = array($unicode[$key]);

				foreach ($temp as $ununicode) {
					if ($ununicode < 128) {
						$splitStr.= chr($ununicode);
					} elseif ($ununicode < 2048) {
						$splitStr.= chr(192+(($ununicode-($ununicode%64))/64));
						$splitStr.= chr(128+($ununicode%64));
					} else {
						$splitStr.= chr(224+(($ununicode-($ununicode%4096))/4096));
						$splitStr.= chr(128+((($ununicode%4096)-($ununicode%64))/64));
						$splitStr.= chr(128+($ununicode%64));
					}
				}
			}
		}

		$splitStr = str_replace(' ','',$splitStr);
		return $splitStr;
	}
	
	function doProcess($action) {
		$result = array();
		
		if ($action == 'livesearch') {
			$keyword = Request('keyword');
			
			if ($keyword != null && strlen($keyword) > 0) {
				$keycode = $this->getKeycode($keyword);
				$engcode = $this->getEngcode($keycode);
				$keywords = $this->db()->select($this->table->keyword,'keyword')->where('keycode',$keycode.'%','LIKE')->orWhere('engcode',$engcode.'%','LIKE')->orderBy('hit','asc')->limit(50)->get();
				
				$result['success'] = true;
				$result['keywords'] = $keywords;
			}
		}
		
		return $result;
	}
}
?>