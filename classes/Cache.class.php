<?php
class Cache {
	private $IM;
	private $cachePath;
	
	function __construct($IM) {
		$this->IM = $IM;
		$this->cachePath = $IM->getAttachmentPath().'/cache';
		
		if (is_dir($this->cachePath) == false) {
			mkdir($this->cachePath);
			chmod($this->cachePath,0707);
		}
	}
	
	function check($controller,$component,$code) {
		if (file_exists($this->cachePath.'/'.$controller.'.'.$component.'.'.$code.'.'.$this->IM->domain.'.'.$this->IM->language.'.cache') == true) {
			return filemtime($this->cachePath.'/'.$controller.'.'.$component.'.'.$code.'.'.$this->IM->domain.'.'.$this->IM->language.'.cache');
		} else {
			return 0;
		}
	}
	
	function get($controller,$component,$code) {
		if (file_exists($this->cachePath.'/'.$controller.'.'.$component.'.'.$code.'.'.$this->IM->domain.'.'.$this->IM->language.'.cache') == true) {
			return file_get_contents($this->cachePath.'/'.$controller.'.'.$component.'.'.$code.'.'.$this->IM->domain.'.'.$this->IM->language.'.cache');
		} else {
			return null;
		}
	}
	
	function store($controller,$component,$code,$data) {
		return file_put_contents($this->cachePath.'/'.$controller.'.'.$component.'.'.$code.'.'.$this->IM->domain.'.'.$this->IM->language.'.cache',$data);
	}
}
?>