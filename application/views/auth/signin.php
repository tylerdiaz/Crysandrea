<div id="zoomit">
	<div class="ta-left">
		<h2>
			<?php if(validation_errors() || (isset($wrong_un_or_pw) && ($wrong_un_or_pw === TRUE))): ?>
				<img src="/images/icons/frown.png" alt="" width="22" height="22" />
				Oh no! Double check and give it another try
			<?php else: ?>
				<img src="/images/icons/happy.png" alt="" width="22" height="22" />
				Hey, you're back! Sign in to get started
			<?php endif; ?>
		</h2>

		<?php if (validation_errors()): ?>
			<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
		<?php endif ?>

		<?php if(isset($error_msg) && strlen($error_msg) > 0): ?>
			<?php $this->load->view('partials/notices/error.php', array('data' => '<li>'.$error_msg.'</li>')) ?>
		<?php endif ?>
		<form class="form-horizontal" method="POST" action="/auth/signin" style="margin:20px 15px 0 30px;">
			<?php if ($this->input->get('r')): ?>
				<input type="hidden" value="<?php echo $this->input->get('r') ?>" name="redirect" />
			<?php endif ?>
			<div class="control-group">
				<label class="control-label" for="username">Username or Email</label>
				<div class="controls"><input type="text" id="username" name="username" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls"><input type="password" id="password" name="password" /><small class="help-block"><a href="/auth/forgot_password">Forgot your password?</a></small></div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" name="remember_me"> Remember me
					</label>
					<button type="submit" class="main_button" id="signin_btn" autocomplete="off" data-toggle="button" data-loading-text="Signing in...">Sign in</button>
				</div>
			</div>
		</form>

		<div class="next_up">Signing in will redirect you to: <strong>http://crysandrea.com/<?php echo $this->input->get('r') ?></strong></div>
	</div>
</div>
<div style="text-align:center; color:#38697f; font-size:12px; opacity:0.8; margin:10px;">Don't have an account? You should <a href="/auth/signup">create your account</a> to get started!</div>