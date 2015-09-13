	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage($post === null ? 'postWrite/write' : 'postWrite/modify'); ?>
	</div>
	
	<table class="writeTable">
	<?php if ($IM->getModule('member')->isLogged() == false) { ?>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('name'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="name" class="inputControl" style="width:150px;"<?php echo $post !== null ? ' value="'.GetString($post->name,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-error="<?php echo $Module->getLanguage('postWrite/help/name/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('password'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="password" name="password" class="inputControl" style="width:150px;" autocomplete="off" required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage($post === null ? 'write/help/password/default' : 'postModify/password'); ?>" data-error="<?php echo $Module->getLanguage('postWrite/help/password/error'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('email'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="email" class="inputControl" style="width:250px; max-width:100%;"<?php echo $post !== null ? ' value="'.GetString($post->email,'input').'"' : ''; ?>>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('postWrite/help/email/default'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<?php } ?>
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
						<li data-value="0"><?php echo $Module->getLanguage('category_none'); ?></li>
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
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setContent($post !== null ? $post->content : '')->loadFile($post !== null ? $post->attachments : array())->doLayout(); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="optionBlock">
				<div class="row">
					<div class="col-md-6"><?php echo $Module->getOptionForm('postWrite','is_notice',$post !== null ? $post->is_notice : null); ?></div>
					<div class="col-md-6"><?php echo $Module->getOptionForm('postWrite','is_html_title',$post !== null ? $post->is_html_title : null); ?></div>
					<div class="col-md-6"><?php echo $Module->getOptionForm('postWrite','is_secret',$post !== null ? $post->is_secret : null); ?></div>
					<div class="col-md-6"><?php echo $Module->getOptionForm('postWrite','is_hidename',$post !== null ? $post->is_hidename : null); ?></div>
				</div>
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