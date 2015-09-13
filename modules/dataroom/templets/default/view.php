	<div class="contextTitle">
		<i class="fa fa-desktop"></i> <?php echo $Module->getLanguage('view/title'); ?>
	</div>
	
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
						
						<div class="button">
							<button class="btn <?php echo $purchase == null ? 'btnRed' : 'btnBlue'; ?>" onclick="Dataroom.download(<?php echo $post->idx; ?>);">
								<i class="fa <?php echo $post->price == 0 ? 'fa-download' : 'fa-shopping-cart'; ?>"></i>
								<?php if ($purchase == null) echo $post->price == 0 ? $Module->getLanguage('button/download_free') : $Module->getLanguage('button/download_buy'); else echo $Module->getLanguage('button/download_buyed'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($Module->checkPermission('modify') == true || $post->midx == $this->IM->getModule('member')->getLogged()) { ?>
		<div class="actionButton">
			<button type="button" class="btn btnRed" onclick="Dataroom.post.delete(<?php echo $post->idx; ?>);"><i class="fa fa-lock"></i> <?php echo $Module->getLanguage('button/lock'); ?></button>
			<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'version',$post->idx); ?>" class="btn btnBlue"><i class="fa fa-floppy-o"></i> <?php echo $Module->getLanguage('button/write_version'); ?></a>
			<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$post->idx); ?>" class="btn btnWhite"><i class="fa fa-pencil-square-o"></i> <?php echo $Module->getLanguage('button/modify'); ?></a>
		</div>
		<?php } else { ?>
		<div class="blankSpace"></div>
		<?php } ?>
		
		<div class="contextTab">
			<ul class="detail">
				<li data-tab="detail" class="selected"><?php echo $Module->getLanguage('detail'); ?></li>
				<li data-tab="history"><?php echo $Module->getLanguage('history'); ?></li>
				<li data-tab="qna"><?php echo $Module->getLanguage('qna'); ?> <span class="liveUpdateDataroomPostQna<?php echo $post->idx; ?>"><?php echo number_format($post->qna); ?></span></li>
				<li data-tab="ment"><?php echo $Module->getLanguage('ment'); ?> <span class="liveUpdateDataroomPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></span></li>
			</ul>
		</div>
		
		<div class="tabPanel">
			<div class="postContext"><?php echo $post->content; ?></div>
			
			<?php if (count($attachments) > 0) { ?>
			<div class="blankSpace"></div>
			
			<div class="contextTitle">
				<i class="fa fa-floppy-o"></i> <?php echo $Module->getLanguage('view/attachment'); ?> <span class="count"><?php echo number_format(count($attachments)); ?></span>
			</div>
			
			<ul class="attachment">
				<?php for ($i=0, $loop=count($attachments);$i<$loop;$i++) { $fileIcon = array('image'=>'fa-file-image-o'); ?>
				<li><a href="<?php echo $attachments[$i]->download; ?>" download="<?php echo $attachments[$i]->name; ?>"><span class="filesize">(<?php echo GetFileSize($attachments[$i]->size); ?>)</span><i class="fa <?php echo empty($fileIcon[$attachments[$i]->type]) == true ? 'fa-file-o' : $fileIcon[$attachments[$i]->type]; ?>"></i> <?php echo $attachments[$i]->name; ?></a></li>
				<?php } ?>
			</ul>
			<?php } ?>
		</div>
		
		<div class="contextTab">
			<ul class="history">
				<li data-tab="detail"><?php echo $Module->getLanguage('detail'); ?></li>
				<li data-tab="history" class="selected"><?php echo $Module->getLanguage('history'); ?></li>
				<li data-tab="qna"><?php echo $Module->getLanguage('qna'); ?> <span class="liveUpdateDataroomPostQna<?php echo $post->idx; ?>"><?php echo number_format($post->qna); ?></span></li>
				<li data-tab="ment"><?php echo $Module->getLanguage('ment'); ?> <span class="liveUpdateDataroomPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></span></li>
			</ul>
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
		
		<div class="blankSpace"></div>
		
		<div class="contextTab">
			<ul class="qna">
				<li data-tab="detail"><?php echo $Module->getLanguage('detail'); ?></li>
				<li data-tab="history"><?php echo $Module->getLanguage('history'); ?></li>
				<li data-tab="qna" class="selected"><?php echo $Module->getLanguage('qna'); ?> <span class="liveUpdateDataroomPostQna<?php echo $post->idx; ?>"><?php echo number_format($post->qna); ?></span></li>
				<li data-tab="ment"><?php echo $Module->getLanguage('ment'); ?> <span class="liveUpdateDataroomPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></span></li>
			</ul>
		</div>
		
		<div class="tabPanel">
			<?php echo $Module->getQnaList($idx); ?>
			
			<?php echo $Module->getQnaPagination($idx); ?>
		</div>
		
		<div class="contextTab">
			<ul class="ment">
				<li data-tab="detail"><?php echo $Module->getLanguage('detail'); ?></li>
				<li data-tab="history"><?php echo $Module->getLanguage('history'); ?></li>
				<li data-tab="qna"><?php echo $Module->getLanguage('qna'); ?> <span class="liveUpdateDataroomPostQna<?php echo $post->idx; ?>"><?php echo number_format($post->qna); ?></span></li>
				<li data-tab="ment" class="selected"><?php echo $Module->getLanguage('ment'); ?> <span class="liveUpdateDataroomPostMent<?php echo $post->idx; ?>"><?php echo number_format($post->ment); ?></span></li>
			</ul>
		</div>
		
		<div class="tabPanel">
			<?php echo $Module->getMentList($idx); ?>
		
			<?php echo $Module->getMentPagination($idx); ?>
		
			<div class="postMentWrite">
				<?php echo $Module->getMentWrite($idx); ?>
			</div>
		</div>
	</div>
	
	<table class="footerTable">
	<tr>
		<td class="buttonLeft">
			<a href="<?php echo $IM->getUrl(null,null,'list').$IM->getQueryString(); ?>" class="btn btnWhite"><i class="fa fa-bars"></i> <?php echo $Module->getLanguage('button/list'); ?></a>
		</td>
		<td class="buttonRight">
			<a href="<?php echo $IM->getUrl(null,null,'write').$IM->getQueryString(); ?>" class="btn btnRed"><i class="fa fa-question"></i> <?php echo $Module->getLanguage('button/write'); ?></a>
		</td>
	</tr>
	</table>
	
	<script>
	$(document).ready(function() {
		$(".contextTab li").on("click",function() {
			if ($(this).hasClass("selected") == false) {
				$(document).scrollTop($(".contextTab ul."+$(this).attr("data-tab")).offset().top - 50);
			}
		})
	});
	</script>