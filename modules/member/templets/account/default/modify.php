<?php if ($step == 'verify') { ?>
<section class="login">
	<h4<?php echo $IM->getSite()->logo !== null ? ' style="background-image:url('.$IM->getSiteLogo().');"' : ''; ?>><?php echo $IM->getSiteTitle(); ?></h4>
	
	<p><?php echo $Module->getLanguage('verify/help/password/default'); ?></p>
	
	<div class="formBox">
		<div class="photo" style="background-image:url(<?php echo $member->photo; ?>);"></div>
		<div class="name"><?php echo $member->nickname; ?></div>
		<div class="email"><?php echo $member->email; ?></div>
		
		<div class="inputBlock">
			<input type="password" name="password" placeholder="<?php echo $Module->getLanguage('info/password'); ?>" class="inputControl">
			<div class="helpBlock"></div>
		</div>
	
		<button type="submit"><?php echo $Module->getLanguage('login/login'); ?></button>
	</div>
	<!--
	<ul>
		<li><a href="<?php echo $signupUrl; ?>">Member SignUp</a></li>
		<li><a href="<?php echo $findUrl; ?>">Forgot Password</a></li>
	</ul>
	-->
</section>
<?php } else { ?>
<section class="column">
	<aside>
		<h4><?php echo $Module->getLanguage('modify/default'); ?></h4>
		
		<p>
			서비스를 원활하게 사용하기 위하여 이 기본 정보를 항상 최신의 정보로 업데이트하여 주십시오.<br>
			입력된 개인정보는 개인정보보호정책에 따라 안전하게 보관됩니다.
		</p>
	</aside>
	
	<section>
		<div class="formBox">
			<div class="inputBox">
				<label><div><?php echo $Module->getLanguage('info/email'); ?></div></label>
				
				<div class="inputBlock clickBlock" onclick="Member.modify.modifyEmail();">
					<span class="text" data-name="email"><?php echo $member->email; ?></span>
					<i class="fa fa-angle-right"></i>
				</div>
			</div>
		</div>
		
		<div class="formBox">
			<div class="inputBox">
				<label><div><?php echo $Module->getLanguage('info/photo'); ?></div></label>
				
				<div class="inputBlock clickBlock" onclick="Member.modify.photoEdit();">
					<img id="ModuleMemberPhotoPreview" src="<?php echo $member->photo; ?>" class="memberPhoto">
					<i class="fa fa-angle-right"></i>
				</div>
			</div>
		</div>
		
		<div class="formBox">
			<div class="inputBox">
				<label><div><?php echo $Module->getLanguage('info/name'); ?></div></label>
				
				<div class="inputBlock">
					<input type="text" name="name" class="inputControl" value="<?php echo $member->name; ?>" required>
					<div class="helpBlock" data-default="<?php echo $Module->getLanguage('signup/help/name/default'); ?>" data-error="<?php echo $Module->getLanguage('signup/help/name/error'); ?>" data-success="<?php echo $Module->getLanguage('signup/help/name/success'); ?>"></div>
				</div>
			</div>
			
			<div class="inputBox">
				<label><div><?php echo $Module->getLanguage('info/nickname'); ?></div></label>
				
				<div class="inputBlock">
					<input type="text" name="nickname" class="inputControl" value="<?php echo $member->nickname; ?>" required>
					<div class="helpBlock" data-default="<?php echo $Module->getLanguage('signup/help/nickname/default'); ?>" data-error="<?php echo $Module->getLanguage('signup/help/nickname/error'); ?>" data-success="<?php echo $Module->getLanguage('signup/help/nickname/success'); ?>" data-duplicated="<?php echo $Module->getLanguage('signup/help/nickname/duplicated'); ?>"></div>
				</div>
			</div>
			
			<button type="submit" data-loading="<?php echo $Module->getLanguage('signup/loading'); ?>"><?php echo $Module->getLanguage('button/confirm'); ?></button>
		</div>
	</section>
</section>

<?php } ?>
<!--
<div class="ModuleMemberModifyDefault">
	<div class="row">
		<div class="col-sm-5">
			<?php if (file_exists(__IM_PATH__.'/externals/signup.php') == true) INCLUDE __IM_PATH__.'/externals/member.modify.php'; ?>
		</div>
	
		<div class="col-sm-7">
			<?php if ($step == 'verify') { ?>
			<h4><?php echo $Module->getLanguage('verify/title'); ?></h4>
			
			<table class="formTable">
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/password_verify'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="password" name="password" class="inputControl" style="width:250px;" required>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('verify/help/password/default'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="splitBottom">
				<td colspan="2"><div></div></td>
			</tr>
			</table>
			
			<div class="buttons">
				<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('verify/loading'); ?>"><?php echo $Module->getLanguage('button/confirm'); ?></button>
			</div>
			
			<?php } else { ?>
			<h4><?php echo $Module->getLanguage('modify/default'); ?></h4>
	
			<table class="formTable">
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/photo'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="hidden" name="photo">
						<img id="ModuleMemberPhotoPreview" src="<?php echo $member->photo; ?>" class="memberPhoto">
						<button type="button" onclick="Member.modify.photoEdit();" class="btn btnRed"><i class="fa fa-photo"></i> <?php echo $Module->getLanguage('button/modifyPhoto'); ?></button>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/email'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="text" name="email" class="inputControl" style="width:250px;" value="<?php echo $member->email; ?>" data-value="<?php echo $member->email; ?>" readonly>
						<button type="button" class="btn btnWhite" onclick="Member.modify.modifyEmail();"><i class="fa fa-envelope-o"></i> <?php echo $Module->getLanguage('button/modify_email'); ?></button>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/password'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="password" name="password" class="inputControl" style="width:250px;" required>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('signup/help/password/default'); ?>" data-error="<?php echo $Module->getLanguage('signup/help/password/error'); ?>" data-success="<?php echo $Module->getLanguage('signup/help/password/success'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/name'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="text" name="name" class="inputControl" style="width:250px;" required>
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('signup/help/name/default'); ?>" data-error="<?php echo $Module->getLanguage('signup/help/name/error'); ?>" data-success="<?php echo $Module->getLanguage('signup/help/name/success'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('info/nickname'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<input type="text" name="nickname" class="inputControl" style="width:250px;">
						<div class="helpBlock" data-default="<?php echo $Module->getLanguage('signup/help/nickname/default'); ?>" data-error="<?php echo $Module->getLanguage('signup/help/nickname/error'); ?>" data-success="<?php echo $Module->getLanguage('signup/help/nickname/success'); ?>" data-duplicated="<?php echo $Module->getLanguage('signup/help/nickname/duplicated'); ?>"></div>
					</div>
				</td>
			</tr>
			<tr class="splitBottom">
				<td colspan="2"><div></div></td>
			</tr>
			</table>
			
			<div class="buttons">
				<button type="submit" class="btn btnRed" data-loading="<?php echo $Module->getLanguage('signup/loading'); ?>"><?php echo $Module->getLanguage('button/confirm'); ?></button>
			</div>
			
			<h4><?php echo $Module->getLanguage('modify/social'); ?></h4>
	
			<table class="formTable">
			<tr>
				<td class="label"><?php echo $Module->getLanguage('social/google/name'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<?php if ($Module->getSocialAuth('google') == null) { ?>
						<a href="<?php echo $IM->getProcessUrl('member','google'); ?>" class="btn btnGoogle"><i class="fa fa-google-plus"></i> <?php echo $Module->getLanguage('social/google/connect'); ?></a>
						<?php } else { ?>
						<a href="#" class="btn btnGoogle"><i class="fa fa-google-plus"></i> <?php echo $Module->getLanguage('social/google/disconnect'); ?></a>
						<?php } ?>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('social/facebook/name'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<?php if ($Module->getSocialAuth('facebook') == null) { ?>
						<a href="<?php echo $IM->getProcessUrl('member','facebook'); ?>" class="btn btnFacebook"><i class="fa fa-facebook-f"></i> <?php echo $Module->getLanguage('social/facebook/connect'); ?></a>
						<?php } else { ?>
						<a href="#" class="btn btnFacebook"><i class="fa fa-facebook-f"></i> <?php echo $Module->getLanguage('social/facebook/disconnect'); ?></a>
						<?php } ?>
					</div>
				</td>
			</tr>
			<tr class="split">
				<td colspan="2"></td>
			</tr>
			<tr>
				<td class="label"><?php echo $Module->getLanguage('social/github/name'); ?></td>
				<td class="input">
					<div class="inputBlock">
						<?php if ($Module->getSocialAuth('github') == null) { ?>
						<a href="<?php echo $IM->getProcessUrl('member','github'); ?>" class="btn btnGithub"><i class="fa fa-github-alt"></i> <?php echo $Module->getLanguage('social/github/connect'); ?></a>
						<?php } else { ?>
						<a href="#" class="btn btnGithub"><i class="fa fa-github-alt"></i> <?php echo $Module->getLanguage('social/github/disconnect'); ?></a>
						<?php } ?>
					</div>
				</td>
			</tr>
			<tr class="splitBottom">
				<td colspan="2"><div></div></td>
			</tr>
			</table>
			<?php } ?>
		</div>
	</div>
</div>
-->