<div id="zoomit">
	<div class="ta-left">
		<h2>
			<img src="/images/icons/unlock.png" alt="" width="22" height="22" />
			Forgot your password? We can help!
		</h2>
		<?php if ($sent): ?>
			<?php $this->load->view('partials/notices/success.php', array('data' => 'You should soon receive and email on <strong>'.$this->input->post('email').'</strong> with instructions on how to reset your password. In case you haven\'t received it, try checking your spam box or email our admin: admin@crysandrea.com', 'header' => 'Your email has been sent!')); ?>
		<?php else: ?>
			<?php if(validation_errors()): ?>
				<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
			<? endif; ?>

			<form class="form-horizontal" method="POST" action="/auth/forgot_password" style="margin:15px 15px 0 30px;">
				<div class="next_up" style="margin:0px 0 15px;"><span class="label label-info">Tip</span> Submit your email and we'll send step-by-step instructions on how reset your account's password.</div>
				<div class="control-group">
					<label class="control-label" for="email">Enter your email:</label>
					<div class="controls"><input type="text" id="email" name="email" /></div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="main_button" id="reset_password_btn" autocomplete="off" data-toggle="button" data-loading-text="Sending instructions...">Send me the instructions &rsaquo;</button>
					</div>
				</div>
			</form>
		<?php endif ?>
	</div>
</div>