<?php
class AddonYoutube {
	private $_youtube;
	private $_accessToken;
	
	function __construct() {
		$_CLIENT_ID = '995059916144-2odfvfoh0h18fhfsid1lh25d1vpunm5n.apps.googleusercontent.com';
		$_CLIENT_SECRET = 'A3G-GgF_2rsWXUuvmU1hPLOv';
		$_CLIENT_REFRESH_TOKEN = '1/sNjSg0NxhstPy5nBqIyd6JZQnYlj80kWEFBoNYzNfykMEudVrK5jSpoR30zcRFq6';
		$_AUTH_URL = 'https://accounts.google.com/o/oauth2/auth';
		$_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
//		$GOOGLE_REVOKE = 'https://accounts.google.com/o/oauth2/revoke';

		$this->_youtube = new OAuthClient();
		$this->_youtube->setClientId($_CLIENT_ID)->setClientSecret($_CLIENT_SECRET)->setRefreshToken($_CLIENT_REFRESH_TOKEN)->setScope('https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.upload')->setAuthUrl($_AUTH_URL)->setTokenUrl($_TOKEN_URL);
		$this->_youtube->getAccessToken();
	}
	
	function get($url,$params=array()) {
		return $this->_youtube->get($url,$params);
	}
	
	function getUploadLocation($file) {
		$video = array(
			'snippet'=>array(
				'title'=>$file->name,
				'description'=>'via iModule YouTube Addon'
			),
			'status'=>array(
				'privacyStatus'=>'unlisted'
			)
		);
		$video = json_encode($video);
		
		$headers = array(
			'Authorization: Bearer '.$this->_youtube->getAccessToken(),
			'Content-type: application/json',
			'x-upload-content-length: '.$file->size,
			'x-upload-content-type: '.$file->mime,
			'Content-Length: '.strlen($video)
		);
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		
		curl_setopt($ch,CURLOPT_URL,'https://www.googleapis.com/upload/youtube/v3/videos?part=status%2Csnippet&uploadType=resumable');
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$video);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER,true);
		
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		
		if ($http_code == 401) {
			$this->_youtube->refreshToken();
			return $this->getUploadLocation($file);
		}
		
		curl_close($ch);
		
		if ($http_code == 200) {
			if (preg_match('/Location: (.*?)\r\n/',$result,$match) == true) {
				return $match[1];
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	function upload($url,$file,$bytes,$start) {
		$fileSize = strlen($bytes);
		$headers = array(
			'Authorization: Bearer '.$this->_youtube->getAccessToken(),
			'Content-Range: bytes '.$start.'-'.($start + $fileSize - 1).'/'.$file->size,
			'Content-type: '.$file->mime,
			'Content-Length: '.$fileSize
		);
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$bytes);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		$result = curl_exec($ch);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		
		if ($http_code == 401) {
			$this->_youtube->refreshToken();
			return $this->upload($url,$file,$bytes,$start);
		}
		
		if ($http_code == 200) {
			return json_decode($result);
		} else {
			return null;
		}
	}
	
	function saveThumbnail($uploaded,$path) {
		$id = $uploaded->id.'.jpg';
		$url = $uploaded->snippet->thumbnails->high->url;
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		$data = curl_exec($ch);
		$http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		
		if ($http_code == 200) {
			if (file_put_contents($path.'/'.$id,$data) === false) return null;
			else return $path.'/'.$id;
		} else {
			return null;
		}
	}
}
?>