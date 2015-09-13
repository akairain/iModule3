<?php
if ($target == 'shop') {
	REQUIRE_ONCE $Addon->getPath().'/init.php';
	
	if ($action == 'getRequiredFields') {
		$category = Request('category');
		
		if (isset($fields[$category]) == true) {
			$results->success = true;
			$results->fields = array();
			foreach ($fields[$category] as $key=>$value) {
				$results->fields[] = array('name'=>$key,'title'=>$value[0],'help'=>$value[1],'value'=>$value[2],'required'=>$value[3],'upload'=>$value[4]);
			}
		} else {
			$results->success = false;
		}
	}
}
?>