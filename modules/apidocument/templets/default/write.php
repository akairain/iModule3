	<div class="contextTitle">
		<i class="fa fa-pencil"></i> <?php echo $Module->getLanguage($post === null ? 'write/title' : 'postModify/title'); ?>
	</div>
	
	<table class="writeTable">
	<tr>
		<td class="label"><?php echo $Module->getLanguage('type'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="hidden" name="type" value="<?php echo $post !== null ? $post->type : ''; ?>">
				<div class="selectControl" data-field="type" style="width:150px; max-width:100%;">
					<button type="button"><?php echo $Module->getLanguage('type'); ?> <span class="arrow"></span></button>
					<ul>
						<li data-value="CONFIG">CONFIG</li>
						<li data-value="PROPERTY">PROPERTY</li>
						<li data-value="GLOBAL">GLOBAL</li>
						<li data-value="METHOD">METHOD</li>
						<li data-value="EVENT">EVENT</li>
						<li data-value="ERROR">ERROR</li>
					</ul>
				</div>
				<div class="helpBlock"></div>
				
				<script>
				$("input[name=type]").on("change",function() {
					var helpBlock = $(this).parents("form").find("input[name=property]").parents(".inputBlock, .inputInline").find(".helpBlock");
					
					if ($(this).val() == "CONFIG") helpBlock.html("ConfigPropertyName:PropertyType");
					else if ($(this).val() == "PROPERTY") helpBlock.html("PropertyName:PropertyType");
					else if ($(this).val() == "GLOBAL") helpBlock.html("GlobalValueName:PropertyType");
					else if ($(this).val() == "METHOD") helpBlock.html("MethodName(ParameterType Parameter ...):ReturnValueType");
					else if ($(this).val() == "EVENT") helpBlock.html("EventName:(ParameterType Parameter ...)");
				});
				</script>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('name'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="name" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->name,'input').'"' : ''; ?> style="width:150px;" required>
				<label><input type="checkbox" name="is_required"> <?php echo $Module->getLanguage('required'); ?></label>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('property'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="property" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->property,'input').'"' : ''; ?> required>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('version'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="text" name="version" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->version,'input').'"' : ''; ?> style="width:100px; max-width:100%;" required>
				<div class="helpBlock" data-default="<?php echo $post == null ? $Module->getLanguage('write/help/version/default') : $Module->getLanguage('write/help/version/new'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('description'); ?></td>
		<td class="input">
			<div class="inputBlock">
				<input type="text" name="description" class="inputControl"<?php echo $post !== null ? ' value="'.GetString($post->description,'input').'"' : ''; ?> required>
				<div class="helpBlock" data-default="<?php echo $Module->getLanguage('write/help/description/default'); ?>"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td class="label"><?php echo $Module->getLanguage('stability'); ?></td>
		<td class="input">
			<div class="inputInline">
				<input type="hidden" name="stability" value="<?php echo $post !== null ? $post->stability : ''; ?>">
				<div class="selectControl" data-field="stability">
					<button type="button"><?php echo $Module->getLanguage('stability'); ?> <span class="arrow"></span></button>
					<ul>
						<li data-value="DEPRECATED">DEPRECATED : <?php echo $Module->getLanguage('write/help/stability/DEPRECATED'); ?></li>
						<li data-value="EXPERIMENTAL">EXPERIMENTAL : <?php echo $Module->getLanguage('write/help/stability/EXPERIMENTAL'); ?></li>
						<li data-value="UNSTABLE">UNSTABLE : <?php echo $Module->getLanguage('write/help/stability/UNSTABLE'); ?></li>
						<li data-value="STABLE">STABLE : <?php echo $Module->getLanguage('write/help/stability/STABLE'); ?></li>
						<li data-value="FROZEN">FROZEN : <?php echo $Module->getLanguage('write/help/stability/FROZEN'); ?></li>
						<li data-value="LOCKED">LOCKED : <?php echo $Module->getLanguage('write/help/stability/LOCKED'); ?></li>
					</ul>
				</div>
				<div class="helpBlock"></div>
			</div>
		</td>
	</tr>
	<tr class="split">
		<td colspan="2"></td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="inputBlock wysiwygFrame">
				<?php $Module->getWysiwyg('content')->setRequired(true)->setContent($post !== null ? $post->content : '')->doLayout(); ?>
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
			<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('write/loading'); ?>"><i class="fa fa-paper-plane-o"></i> <?php echo $Module->getLanguage($post === null ? 'button/submit' : 'button/modify'); ?></button>
		</td>
	</tr>
	</table>