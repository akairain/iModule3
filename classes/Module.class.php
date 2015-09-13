<?php
class Module {
	private $IM;
	
	private $table;
	private $modulePath;
	private $moduleDir;
	private $moduleInfo;
	private $moduleConfigs = null;
	private $moduleInstalled = null;
	
	function __construct($IM) {
		$this->IM = $IM;
		$this->table = new stdClass();
		$this->table->module = 'module_table';
		
		/* for beta only Start */
		$modulesPath = __IM_PATH__.'/modules';
		$modulesDir = opendir($modulesPath);
		while ($module = readdir($modulesDir)) {
			if (is_dir($modulesPath.'/'.$module) == true) {
				$hash = $this->getHash($module);
				$target = json_encode($this->getTarget($module));
				
				if ($hash == false) continue;
				
				if ($this->IM->db()->select($this->table->module)->where('module',$module)->has() == true) {
					$this->IM->db()->update($this->table->module,array('hash'=>$hash,'target'=>$target))->where('module',$module)->execute();
				}
			}
		}
		/* for beta only End */
		
		$modules = $this->IM->db()->select($this->table->module)->get();
		for ($i=0, $loop=sizeof($modules);$i<$loop;$i++) {
			$targets = json_decode($modules[$i]->target);
			
			foreach ($targets as $target=>$events) {
				foreach ($events as $event=>$callers) {
					foreach ($callers as $caller) {
						$this->IM->Event->addTarget($target,$event,$caller,'module/'.$modules[$i]->module);
					}
				}
			}
		}
	}
	
	function getHash($module) {
		return file_exists(__IM_PATH__.'/modules/'.$module.'/'.$module.'.json') == true ? md5_file(__IM_PATH__.'/modules/'.$module.'/'.$module.'.json') : false;
	}
	
	function getTarget($module) {
		if (file_exists(__IM_PATH__.'/modules/'.$module.'/'.$module.'.json') == false) return false;
		
		$json = json_decode(file_get_contents(__IM_PATH__.'/modules/'.$module.'/'.$module.'.json'));
		return empty($json->target) == true ? new stdClass() : $json->target;
	}
	
	function loadGlobals() {
		$globals = $this->IM->db()->select($this->table->module)->where('is_global','TRUE')->get();
		for ($i=0, $loop=sizeof($globals);$i<$loop;$i++) {
			$this->IM->getModule($globals[$i]->module);
		}
	}
	
	function load($module) {
		$this->modulePath = __IM_PATH__.'/modules/'.$module;
		$this->moduleDir = __IM_DIR__.'/modules/'.$module;
		
		if (is_dir($this->modulePath) == false) return false;
		
		$this->moduleInfo = json_decode(file_get_contents($this->modulePath.'/'.$module.'.json'));
		
		if ($this->moduleInfo->install == true) {
			$this->moduleInstalled = $this->IM->db('default')->select($this->table->module)->where('module',$module)->getOne();
			if ($this->moduleInstalled == null) return false;
			else $this->moduleConfigs = json_decode($this->moduleInstalled->configs);
		}
		
		$class = 'Module'.ucfirst($module);
		if (file_exists($this->modulePath.'/'.$class.'.class.php') == false) return false;
		
		return new $class($this->IM,$this);
	}
	
	function getPath() {
		return $this->modulePath;
	}
	
	function getDir() {
		return $this->moduleDir;
	}
	
	function getInfo() {
		return $this->moduleInfo;
	}
	
	function getInstalled() {
		return $this->moduleInstalled;
	}
	
	function getConfig($key) {
		if (empty($this->moduleConfigs->$key) == true) return null;
		else return $this->moduleConfigs->$key;
	}
	
	function resetArticle() {
		$modules = $this->IM->db()->select($this->table->module)->where('is_article','TRUE')->get();
		for ($i=0, $loop=count($modules);$i<$loop;$i++) {
			$this->IM->getModule($modules[$i]->module)->resetArticle();
		}
	}
}
?>