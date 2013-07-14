<?php $this->load->view('mailbox/mailbox_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<br />

<div class="grid_9" style="padding:3px 10px;">
	<?php if(validation_errors()): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
	<? endif; ?>
	<?php if ($error): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => $error)) ?>
	<?php endif ?>
	<?php echo form_open('/mailbox/create_message', array('id' => 'create_message')) ?>
		<div class="control-group">
			<label class="control-label" for="to" id="to_label">Send to:</label>
			<div class="controls"><input type="text" id="to" name="to" autocomplete="off" value="<?php echo (set_value('to') ? set_value('to') : $this->input->get('to')); ?>" class="input-xxlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="title" id="subject_label">Subject:</label>
			<div class="controls"><input type="text" id="title" name="title" value="<?php echo set_value('title'); ?>" class="input-xxlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="message">Message:</label>
			<div class="controls">
				<textarea name="message" id="message" cols="30" rows="10" class="input-xxlarge"><?php echo set_value('message') ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="main_button" id="signin_btn" data-toggle="button" data-loading-text="Sending message...">Send Message</button>
			</div>
		</div>
	</form>
</div>