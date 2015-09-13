	<input type="hidden" name="id">
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('postWrite/title'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="title" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->title,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('video/help/title/default'); ?>" data-error="<?php echo $Module->getLanguage('video/help/title/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('video/privacy'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="privacy" value="public">
				
				<div class="selectControl" data-field="privacy">
					<button type="button"> 으샤 <span class="arrow"></span></button>
					
					<ul>
						<li data-value="public"><?php echo $Module->getLanguage('video/help/privacy/public'); ?></li>
						<li data-value="private"><?php echo $Module->getLanguage('video/help/privacy/private'); ?></li>
					</ul>
				</div>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('video/help/privacy/default'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('video/file'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="file" name="file" accept="video/mp4,video/x-m4v,video/*" style="display:none;">
				
				<button type="button" data-role="select" class="btn btnRed"><i class="fa fa-video-camera"></i> <?php echo $Module->getLanguage('video/help/file/button'); ?></button>
				
				<div id="ModuleLmsSelectedFile" class="selectedFile"><i class="fa fa-spin fa-spinner"></i> <span id="ModuleLmsSelectedFileName" class="filename"></span> 업로드 대기중... (<span id="ModuleLmsWaitingTime" data-time="10">10</span>초 남음)</div>
				
				<div id="ModuleLmsUploadProgress" class="progress">
					<div class="bar"></div>
				</div>
				
				<div class="helpBlock" data-wait="업로드 준비중입니다..." data-complete="업로드가 완료되었습니다. 이제 학습자료를 등록할 수 있습니다." data-error="동영상 업로드중 에러가 발생하였습니다. 페이지를 새로고침하신 후 다시 시도하여 주십시오."></div>
				
				<button type="button" data-role="upload" class="btn btnRed" onclick="Lms.video.waitingTimer('run');"><i class="fa fa-upload"></i> 지금 바로 업로드</button>
				<button type="button" data-role="cancel" class="btn btnWhite" onclick="Lms.video.waitingTimer('stop');"><i class="fa fa-times"></i> 업로드 취소</button>
				
				
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> <?php echo $token->email; ?> 유튜브 계정에 동영상 파일을 업로드 한 후 학습자료로 등록하게 됩니다.
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('youtube/progress_check'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<label><input type="checkbox" name="progress_check" value="on"> <?php echo $Module->getLanguage('youtube/help/progress_check/default'); ?></label>
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> <?php echo $Module->getLanguage('youtube/help/progress_check/detail'); ?>
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('youtube/afk_check'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<label><input type="checkbox" name="afk_check" value="on"> <?php echo $Module->getLanguage('youtube/help/afk_check/default'); ?></label>
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> <?php echo $Module->getLanguage('youtube/help/afk_check/detail'); ?>
				</div>
			</div>
			
			<div class="inputBlock">
				<input type="input" name="afk_check_time" value="300" class="inputControl" style="width:50px;">
				<span>초 마다 자리비움을 체크합니다.</span>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setContent($post !== null ? $post->content : '')->loadFile($post !== null ? $post->attachments : array())->doLayout(); ?>
			</div>
		</td>
	</tr>
	<tr class="splitBottom">
		<td colspan="2"><div></div></td>
	</tr>
	</table>
	
	
<div id="loaded"></div>

<script>
//Lms.video.getUploadUrl();
</script>