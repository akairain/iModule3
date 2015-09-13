<div class="WidgetDataroomDownloadDefault">
	<div class="contextTitle">
		<?php echo $titleIcon ? $titleIcon : '<i class="fa fa-download"></i>'; ?> <?php echo $title; ?>
	</div>
	
	<div class="latest">
		<div class="info">
			<div class="boxDefault">
				<?php echo $Widget->getValue('text'); ?>
			</div>
		</div>
		<div class="button">
			<button type="button" onclick="Dataroom.download(<?php echo $post->idx; ?>);">
				<i class="fa fa-download"></i>
				<div class="text">최신버전 다운로드</div>
				<div class="version">Latest <b><?php echo $post->last_version; ?></b> <span class="reg_date"><?php echo GetTime('M d, Y',$post->last_update); ?></div>
			</button>
		</div>
	</div>
	
	<table class="versionTable">
	<thead>
		<tr>
			<th class="title">
				<div><?php echo $Module->getLanguage('filename'); ?></div>
			</th>
			<th class="version">
				<div><?php echo $Module->getLanguage('version'); ?></div>
			</th>
			<th class="size">
				<div><?php echo $Module->getLanguage('filesize'); ?></div>
			</th>
			<th class="reg_date">
				<div><?php echo $Module->getLanguage('reg_date'); ?></div>
			</th>
			<th class="hit">
				<div><?php echo $Module->getLanguage('hit'); ?></div>
			</th>
			<th class="download">
				<div><span class="fa fa-download"></span></div>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php for ($i=0, $loop=count($versions);$i<$loop;$i++) { ?>
		<tr<?php echo $i == 0 ? ' class="opened"' : ''; ?>>
			<td class="title" onclick="$(this).parents('tr').toggleClass('opened');"><div><i class="fa fa-caret-right"></i><i class="fa fa-caret-down"></i> <?php echo $versions[$i]->file->name; ?></div></td>
			<td class="version"><div><?php echo $versions[$i]->version; ?></div></td>
			<td class="size"><div><?php echo GetFileSize($versions[$i]->file->size); ?></div></td>
			<td class="reg_date"><div><?php echo GetTime('F d, Y',$versions[$i]->reg_date); ?></div></td>
			<td class="hit"><div><?php echo number_format($versions[$i]->file->hit); ?></div></td>
			<td class="download">
				<button type="button" class="btn btnRed" onclick="Dataroom.download(<?php echo $idx; ?>,'<?php echo $versions[$i]->version; ?>');">
					<i class="fa <?php echo $post->price == 0 ? 'fa-download' : 'fa-shopping-cart'; ?>"></i>
					<span><?php if ($purchase == null) echo $post->price == 0 ? $Module->getLanguage('button/download_free') : $Module->getLanguage('button/download_buy'); else echo $Module->getLanguage('button/download_buyed'); ?></span>
				</button>
			</td>
		</tr>
		<tr class="toggle">
			<td colspan="6">
				<ul class="history">
					<?php for ($j=0, $loopj=count($versions[$i]->history);$j<$loopj;$j++) { ?>
					<li><?php echo $versions[$i]->history[$j]; ?></li>
					<?php } ?>
				</ul>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	</table>
	
	<div class="boxInfo"><i class="fa fa-warning"></i> 위의 자료를 다운받는 것은 해당 라이센스에 동의함으로 간주됩니다. 라이센스정책을 확인하여 주시기 바랍니다.</div>
</div>