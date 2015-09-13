					<div class="blankSpace"></div>
					
					<?php if ($IM->domain == 'www.minitalk.kr') { ?>
					<?php $IM->getWidget('dataroom/recently')->setTemplet('default')->setValue('did','minitalk')->setValue('category',array(4,5,6,7,8))->setValue('titleIcon','<i class="fa fa-download"></i>')->setValue('count',8)->setValue('title','최근 등록된 자료')->doLayout(); ?>
					<?php } ?>