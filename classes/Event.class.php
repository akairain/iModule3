<?php
class Event {
	private $IM;
	private $targets = array();
	
	function __construct($IM) {
		$this->IM = $IM;
	}
	
	function addTarget($target,$event,$caller,$listener) {
		if (empty($this->targets[$target]) == true) $this->targets[$target] = array();
		if (empty($this->targets[$target][$event]) == true) $this->targets[$target][$event] = array();
		if (empty($this->targets[$target][$event][$caller]) == true) $this->targets[$target][$event][$caller] = array();
		
		$this->targets[$target][$event][$caller][] = $listener;
	}
	
	function fireEvent($event,$target,$caller,$values=null,$results=null,&$context=null) {
		if (empty($this->targets[$target][$event][$caller]) == true) return;
		
		for ($i=0, $loop=sizeof($this->targets[$target][$event][$caller]);$i<$loop;$i++) {
			$this->execEvent($event,$target,$caller,$this->targets[$target][$event][$caller][$i],$values,$results,$context);
		}
	}
	
	function execEvent($event,$target,$caller,$listener,$values,$results,&$context) {
		$IM = $this->IM;
		
		$temp = explode('/',$listener);
		$listenerType = array_shift($temp);
		$listenerName = array_shift($temp);
		
		if ($listenerType == 'addon') {
			$Addon = new Addon($listenerName);
		} elseif ($listenerType == 'module') {
			$Module = $this->IM->getModule($listenerName);
		}
		
		if ($event == 'beforeDoProcess' || $event == 'afterDoProcess') {
			$action = $caller;
			unset($caller,$context);
			
			$Module = $this->IM->getModule($target);
		}
		
		if ($event == 'afterDoLayout') {
			$html = &$context;
			unset($results,$context);
		}
		
		if ($event == 'afterInitContext') {
			$view = $caller;
			unset($caller);
		}
		
		if ($event == 'afterGetContext') {
			$view = $caller;
			unset($caller);
		}
		$listenerPath = '';
		if ($listenerType == 'addon') {
			$listenerPath = __IM_PATH__.'/addons/'.$listenerName.'/'.$event.'.php';
		} else {
			$listenerPath = __IM_PATH__.'/modules/'.$listenerName.'/events/'.$event.'.php';
		}
		
		if ($listenerPath != '' && file_exists($listenerPath) == true) {
			INCLUDE $listenerPath;
		}
	}
}
?>