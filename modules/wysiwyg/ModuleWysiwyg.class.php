<?php
class ModuleWysiwyg {
	private $IM;
	private $Module;
	
	private $lang = null;
	
	private $_attachment = null;
	private $_id = null;
	private $_name = null;
	private $_content = null;
	private $_required = false;
	private $_theme = 'white';
	private $_height = 400;
	private $_hideButtons = array();
	private $_toolBarFixed = true;
	private $_uploader = true;
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->_attachment = $this->IM->getModule('attachment');
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
	
	function reset() {
		$this->_id = null;
		$this->_name = null;
		$this->_content = null;
		$this->_required = false;
		$this->_theme = 'white';
		$this->_hideButtons = array();
		$this->_uploader = true;
	}
	
	function setId($id) {
		$this->_id = $id;
		
		return $this;
	}
	
	function setName($name) {
		$this->_name = $name;
		$this->_attachment->setWysiwyg($name);
		
		return $this;
	}
	
	function setModule($module) {
		$this->_attachment->setModule($module);
		
		return $this;
	}
	
	function loadFile($files=array()) {
		$this->_attachment->loadFile($files);
		
		return $this;
	}
	
	function setContent($content) {
		$this->_content = $content;
		
		return $this;
	}
	
	function setRequired($required) {
		$this->_required = $required;
		
		return $this;
	}
	
	function setTheme($theme) {
		$this->_theme = $theme;
		
		return $this;
	}
	
	function getAttachment() {
		return $this->_attachment;
	}
	
	function setHeight($height) {
		$this->_height = $height;
		
		return $this;
	}
	
	function setUploader($uploader) {
		$this->_uploader = $uploader;
		
		return $this;
	}
	
	function setHideButtons($hideButtons=array()) {
		$this->_hideButtons = $hideButtons;
		
		return $this;
	}
	
	function setToolBarFixed($toolbarFixed) {
		$this->_toolBarFixed = $toolbarFixed;
		
		return $this;
	}
	
	function preload() {
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/wysiwyg.js');
		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/'.$this->_theme.'.css');
		
		$this->getAttachment()->preload();
	}
	
	function doLayout() {
		$this->_id = $this->_id == null ? uniqid('wysiwyg-') : $this->_id;
		$this->_name = $this->_name == null ? 'content' : $this->_name;
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/wysiwyg.js');
		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/'.$this->_theme.'.css');
		
		$wysiwyg = '<textarea id="'.$this->_id.'" name="'.$this->_name.'" data-wysiwyg="true"'.($this->_required == true ? ' data-required="required"' : '').'>'.($this->_content !== null ? $this->_content : '').'</textarea>'.PHP_EOL;
		$wysiwyg.= $this->_buildScript();
		
		echo $wysiwyg;
		
		$this->_attachment->setId($this->_id.'-attachment');
		$this->_attachment->doLayout();
		
		$this->reset();
	}
	
	function _buildScript() {
		$script = '<script>$(document).ready(function() { $("#'.$this->_id.'").redactor({minHeight:'.$this->_height.',buttonsHide:'.json_encode($this->_hideButtons).',toolbarFixed:'.json_encode($this->_toolBarFixed).'}); });</script>'.PHP_EOL;
		
		return $script;
	}
}
?>