<?php $this->load->view('account/account_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<div style="width:610px; margin:15px 55px; position:relative;">
	<?php if(validation_errors()): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
	<?php endif; ?>

	<?php foreach ($success as $key => $success_notice): ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong><?php echo repeater('More ', $key).($key == 0 ? 'G' : 'g') ?>ood news!</strong> <?php echo $success_notice ?>
		</div>
	<?php endforeach ?>

	<div style="margin-top:15px;">
		<?php echo form_open('/account', array('id' => 'save_settings', 'class' => 'form-horizontal')) ?>
			<div class="control-group">
				<label class="control-label" for="username">Username</label>
				<div class="controls"><input type="text" id="username" name="username" disabled value="<?php echo $user['username'] ?>" /><br /><small class="help-inline"><a href="#" data-dropdown="#dropdown-1" id="change_username_tip">Want to change your username?</a></small></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="email">Email</label>
				<div class="controls"><input type="email" id="email" name="email" value="<?php echo $user['user_email'] ?>" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="timezone">Timezone</label>
				<div class="controls">
					<?php echo form_dropdown('timezone', $timezones, $user_tz, 'id="timezone"'); ?><br />
					<small class="help-inline" id="timezone_confirmation"><a href="#" id="auto_timezone">Auto-suggest</a> &bull; Current time: <strong id="timezone_value"><?php echo gmdate("g:ia", time()+$user['timezone']) ?></strong></small>
				</div>
			</div>
			<div class="control-group" style="border-top:1px solid #e3e3e3; padding-top:20px;">
				<label class="control-label" for="new_password">New Password</label>
				<div class="controls"><input type="password" id="new_password" name="new_password" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="confirm_new_password">Confirm Password</label>
				<div class="controls"><input type="password" id="confirm_new_password" name="confirm_new_password" /></div>
			</div>
			<div class="control-group" style="border-top:1px solid #dd8; padding:20px 0 20px; background:#ffc;">
				<label class="control-label" for="current_password">Current Password</label>
				<div class="controls"><input type="password" id="current_password" class="password_lock" name="password" placeholder="Your password" /><small class="help-block">Remember to confirm your current password to save your changes!</small></div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="main_button" id="signin_btn" autocomplete="off" data-toggle="button" data-loading-text="Saving changes...">Save changes</button>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="dropdown-1" class="dropdown-menu has-tip" style="max-width:220px; padding:10px; line-height:1.4">
	<h5 style="background:#ff8; display:inline;">Changing your username will cost 1,000 palladium.</h5>
	<p style="font-size:12px; margin-top:10px"><strong>Why?</strong> Apart from the amount of work it takes, this is done to discourage people from abusing name changes. The fee will be applied only when your username has been changed.</p>
	<?php if ($user['user_palladium'] >= 1000): ?>
		<a href="#" id="change_username" class="main_button" style="color:white">OK, I still want to change it</a>
	<?php else: ?>
		<div style="text-align:center; color:#888; font-size:11px; margin-top:5px; border-top:1px solid #ccc; padding-top:5px;">You do not have enough Palladium for this.</div>
	<?php endif ?>
</div>

<script type="text/javascript">var hours = <?php echo gmdate("G", time()) ?>, mins = <?php echo gmdate("i", time()) ?></script>