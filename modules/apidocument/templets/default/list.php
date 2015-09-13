	<script>
	$(document).ready(function() {
		$("#ModuleApidocumentVersion").on("change",function() {
			location.href = ENV.getUrl(null,null,'list',$(this).val());
		});
		
		$("#ModuleApidocumentKeyword").on("keyup",function() {
			if ($(this).val().length > 0) {
				$("tr[data-name]").hide();
				$(".ModuleApidocumentDefault .boxDefault").hide();
				$("tr[data-name*='"+$(this).val()+"']").show();
			} else {
				$(".ModuleApidocumentDefault .boxDefault").show();
				$("tr[data-name]").show();
			}
		});
		
		$("button[data-type]").on("click",function() {
			$("html, body").animate({scrollTop:$(".ModuleApidocumentSection."+$(this).attr("data-type")).offset().top - $("#iModuleNavigation").height() - 5},"fast");
		});
		
		var buttons = $("button[data-type]");
		for (var i=0, loop=buttons.length;i<loop;i++) {
			if ($(".ModuleApidocumentSection."+$(buttons[i]).attr("data-type")).length == 0) {
				$(buttons[i]).hide();
			}
		}
	});
	</script>

	<div class="documentHeader">
		<h4>
			<i class="fa fa-cubes"></i> <?php echo $apidocument->class; ?>
			
			<div class="search"><i class="fa fa-search"></i> <input type="text" id="ModuleApidocumentKeyword" class="inputControl" style="width:160px;" placeholder="Property or Method Name"></div>
		</h4>
		
		<table class="table">
		<tr>
			<td class="label">
				<div class="text">Latest</div>
			</td>
			<td class="split"></td>
			<td class="value">
				<div class="text"><?php echo count($versions) > 0 ? $versions[0] : 'NONE'; ?></div>
			</td>
			<td class="split"></td>
			<td class="label">
				<div class="text">Defined</div>
			</td>
			<td class="split"></td>
			<td class="value">
				<div class="text">minitalk.js</div>
			</td>
		</tr>
		<tr>
			<td colspan="7" class="split"></td>
		</tr>
		<tr>
			<td class="label">
				<div class="text">Versions</div>
			</td>
			<td class="split"></td>
			<td class="value">
				<div class="input">
					<input type="hidden" id="ModuleApidocumentVersion" value="<?php echo $version; ?>">
					<div class="selectControl" data-field="#ModuleApidocumentVersion" style="width:150px;">
						<button type="button">Version <span class="arrow"></span></button>
						
						<ul>
							<?php for ($i=0, $loop=count($versions);$i<$loop;$i++) { ?>
							<li data-value="<?php echo $versions[$i]; ?>">v<?php echo $versions[$i]; ?></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="7" class="splitBottom"><div></div></td>
		</tr>
		</table>
	</div>
	
	<div class="boxDefault">
		<ul>
			<li><span class="stability" data-stability="DEPRECATED">DEPRECATED</span> <?php echo $Module->getLanguage('write/help/stability/DEPRECATED'); ?></li>
			<li><span class="stability" data-stability="EXPERIMENTAL">EXPERIMENTAL</span> <?php echo $Module->getLanguage('write/help/stability/EXPERIMENTAL'); ?></li>
			<li><span class="stability" data-stability="UNSTABLE">UNSTABLE</span> <?php echo $Module->getLanguage('write/help/stability/UNSTABLE'); ?></li>
			<li><span class="stability" data-stability="STABLE">STABLE</span> <?php echo $Module->getLanguage('write/help/stability/STABLE'); ?></li>
			<li><span class="stability" data-stability="FROZEN">FROZEN</span> <?php echo $Module->getLanguage('write/help/stability/FROZEN'); ?></li>
			<li><span class="stability" data-stability="LOCKED">LOCKED</span> <?php echo $Module->getLanguage('write/help/stability/LOCKED'); ?></li>
		</ul>
	</div>
	
	<?php if (count($configs) > 0) { ?>
	<div class="ModuleApidocumentSection configs">
		<div class="sectionHeader">
			<div class="title">
				<i class="fa fa-cog"></i>Configs
				
				<div class="hidden-xs">
					<button type="button" data-type="configs"><i class="fa fa-cog"></i> Configs</button>
					<button type="button" data-type="properties"><i class="fa fa-tags"></i> Properties</button>
					<button type="button" data-type="globals"><i class="fa fa-globe"></i> Globals</button>
					<button type="button" data-type="methods"><i class="fa fa-cube"></i> Methods</button>
					<button type="button" data-type="events"><i class="fa fa-bolt"></i> Events</button>
					<button type="button" data-type="errors"><i class="fa fa-exclamation-triangle"></i> ErrorCodes</button>
				</div>
			</div>
			
			<div class="label">
				<div class="type">Config</div>
				<div class="version">Version</div>
			</div>
		</div>
		
		<div class="sectionBody">
			<table class="listTable">
			<tbody>
				<?php for ($i=0, $loop=count($configs);$i<$loop;$i++) { $item = $configs[$i]; ?>
				<tr data-name="<?php echo strtolower($item->name); ?>" class="toggle">
					<td class="toggle" onclick="Apidocument.toggle(this);">
						<div class="icon">
							<i class="fa fa-caret-right"></i>
							<i class="fa fa-caret-down"></i>
						</div>
					</td>
					<td class="split"></td>
					<td class="content">
						<table>
						<tr>
							<td class="name" onclick="Apidocument.toggle(this);">
								<div class="name">
									<?php echo $item->is_required == true ? '<span class="required">REQ</span>' : ''; ?>
									<?php echo $item->property; ?>
									<?php echo $item->is_changed == true ? '<span class="changed">CHANGED '.$item->version.'</span>' : ''; ?>
									<?php echo $item->is_new == true ? '<span class="new">NEW</span>' : ''; ?>
								</div>
								<div class="description"><?php echo $item->description; ?></div>
							</td>
							<td class="version">
								<div class="version"><?php echo $item->defined; ?> <i class="fa fa-caret-up"></i><?php if ($item->deprecated) { ?> / <?php echo $item->deprecated; ?> <i class="fa fa-caret-down"></i><?php } ?></div>
								<div class="stability" data-stability="<?php echo $item->stability; ?>"><?php echo $item->stability; ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="content">
								<?php echo $item->content; ?>
								
								<?php if ($Module->checkPermission('write') == true) { ?>
								<div class="button">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$item->idx); ?>" class="btn btnWhite"><?php echo $Module->getLanguage('button/modify'); ?></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr data-name="<?php echo strtolower($item->name); ?>">
					<td colspan="4" class="split"></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<?php if (count($globals) > 0) { ?>
	<div class="ModuleApidocumentSection globals">
		<div class="sectionHeader">
			<div class="title">
				<i class="fa fa-globe"></i>Globals
				
				<div class="hidden-xs">
					<button type="button" data-type="configs"><i class="fa fa-cog"></i> Configs</button>
					<button type="button" data-type="properties"><i class="fa fa-tags"></i> Properties</button>
					<button type="button" data-type="globals"><i class="fa fa-globe"></i> Globals</button>
					<button type="button" data-type="methods"><i class="fa fa-cube"></i> Methods</button>
					<button type="button" data-type="events"><i class="fa fa-bolt"></i> Events</button>
					<button type="button" data-type="errors"><i class="fa fa-exclamation-triangle"></i> ErrorCodes</button>
				</div>
			</div>
			
			<div class="label">
				<div class="type">Method</div>
				<div class="version">Version</div>
			</div>
		</div>
		
		<div class="sectionBody">
			<table class="listTable">
			<tbody>
				<?php for ($i=0, $loop=count($globals);$i<$loop;$i++) { $item = $globals[$i]; ?>
				<tr data-name="<?php echo strtolower($item->name); ?>" class="toggle">
					<td class="toggle" onclick="Apidocument.toggle(this);">
						<div class="icon">
							<i class="fa fa-caret-right"></i>
							<i class="fa fa-caret-down"></i>
						</div>
					</td>
					<td class="split"></td>
					<td class="content">
						<table>
						<tr>
							<td class="name" onclick="Apidocument.toggle(this);">
								<div class="name">
									<?php echo $item->is_required == true ? '<span class="required">REQ</span>' : ''; ?>
									<?php echo $item->property; ?>
									<?php echo $item->is_changed == true ? '<span class="changed">CHANGED '.$item->version.'</span>' : ''; ?>
									<?php echo $item->is_new == true ? '<span class="new">NEW</span>' : ''; ?>
								</div>
								<div class="description"><?php echo $item->description; ?></div>
							</td>
							<td class="version">
								<div class="version"><?php echo $item->defined; ?> <i class="fa fa-caret-up"></i><?php if ($item->deprecated) { ?> / <?php echo $item->deprecated; ?> <i class="fa fa-caret-down"></i><?php } ?></div>
								<div class="stability" data-stability="<?php echo $item->stability; ?>"><?php echo $item->stability; ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="content">
								<?php echo $item->content; ?>
								
								<?php if ($Module->checkPermission('write') == true) { ?>
								<div class="button">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$item->idx); ?>" class="btn btnWhite"><?php echo $Module->getLanguage('button/modify'); ?></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr data-name="<?php echo strtolower($item->name); ?>">
					<td colspan="4" class="split"></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<?php if (count($methods) > 0) { ?>
	<div class="ModuleApidocumentSection methods">
		<div class="sectionHeader">
			<div class="title">
				<i class="fa fa-cube"></i>Methods
				
				<div class="hidden-xs">
					<button type="button" data-type="configs"><i class="fa fa-cog"></i> Configs</button>
					<button type="button" data-type="properties"><i class="fa fa-tags"></i> Properties</button>
					<button type="button" data-type="globals"><i class="fa fa-globe"></i> Globals</button>
					<button type="button" data-type="methods"><i class="fa fa-cube"></i> Methods</button>
					<button type="button" data-type="events"><i class="fa fa-bolt"></i> Events</button>
					<button type="button" data-type="errors"><i class="fa fa-exclamation-triangle"></i> ErrorCodes</button>
				</div>
			</div>
			
			<div class="label">
				<div class="type">Method</div>
				<div class="version">Version</div>
			</div>
		</div>
		
		<div class="sectionBody">
			<table class="listTable">
			<tbody>
				<?php for ($i=0, $loop=count($methods);$i<$loop;$i++) { $item = $methods[$i]; ?>
				<tr data-name="<?php echo strtolower($item->name); ?>" class="toggle">
					<td class="toggle" onclick="Apidocument.toggle(this);">
						<div class="icon">
							<i class="fa fa-caret-right"></i>
							<i class="fa fa-caret-down"></i>
						</div>
					</td>
					<td class="split"></td>
					<td class="content">
						<table>
						<tr>
							<td class="name" onclick="Apidocument.toggle(this);">
								<div class="name">
									<?php echo $item->is_required == true ? '<span class="required">REQ</span>' : ''; ?>
									<?php echo $item->property; ?>
									<?php echo $item->is_changed == true ? '<span class="changed">CHANGED '.$item->version.'</span>' : ''; ?>
									<?php echo $item->is_new == true ? '<span class="new">NEW</span>' : ''; ?>
								</div>
								<div class="description"><?php echo $item->description; ?></div>
							</td>
							<td class="version">
								<div class="version"><?php echo $item->defined; ?> <i class="fa fa-caret-up"></i><?php if ($item->deprecated) { ?> / <?php echo $item->deprecated; ?> <i class="fa fa-caret-down"></i><?php } ?></div>
								<div class="stability" data-stability="<?php echo $item->stability; ?>"><?php echo $item->stability; ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="content">
								<?php echo $item->content; ?>
								
								<?php if ($Module->checkPermission('write') == true) { ?>
								<div class="button">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$item->idx); ?>" class="btn btnWhite"><?php echo $Module->getLanguage('button/modify'); ?></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr data-name="<?php echo strtolower($item->name); ?>">
					<td colspan="4" class="split"></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<?php if (count($events) > 0) { ?>
	<div class="ModuleApidocumentSection events">
		<div class="sectionHeader">
			<div class="title">
				<i class="fa fa-bolt"></i>Events
				
				<div class="hidden-xs">
					<button type="button" data-type="configs"><i class="fa fa-cog"></i> Configs</button>
					<button type="button" data-type="properties"><i class="fa fa-tags"></i> Properties</button>
					<button type="button" data-type="globals"><i class="fa fa-globe"></i> Globals</button>
					<button type="button" data-type="methods"><i class="fa fa-cube"></i> Methods</button>
					<button type="button" data-type="events"><i class="fa fa-bolt"></i> Events</button>
					<button type="button" data-type="errors"><i class="fa fa-exclamation-triangle"></i> ErrorCodes</button>
				</div>
			</div>
			
			<div class="label">
				<div class="type">Method</div>
				<div class="version">Version</div>
			</div>
		</div>
		
		<div class="sectionBody">
			<table class="listTable">
			<tbody>
				<?php for ($i=0, $loop=count($events);$i<$loop;$i++) { $item = $events[$i]; ?>
				<tr data-name="<?php echo strtolower($item->name); ?>" class="toggle">
					<td class="toggle" onclick="Apidocument.toggle(this);">
						<div class="icon">
							<i class="fa fa-caret-right"></i>
							<i class="fa fa-caret-down"></i>
						</div>
					</td>
					<td class="split"></td>
					<td class="content">
						<table>
						<tr>
							<td class="name" onclick="Apidocument.toggle(this);">
								<div class="name">
									<?php echo $item->is_required == true ? '<span class="required">REQ</span>' : ''; ?>
									<?php echo $item->property; ?>
									<?php echo $item->is_changed == true ? '<span class="changed">CHANGED '.$item->version.'</span>' : ''; ?>
									<?php echo $item->is_new == true ? '<span class="new">NEW</span>' : ''; ?>
								</div>
								<div class="description"><?php echo $item->description; ?></div>
							</td>
							<td class="version">
								<div class="version"><?php echo $item->defined; ?> <i class="fa fa-caret-up"></i><?php if ($item->deprecated) { ?> / <?php echo $item->deprecated; ?> <i class="fa fa-caret-down"></i><?php } ?></div>
								<div class="stability" data-stability="<?php echo $item->stability; ?>"><?php echo $item->stability; ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="content">
								<?php echo $item->content; ?>
								
								<?php if ($Module->checkPermission('write') == true) { ?>
								<div class="button">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$item->idx); ?>" class="btn btnWhite"><?php echo $Module->getLanguage('button/modify'); ?></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr data-name="<?php echo strtolower($item->name); ?>">
					<td colspan="4" class="split"></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<?php if (count($errors) > 0) { ?>
	<div class="ModuleApidocumentSection errors">
		<div class="sectionHeader">
			<div class="title">
				<i class="fa fa-exclamation-triangle"></i>ErrorCodes
				
				<div class="hidden-xs">
					<button type="button" data-type="configs"><i class="fa fa-cog"></i> Configs</button>
					<button type="button" data-type="properties"><i class="fa fa-tags"></i> Properties</button>
					<button type="button" data-type="globals"><i class="fa fa-globe"></i> Globals</button>
					<button type="button" data-type="methods"><i class="fa fa-cube"></i> Methods</button>
					<button type="button" data-type="events"><i class="fa fa-bolt"></i> Events</button>
					<button type="button" data-type="errors"><i class="fa fa-exclamation-triangle"></i> ErrorCodes</button>
				</div>
			</div>
			
			<div class="label">
				<div class="type">Method</div>
				<div class="version">Version</div>
			</div>
		</div>
		
		<div class="sectionBody">
			<table class="listTable">
			<tbody>
				<?php for ($i=0, $loop=count($errors);$i<$loop;$i++) { $item = $errors[$i]; ?>
				<tr data-name="<?php echo strtolower($item->name); ?>" class="toggle">
					<td class="toggle" onclick="Apidocument.toggle(this);">
						<div class="icon">
							<i class="fa fa-caret-right"></i>
							<i class="fa fa-caret-down"></i>
						</div>
					</td>
					<td class="split"></td>
					<td class="content">
						<table>
						<tr>
							<td class="name" onclick="Apidocument.toggle(this);">
								<div class="name">
									<?php echo $item->is_required == true ? '<span class="required">REQ</span>' : ''; ?>
									<?php echo $item->property; ?>
									<?php echo $item->is_changed == true ? '<span class="changed">CHANGED '.$item->version.'</span>' : ''; ?>
									<?php echo $item->is_new == true ? '<span class="new">NEW</span>' : ''; ?>
								</div>
								<div class="description"><?php echo $item->description; ?></div>
							</td>
							<td class="version">
								<div class="version"><?php echo $item->defined; ?> <i class="fa fa-caret-up"></i><?php if ($item->deprecated) { ?> / <?php echo $item->deprecated; ?> <i class="fa fa-caret-down"></i><?php } ?></div>
								<div class="stability" data-stability="<?php echo $item->stability; ?>"><?php echo $item->stability; ?></div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="content">
								<?php echo $item->content; ?>
								
								<?php if ($Module->checkPermission('write') == true) { ?>
								<div class="button">
									<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write',$item->idx); ?>" class="btn btnWhite"><?php echo $Module->getLanguage('button/modify'); ?></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr data-name="<?php echo strtolower($item->name); ?>">
					<td colspan="4" class="split"></td>
				</tr>
				<?php } ?>
			</tbody>
			</table>
		</div>
	</div>
	<?php } ?>
	
	<?php if ($Module->checkPermission('write') == true) { ?>
	<table class="footerTable">
	<tr>
		<td></td>
		<td class="buttonRight">
			<a href="<?php echo $IM->getUrl($IM->menu,$IM->page,'write'); ?>" class="btn btnRed"><i class="fa fa-pencil"></i> <?php echo $this->getLanguage('button/write'); ?></a>
		</td>
	</tr>
	</table>
	<?php } ?>