	<div id="ModuleLmsYoutubePlayer"></div>
	
	<script>$(document).ready(function() { Lms.youtube.loadVideo("<?php echo $post->idx; ?>","<?php echo $attend->mode; ?>","<?php isset($access_token) == true ? $access_token : ''; ?>"); });</script>