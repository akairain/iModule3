<?php
REQUIRE_ONCE '../../configs/init.config.php';
REQUIRE_ONCE './classes/AddonYoutube.class.php';

$idx = Request('idx');
$IM = new iModule();
$Attachment = $IM->getModule('attachment');

if ($idx == null) exit();

$results = new stdClass();
$youtubeInfo = $Attachment->getFileExtraInfo($idx,'youtube');
if ($youtubeInfo != null) {
	$youtube = new AddonYoutube();
	$data = $youtube->get('https://www.googleapis.com/youtube/v3/videos?part=status%2Csnippet%2CprocessingDetails%2Cplayer&id='.$youtubeInfo->id);
	
	if ($data != null) {
		$item = $data->items[0];
		$results->id = $item->id;
		$results->status = $item->status->uploadStatus;
		
		$Attachment->setFileExtraInfo($idx,'youtube',$results);

		$results->thumbnail = $Attachment->getAttachmentUrl($idx,'thumbnail');
		if ($results->thumbnail == null) {
			$thumbnail = $youtube->saveThumbnail($item,$Attachment->getTempPath(true));
			if ($thumbnail != null) {
				$file = $Attachment->db()->select($Attachment->table->attachment)->where('idx',$idx)->getOne();
				$image = getimagesize($thumbnail);
				if ($Attachment->createThumbnail($thumbnail,$IM->getAttachmentPath().'/'.$file->path.'.thumb',($image[0] <= $image[1] ? 200 : 0),($image[0] > $image[1] ? 200 : 0),true) == true) {
					$results->thumbnail = $Attachment->getAttachmentUrl($idx,'thumbnail');
				}
			}
		}
	}
}

exit(json_encode($results));
?>