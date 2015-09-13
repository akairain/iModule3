<?php
REQUIRE_ONCE $Addon->getPath().'/classes/AddonYoutube.class.php';

if ($target == 'attachment') {
	$Module = $IM->getModule('attachment');
	
	if ($action == 'load') {
		for ($i=0, $loop=sizeof($values->files);$i<$loop;$i++) {
			if ($values->files[$i]->type == 'video') {
				$values->files[$i]->youtube = $Module->getFileExtraInfo($values->files[$i]->idx,'youtube');
			}
		}
	}
	
	if ($action == 'upload') {
		if ($results->success == true) {
			if ($values->fileInfo->type == 'video') {
				$uploaded = null;
				$youtube = new AddonYoutube();
				
				if ($values->status == 'METADATA') {
					$youtubeInfo = new stdClass();
					$youtubeInfo->location = $youtube->getUploadLocation($values->fileInfo);
					if ($youtubeInfo->location != null) {
						$Module->setFileExtraInfo($values->fileInfo->idx,'youtube',$youtubeInfo);
					}
				} else {
					$youtubeInfo = $Module->getFileExtraInfo($values->fileInfo->idx,'youtube');
	 				if ($youtubeInfo->location != null) {
		 				$uploaded = $youtube->upload($youtubeInfo->location,$values->fileInfo,$values->chunkBytes,$values->chunkRangeStart);
		 				
						if (isset($uploaded->etag) == true && isset($uploaded->id) == true && isset($uploaded->status) == true) {
							$youtubeInfo = new stdClass();
							$youtubeInfo->id = $uploaded->id;
							$youtubeInfo->status = $uploaded->status->uploadStatus;
							
							$Module->setFileExtraInfo($values->fileInfo->idx,'youtube',$youtubeInfo);
							$values->fileInfo->youtube = $youtubeInfo;
						}
	 				}
				}
			}
		}
	}
}
?>