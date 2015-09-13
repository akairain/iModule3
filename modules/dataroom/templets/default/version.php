	<div class="contextTitle">
		<i class="fa fa-floppy-o"></i> <?php echo $Module->getLanguage('versionWrite/title'); ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td>
			<div class="viewPanel">
				<div class="itemBox">
					<div class="itemPanel">
						<div class="logoPanel">
							<div class="logo" style="background-image:url(<?php echo $post->logo == null ? '' : $post->logo->path; ?>);"></div>
						</div>
						
						<div class="detailPanel">
							<div class="detail">
								<h4><?php echo $post->title; ?></h4>
								
								<div class="description"><?php echo $post->search; ?></div>
								
								<div class="price">
									<i class="fa fa-rub"></i> <?php echo $post->price == 0 ? 'FREE' : number_format($post->price); ?>
								</div>
								
								<div class="splitLine"></div>
								
								<div class="tag">
									<div class="label"><?php echo $Module->getLanguage('name'); ?></div>
									<div class="value"><?php echo $post->name; ?></div>
								</div>
								
								<div class="tag">
									<div class="label"><?php echo $Module->getLanguage('homepage'); ?></div>
									<div class="value"><?php echo $post->homepage; ?></div>
								</div>
								
								<div class="splitLine"></div>
								
								<div class="tag">
									<div class="label"><?php echo $Module->getLanguage('license'); ?></div>
									<div class="value"><?php echo $post->license; ?></div>
								</div>
								
								<div class="tag">
									<div class="label"><?php echo $Module->getLanguage('version'); ?></div>
									<div class="value version"><?php echo $post->last_version; ?> <span class="date">(<?php echo GetTime('Y.m.d H:i',$post->last_update); ?>)</span></div>
								</div>
								
								<div class="splitLine"></div>
								
								<div class="buttonSplit">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$post->idx); ?>" class="btn btnWhite"><i class="fa fa-pencil-square-o"></i> <?php echo $Module->getLanguage('button/modify'); ?></a>
									<button type="button" class="btn btnRed"><i class="fa fa-lock"></i> <?php echo $Module->getLanguage('button/lock'); ?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="warning">
			<i class="fa fa-warning"></i> 이 페이지는 기존 자료의 신규버전을 등록하는 페이지로, 기존에 등록한 자료의 업데이트버전이 아닌 경우 신규 자료를 등록하는 페이지에서 등록하여 주시기 바랍니다.<br>
			신규버전의 경우, 기존버전을 구매한사람은 무료로 업데이트버전을 다운로드 받을 수 있습니다.<br>
			(기존 구매자에게 신규로 판매하고자 하는 경우 신규자료등록페이지를 이용하시기 바랍니다.)
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('version'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="version" class="inputControl" style="width:100px;" required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('versionWrite/help/version/default'); ?>" data-error="<?php echo $Module->getLanguage('versionWrite/help/version/error'); ?>"></div>
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> 자료버전은 버전규격에 맞게 숫자와 점으로만 구성될 수 있습니다.
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('file'); ?></td>
		<td class="input">
			<div class="inputInline">
				<button type="button" onclick="Dataroom.version.selectFile(this);" class="btn btnRed" style="margin-right:5px;"><i class="fa fa-upload"></i> <?php echo $Module->getLanguage('button/file'); ?></button>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('versionWrite/help/file/default'); ?>"></div>
				<input type="file" name="file" accept="application/zip" style="display:none;">
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> 자료를 구매한 분들을 보호하기 위하여 한번 배포/판매된 자료는 삭제할 수 없습니다.<br>자료에 문제가 있다면 새로운버전으로 업데이트하시거나, 자료를 배포중단할 수 있습니다. 배포중단은 신규 구매 및 다운로드만 제한되며 기존에 구매하거나 다운로드한 사람은 계속 다운로드 받을 수 있습니다.
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('history'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<textarea name="history" class="textareaControl" style="height:200px;"></textarea>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('versionWrite/help/history/default'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="splitBottom">
		<td colspan="2"><div></div></td>
	</tr>
	</table>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list'); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('versionWrite/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage('button/submit'); ?></button>
		</td>
	</tr>
	</table>