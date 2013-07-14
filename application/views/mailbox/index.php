<?php $this->load->view('mailbox/mailbox_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<style type="text/css">
	select.mail_dropdown {
		padding:1px 2px;
		height:auto;
	}
</style>
<?php if ($this->input->get('success')): ?>
	<?php $this->load->view('partials/notices/success', array('header' => 'Your message has been set!', 'data' => htmlentities($this->input->get('success')))) ?>
<?php endif ?>
<?php if ($this->input->get('error')): ?>
	<?php $this->load->view('partials/notices/error', array('data' => htmlentities($this->input->get('error')))) ?>
<?php endif ?>
<?php if ($total_mail > $max_mail): ?>
	<?php $this->load->view('partials/notices/notice', array('data' => 'Your inbox is filled up beyond the capacity limit. On '.date('M', time()).' 21st, we will delete the oldest messages from inboxes till they meet the capacity. If you have some valuable PM\'s, we suggest you save them in your savebox. <a href="/mailbox/backup_inbox">Here is a file containing all your inbox messages in case you would like to save them</a>.')) ?>
<?php endif ?>
<div class="clearfix" style="margin:10px 0">
	<div class="left" style="color:#888; padding:8px 5px 0;">Your inbox is <?php echo round(($total_mail/$max_mail)*100) ?>% full (<?php echo $total_mail ?>/<?php echo $max_mail ?> used)</div>
	<a href="/mailbox/create_message" class="main_button right">Create a message</a>
</div>
<?php if (count($messages) > 0): ?>
<form method="post" action="/mailbox/index/" style="margin:0; padding:0; display:inline;">
<div class="table-organizer">
	<strong class="left" style="font-size:15px; padding:3px 5px; color:#B4D676; text-shadow:-1px -1px 0 #0D190B">Your messages:</strong>
	<select class="mail_dropdown right" onChange="this.form.submit();" name="action1">
		<option value="none" selected>Do with selected...</option>
		<option value="delete">Delete mail</option>
		<option value="read">Mark as read</option>
		<option value="unread">Mark as unread</option>
		<?php if($this->uri->segment(2) == 'saved'): ?>
			<option value="unsave">Unsave message</option>
		<?php else: ?>
			<option value="save">Save message</option>
		<?php endif; ?>
	</select>

	<table cellpadding="0" cellspacing="0" class="organized" style="margin:5px 0">
		<thead>
			<tr>
				<th width="18"></th>
				<th>Title</th>
				<th width="160"><?php echo (strpos($page_body, 'outbox') ? 'To' : 'From') ?></th>
				<th width="170">Received</th>
				<th width="18"><input type="checkbox" name="checkall" onclick="checkUncheckAll(this);" size="20" /></th>
			</tr>
		</thead>
			{messages}
				<tr class="{cycle} {status}" id="{mail_id}">
					<td class="icon alt2">{unread_img}</td>
					<td><a href="/mailbox/view_message/{mail_id}">{subject}</a></td>
					<td class="alt2">{sender}</td>
					<td>{timestamp}</td>
					<td class="alt2"><input type="checkbox" name="mail[]" value="{mail_id}" class="checker" /></td>
				</tr>
			{/messages}
	</table>

	<div class="clearfix">
		<span class="left tree_paginate">
			<?php echo $this->pagination->create_links(); ?>
		</span>

		<?php if (count($messages) > 5): ?>
		<select class="mail_dropdown right" onChange="this.form.submit();" name="action2" style="margin-top:5px">
			<option value="none" selected>Do with selected...</option>
			<option value="delete">Delete mail</option>
			<option value="read">Mark as read</option>
			<option value="unread">Mark as unread</option>
			<?php if($this->uri->segment(2) == 'saved') : ?>
				<option value="unsave">Unsave message</option>
			<?php else: ?>
				<option value="save">Save message</option>
			<?php endif; ?>
		</select>
		<?php endif; ?>
	</div>
	</div>
</form>
<?php else: ?>

<div class="empty_notice">
	Hooray! You have no mail to attend to, check back later. :D
</div>
<?php endif ?>
