	<div class="contextTitle">
		<i class="fa fa-archive"></i> <?php echo $Module->getLanguage($post === null ? 'postWrite/write' : 'postWrite/modify'); ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td class="warning">
			<i class="fa fa-warning"></i> 이 페이지는 신규 자료를 등록하는 페이지로, 기존에 등록한 자료의 버전업데이트 등록을 할 수 없습니다.<br>
			(단, 기존자료와 많은부분이 변경되어 신규로 판매하고자 하는 경우에는 신규등록이 가능합니다.)<br>
			기존에 등록한 자료의 버전업데이트 등록은 기존 자료 페이지의 버전추가하기 메뉴를 통해 가능합니다.
		</td>
	</tr>
	<tr class="split">
		<td></td>
	</tr>
	</table>
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('title'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="title" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->title,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-error="<?php echo $Module->getLanguage('postWrite/help/title/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<?php if (count($categorys) > 0) { ?>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('category'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="hidden" name="category" value="<?php echo $post !== null ? $post->category : ($default != null && isset($default->category) == true ? $default->category : 0); ?>">
				<div class="selectControl" data-field="category" style="width:150px; max-width:100%;">
					<button type="button"><?php echo $Module->getLanguage('category'); ?> <span class="arrow"></span></button>
					
					<ul>
						<?php for ($i=0, $loop=count($categorys);$i<$loop;$i++) { ?>
						<li data-value="<?php echo $categorys[$i]->idx; ?>"><?php echo $categorys[$i]->title; ?></li>
						<?php } ?>
					</ul>
				</div>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<?php } elseif ($post != null || ($default != null && isset($default->category) == true)) { ?>
	<input type="hidden" name="category" value="<?php echo $post != null ? $post->category : $default->category; ?>">
	<?php } ?>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('homepage'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="homepage" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->homepage,'input').'"' : ''; ?>>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('license'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="hidden" name="license"<?php echo $post !== null ? ' value="'.$post->license.'"' : ''; ?>>
				<div class="selectControl" data-field="license" style="width:150px;">
					<button type="button"><?php echo $Module->getLanguage('license'); ?> <span class="arrow"></span></button>
					
					<ul>
						<li data-value="GPL V2">GPL V2</li>
						<li data-value="LGPL V2">LGPL V2</li>
						<li data-value="GPL V3">GPL V3</li>
						<li data-value="LGPL V3">LGPL V3</li>
						<li data-value="MIT License">MIT License</li>
						<li data-value="Apache License 2.0">Apache License</li>
						<li data-value="BSD License">BSD License</li>
						<li data-value="Mozilla Public License">Mozilla Public License</li>
						<li data-value="Public License">Public License</li>
						<li data-value="WTFPL">WTFPL</li>
						<li data-value="CopyRight">CopyRight</li>
						<li data-value="Others">Others</li>
					</ul>
				</div>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('postWrite/help/license/default'); ?>" data-error="<?php echo $Module->getLanguage('postWrite/help/license/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('price'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="price" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->price,'price').'"' : ''; ?> style="width:100px;">
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('postWrite/help/price/default'); ?>"></div>
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> 자료를 구매한 구매자는 같은 자료의 버전업데이트를 무료로 다운로드 받을 수 있습니다.<br>
					버전업데이트 자료를 별도로 판매하고자 하는 경우에는 신규자료등록을 하여 신규로 판매할 수 있습니다.<br>
					(스토어 운영정책에 따라, 판매가의 30% 의 포인트가 수수료로 차감되며, 판매즉시 회원님께 포인트로 적립됩니다.)
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('logo'); ?></td>
		<td class="input">
			<div class="inputInline">
				<button type="button" onclick="Dataroom.post.selectLogo(this);" class="btn btnRed" style="margin-right:5px;"><i class="fa fa-upload"></i> <?php echo $Module->getLanguage('button/file'); ?></button>
				<div class="helpBlock"<?php echo $post == null || $post->logo == null ? 'data-default="'.$Module->getLanguage('postWrite/help/logo/default').'"' : ''; ?>><?php echo $post !== null && $post->logo !== null ? '<i class="fa fa-file-image-o"></i> '.$post->logo->name : ''; ?></div>
				<input type="file" name="logo" accept="image/jpeg,image/png,image/gif" style="display:none;">
				
				<div class="boxDefault">
					<i class="fa fa-warning"></i> 로고이미지는 최대 500픽셀의 정사각형으로 표시됩니다.
				</div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="2" class="warning">
			<i class="fa fa-warning"></i> 자료소개 내용에 자료파일이 첨부되지 않게 유의하여 주십시오. 자료파일은 자료등록 후 다음 페이지에서 등록가능합니다.<br>
			본문에 삽입되어진 자료는 구매여부와 관계없이 다운로드 받을 수 있으며, 버전별 다운로드에 반영되지 않습니다.
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
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list'); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('postWrite/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage($post === null ? 'button/submit' : 'button/modify'); ?></button>
		</td>
	</tr>
	</table>