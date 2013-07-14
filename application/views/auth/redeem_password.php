<div id="zoomit">
	<div class="ta-left">
		<h2>
			<img src="/images/icons/unlock.png" alt="" width="22" height="22" />
			One last step and you're new password is set
		</h2>
		<?php if ($form == 'completed'): ?>
			<?php $this->load->view('partials/notices/success.php', array('data' => 'Your new password has been set, you can now sign in with it!', 'header' => 'Yay, you\'re all done!')); ?>
		<?php else: ?>
			<?php if(validation_errors()): ?>
				<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
			<? endif; ?>

			<form class="form-horizontal" method="POST" action="<?php echo $this->uri->ruri_string() ?>/?user=<?php echo $this->input->get('user') ?>" style="margin:18px 15px 0 30px;">
				<div class="next_up" style="margin:0px 0 20px;"><span class="label label-info">Tip</span> Make your password easy to remember and tough to guess. Need some ideas on how to do this? <a target="_window" href="http://preshing.com/20110811/xkcd-password-generator">This web comic has a good idea</a>.</div>
				<div class="control-group">
					<label class="control-label" for="rpassword">New password</label>
					<div class="controls"><input type="password" id="rpassword" name="new_password" /></div>
				</div>
				<div class="control-group">
					<label class="control-label" for="rnew_password" style="font-size:12px;">Confirm new password</label>
					<div class="controls"><input type="password" id="rnew_password" name="confirm_new_password" /></div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="main_button" id="redeem_password_btn" autocomplete="off" data-toggle="button" data-loading-text="Saving new password...">Set new password</button>
					</div>
				</div>
			</form>
		<?php endif ?>
	</div>
</div>