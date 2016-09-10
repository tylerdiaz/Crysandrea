<div id="zoomit" style="background:#fff url(<?php echo ($this->input->get('avatar') ? '/auth/get_preview?fade=1' : '') ?>)no-repeat -10px 200px">
	<div class="ta-left">
		<h2>
			<?php if(validation_errors() || (isset($wrong_un_or_pw) && ($wrong_un_or_pw === TRUE))): ?>
				<img src="/images/icons/frown.png" alt="" width="22" height="22" />
				Oh no! Double check and give it another try
			<?php else: ?>
				<img src="/images/icons/happy.png" alt="" width="22" height="22" />
				<?php if ($this->input->get('avatar')): ?>
					Last step! Fill out your account information.
				<?php else: ?>
					Deciding to join us? Great decision!
				<?php endif ?>
			<?php endif; ?>
		</h2>

		<?php if (validation_errors()): ?>
			<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
		<?php endif ?>

		<?php if(isset($error_msg) && strlen($error_msg) > 0): ?>
			<?php $this->load->view('partials/notices/error.php', array('data' => '<li>'.$error_msg.'</li>')) ?>
		<?php endif ?>
		<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REQUEST_URI'] ?>" style="margin:20px 15px 0 30px;">
			<?php if ($this->input->get('r')): ?>
				<input type="hidden" value="<?php echo $this->input->get('r') ?>" name="redirect" />
			<?php endif ?>
			<div class="control-group">
				<label class="control-label" for="signup_username">Username</label>
				<div class="controls"><input type="text" id="signup_username" name="username" value="<?php echo set_value('username'); ?>" /><small class="help-block">Letters, numbers and spaces only.</small></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="signup_email">Your email</label>
				<div class="controls"><input type="text" id="signup_email" name="email" value="<?php echo set_value('email'); ?>" /><small class="help-block">Just in case you forget something.</small></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="signup_password">Password</label>
				<div class="controls"><input type="password" id="signup_password" name="password" /><small class="help-block"></small></div>
			</div>
			<?php if (count($security_question) > 0): ?>
			<div class="control-group">
				<label class="control-label" for="signup_bot_prevention">Bot prevention</label>
				<div class="controls"><input type="text" id="signup_bot_prevention" name="bot_prevention" /><small class="help-block" style="background:#ffc; color:#111;"><?php echo $security_question['question'] ?></small></div>
			</div>
			<?php endif ?>
			<?php if ($recaptcha): ?>
			<div class="control-group">
				<label class="control-label" for="recaptcha">Recaptcha</label>
				<div class="controls"><input type="text" id="recaptcha" name="recaptcha" /></div>
			</div>
			<?php endif ?>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="main_button" id="signup_btn" autocomplete="off" data-toggle="button" data-loading-text="Creating your account...">Create your account &nbsp;&rsaquo;</button>
					<small class="help-block" style="margin-top:10px">By signing up, you agree to our <a href="/general/read_document/tos">Terms of Service</a> and <a href="/general/read_document/privacy">Private Policy</a></small>
				</div>
			</div>
		</form>

	</div>
</div>
<!-- <div style="text-align:center; color:#38697f; font-size:12px; opacity:0.8; margin:10px;">Don't have an account? You should <a href="#">create your account</a> to get started!</div> -->