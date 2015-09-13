	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage($class === null ? 'create/create' : 'create/modify'); ?>
	</div>
	
	<div class="writeLayout">
		<div class="writeColumn">
			<div class="photo-editor-container">
				<div class="photo-editor">
					<input type="file" class="cropit-image-input">
					<div class="cropit-image-preview-container">
						<div class="classImage cropit-image-preview">
							<div class="classTitle">
								<div class="title"><?php echo $class != null ? $class->title : $Module->getLanguage('class_title'); ?></div>
								<div class="author"><?php echo $this->IM->getModule('member')->getMemberNickname($class != null ? $class->midx : null,false,$Module->getLanguage('author')); ?></div>
								<div class="info"><i class="fa fa-user"></i> 0 <i class="fa fa-book"></i> 0</div>
							</div>
						</div>
						
					</div>
						
					<div class="cropit-image-zoom-container">
						<span class="cropit-image-zoom-out"><i class="fa fa-picture-o"></i></span>
						<input type="range" class="cropit-image-zoom-input">
						<span class="cropit-image-zoom-in"><i class="fa fa-picture-o"></i></span>
					</div>
				</div>
				
				<button type="button" class="btn btnBlue selectImage" onclick="$('input.cropit-image-input').click();">표지이미지 선택</button>
				
				<input type="hidden" name="cover">
			</div>
			
			<script>
			$(function() {
				$(".photo-editor").cropit({
					exportZoom:2,
					imageBackground:true,
					imageBackgroundBorderWidth:30,
					imageState:{
						src:"<?php echo $class->cover != null ? $class->cover->path : ''; ?>"
					}
				});

				$(".export").click(function() {
					var imageData = $('.image-editor').cropit('export');
					window.open(imageData);
				});
			});
			</script>
		</div>
		
		<div class="writeColumn">
			<table class="writeTable">
			<tr>
				<td class="label"><?php echo $Module->getLanguage('class_title'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="text" name="title" class="inputControl"<?php echo $class !== null ? ' value="'.GetString($class->title,'input').'"' : ''; ?> required>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('create/help/title/default'); ?>" data-error="<?php echo $Module->getLanguage('create/help/title/error'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('class_intro'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<textarea name="content" class="textareaControl" rows="10" required><?php echo $class != null ? $class->content : ''; ?></textarea>
						
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('create/help/content/default'); ?>" data-error="<?php echo $Module->getLanguage('create/help/content/error'); ?>"></div>
					</div>
				</td>
			</tr>
			<?php if (count($labels) > 0) { ?>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('class_label'); ?></td>
				<td class="input">
					<div class="inputInline">
						<?php for ($i=0, $loop=count($labels);$i<$loop;$i++) { ?>
						<label><input type="checkbox" name="labels[]" value="<?php echo $labels[$i]->idx; ?>"<?php echo ($class !== null && in_array($labels[$i]->idx,$class->labels) == true) || (isset($default->label) == true && $default->label == $labels[$i]->idx) ? ' checked' : ''; ?>> <?php echo $labels[$i]->title; ?></label>
						<?php } ?>
						<div class="helpBlock"></div>
					</div>
				</td>
			</tr>
			<?php } ?>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('class_type'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="hidden" name="type" value="<?php echo $class != null ? $class->type : 'public'; ?>">
						<div class="selectControl" data-field="type">
							<button type="button"><?php echo $Module->getLanguage('class_type'); ?> <span class="arrow"></span></button>
							
							<ul>
								<li data-value="public"><?php echo $Module->getLanguage('class_type_list/public'); ?></li>
								<li data-value="private"><?php echo $Module->getLanguage('class_type_list/private'); ?></li>
							</ul>
						</div>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('create/help/type/default'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('class_type'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="hidden" name="attend" value="<?php echo $class != null ? $class->type : 'open'; ?>">
						<div class="selectControl" data-field="attend">
							<button type="button"><?php echo $Module->getLanguage('class_attend'); ?> <span class="arrow"></span></button>
							
							<ul>
								<li data-value="public"><?php echo $Module->getLanguage('class_attend_list/open'); ?></li>
								<li data-value="private"><?php echo $Module->getLanguage('class_attend_list/close'); ?></li>
							</ul>
						</div>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('create/help/attend/default'); ?>" data-private="<?php echo $Module->getLanguage('create/help/attend/private'); ?>"></div>
					</div>
				</td>
			</tr>
			</table>
		</div>
	</div>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list'); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('postWrite/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage($class === null ? 'button/submit' : 'button/modify'); ?></button>
		</td>
	</tr>
	</table>