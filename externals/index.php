		<?php $temp = explode('.',$IM->domain); $siteCode = $temp[1]; ?>
		<div class="index container">
			<div class="row">
				<div class="col-md-9">
					<div class="notice">
						<section>
							<?php
							if ($IM->getPages('index','notice') !== null && $IM->getPages('index','notice')->type == 'module' && $IM->getPages('index','notice')->context->module == 'board') {
								$notice = $IM->getWidget('board/recently')->setTemplet('@notice')->setValue('type','post')->setValue('bid',$IM->getPages('index','notice')->context->context)->setValue('titleIcon','<i class="fa fa-bell"></i>')->setValue('count',3);
								if ($IM->getPages('index','notice')->context->config != null && $IM->getPages('index','notice')->context->config->category) {
									$notice->setValue('category',$IM->getPages('index','notice')->context->config->category);
								}
								$notice->doLayout();
							}
							?>
						</section>
						
						<aside>
							<?php $IM->getWidget('member/recently')->setTemplet('default')->setValue('count',16)->doLayout(); ?>
						</aside>
					</div>
					
					<div class="blankSpace"></div>
					
					<div class="row">
						<div class="col-sm-6">
							<?php $IM->getWidget('article')->setTemplet('default')->setValue('type','post')->setValue('count',10)->setValue('titleIcon','<i class="fa fa-leaf"></i>')->doLayout(); ?>
						</div>
						
						<div class="col-sm-6">
							<?php $IM->getWidget('article')->setTemplet('default')->setValue('type','ment')->setValue('count',10)->setValue('titleIcon','<i class="fa fa-leaf"></i>')->doLayout(); ?>
						</div>
					</div>
					
					<?php if (file_exists(__IM_PATH__.'/externals/index.context.'.$siteCode.'.php') == true) INCLUDE __IM_PATH__.'/externals/index.context.'.$siteCode.'.php'; ?>
				</div>
				
				<div class="col-md-3 hidden-sm hidden-xs">
					<?php $IM->getWidget('member/login')->setTemplet('@sidebar')->doLayout(); ?>
					
					<?php if (file_exists(__IM_PATH__.'/externals/index.side.'.$siteCode.'.php') == true) INCLUDE __IM_PATH__.'/externals/index.side.'.$siteCode.'.php'; ?>
					
					<div style="min-height:600px;">
						<div class="rightFixed">
							<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
							<div class="rightFixedInner" data-google-responsive="true">
								<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-3210736654114323" data-ad-slot="1232214968" data-ad-format="auto"></ins>
								<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>