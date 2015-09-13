	<input type="hidden" name="id">
	<input type="hidden" name="thumbnail">
	<input type="hidden" name="time">
	<input type="hidden" name="status">
	<input type="hidden" name="caption">
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('youtube/url'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="url" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->url,'input').'"' : ''; ?>>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('youtube/help/url/default'); ?>" data-error="<?php echo $Module->getLanguage('youtube/help/url/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('postWrite/title'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="title" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->title,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('youtube/help/title/default'); ?>" data-error="<?php echo $Module->getLanguage('youtube/help/title/error'); ?>"></div>
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