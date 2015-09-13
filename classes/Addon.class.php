<?php
class Addon {
	private $IM;
	private $addons = array();
	private $table;
	private $addonPath;
	private $addonDir;
	private $addonInfo;
	
	function __construct($IM) {
		if (is_object($IM) == true) {
			$this->IM = $IM;
			
			$this->table = new stdClass();
			$this->table->addon = 'addon_table';
			
			/* for beta only Start */
			$addonsPath = __IM_PATH__.'/addons';
			$addonsDir = opendir($addonsPath);
			while ($addon = readdir($addonsDir)) {
				if (is_dir($addonsPath.'/'.$addon) == true) {
					$hash = $this->getHash($addon);
					$target = json_encode($this->getTarget($addon));
					
					if ($hash == false) continue;
					
					if ($this->db()->select($this->table->addon)->where('addon',$addon)->has() == true) {
						$this->db()->update($this->table->addon,array('hash'=>$hash,'target'=>$target,'active'=>'TRUE'))->where('addon',$addon)->execute();
					} else {
						$this->db()->insert($this->table->addon,array('addon'=>$addon,'hash'=>$hash,'target'=>$target,'active'=>'TRUE'))->execute();
					}
				}
			}
			/* for beta only End */
			
			$addons = $this->db()->select($this->table->addon)->where('active','TRUE')->get();
			for ($i=0, $loop=sizeof($addons);$i<$loop;$i++) {
				$targets = json_decode($addons[$i]->target);
				
				foreach ($targets as $target=>$events) {
					foreach ($events as $event=>$callers) {
						foreach ($callers as $caller) {
							$this->IM->Event->addTarget($target,$event,$caller,'addon/'.$addons[$i]->addon);
						}
					}
				}
			}
		} else {
			$this->IM = null;
			$this->addonPath = __IM_PATH__.'/addons/'.$IM;
			$this->addonDir = __IM_DIR__.'/addons/'.$IM;
			$this->addonInfo = json_decode(file_get_contents($this->addonPath.'/'.$IM.'.json'));
		}
	}
	
	function db($db='default') {
		return $this->IM->db($db);
	}
	
	function getHash($addon) {
		return file_exists(__IM_PATH__.'/addons/'.$addon.'/'.$addon.'.json') == true ? md5_file(__IM_PATH__.'/addons/'.$addon.'/'.$addon.'.json') : false;
	}
	
	function getTarget($addon) {
		if (file_exists(__IM_PATH__.'/addons/'.$addon.'/'.$addon.'.json') == false) return false;
		
		$json = json_decode(file_get_contents(__IM_PATH__.'/addons/'.$addon.'/'.$addon.'.json'));
		return $json->target;
	}
	
	function getPath() {
		if ($this->IM !== null) return null;
		return $this->addonPath;
	}
	
	function getDir() {
		if ($this->IM !== null) return null;
		return $this->addonDir;
	}
	
	function getInfo() {
		if ($this->IM !== null) return null;
		return $this->addonInfo;
	}
	
	function getLanguage($code) {
		if ($this->IM !== null) return null;
		
		if (is_dir($this->addonPath.'/languages') == false) return null;
		
		if ($this->lang == null) {
			if (file_exists($this->addonPath.'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->addonPath.'/languages/'.$this->IM->language.'.json'));
			} else {
				$this->lang = json_decode(file_get_contents($this->addonPath.'/languages/'.$this->getInfo()->languages[0].'.json'));
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
}
?>