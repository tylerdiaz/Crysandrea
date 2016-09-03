<?php $this->load->view('account/account_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<div style="margin:15px 80px; position:relative;">
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
		<?php echo form_open('/account/signature', array('id' => 'save_signature')) ?>
			<div class="control-group">
				<label class="control-label" for="forum_signature">Your forum signature: <strong style="background:#ffc; font-size:12px;">(<span id="chars_left"><?php echo 255-strlen($user['user_signature']) ?></span> characters left)</strong>:</label>
				<div class="controls">
					<textarea name="user_signature" id="forum_signature" cols="30" rows="8" class="input-xxlarge" style="resize:vertical;"><?php echo stripslashes($user['user_signature']) ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="main_button" id="save_signature_btn" autocomplete="off" data-toggle="button" data-loading-text="Saving changes...">Save signature</button>
				</div>
			</div>
		</form>
	</div>
</div>