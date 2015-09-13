	<div class="contextTitle">
		<i class="fa fa-cube"></i> <?php echo $Module->getLanguage($post === null ? 'postWrite/write' : 'postWrite/modify'); ?>
	</div>
	
	<?php echo $typeContext; ?>
	
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