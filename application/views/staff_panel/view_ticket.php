<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'staff_panel')); ?>
<div class="clearfix">

<?php if ($ticket_data['status'] == 'solved'): ?>
	<div class="alert alert-success" style="margin-top:5px;">
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	  <?php
	  	$timestamp_delta = strtotime($ticket_data['solved_at'])-strtotime($ticket_data['timestamp']);
	  	$total_minutes = round($timestamp_delta/60);
	  	$total_hours = round($total_minutes/60, 1);
 	  ?>
	  <strong>Ticket closed</strong> (<?php echo (($total_hours) < 1 ? $total_minutes.' minute' : $total_hours.' hour') ?> wait): This ticket was solved <?php echo _datadate($ticket_data['solved_at'])?> by <?php echo $ticket_data['attended_by'] ?>
	</div>
<?php endif ?>

<div class="grid_5 clearfix" style="padding:10px; border-bottom:1px solid #ccc; ">
	<img src="/images/avatars/<?php echo $ticket_data['user_id'] ?>_headshot.png?<?php echo $ticket_data['last_saved_avatar'] ?>" alt="" class="avatar_headshot" />
	<div style="width:500px; float:left; background:transparent; padding:8px 2px 5px 10px;">
		<div class="clearfix">
			<strong class="left" style="font-size:14px; line-height:1.8"><a href="/user/<?php echo urlencode($ticket_data['username']) ?>"><?php echo $ticket_data['username'] ?></a> said:</strong>
			<span class="right" style="font-size:12px; color:#777; line-height:1.8">Received <?php echo _datadate($ticket_data['timestamp'])?></span>
		</div>
		<p>
			<?php echo parse_bbcode(stripslashes(nl2br($ticket_data['description']))); ?>
		</p>
	</div>
</div>

<?php if (strlen($ticket_data['url']) > 0): ?>
	<br clear="all" />
	<div style="padding:10px 10px 0px 90px;">
		<strong>Related link:</strong> <a href="<?php echo $ticket_data['url'] ?>"><?php echo $ticket_data['url'] ?></a><br clear="all" />
	</div>
<?php endif ?>

<?php if (count($reply_message) > 1): ?>
	<div class="grid_5 clearfix" style="padding:10px; border-bottom:1px solid #ccc; ">
		<img src="/images/avatars/<?php echo $reply_message['sender'] ?>_headshot.png?<?php echo $reply_message['last_saved_avatar'] ?>" alt="" class="avatar_headshot" />
		<div style="width:500px; float:left; background:transparent; padding:8px 2px 5px 10px;">
			<div class="clearfix">
				<strong class="left" style="font-size:14px; line-height:1.8"><a href="/user/<?php echo urlencode($reply_message['username']) ?>"><?php echo $reply_message['username'] ?></a> said:</strong>
				<span class="right" style="font-size:12px; color:#777; line-height:1.8">Received <?php echo _datadate($reply_message['date'])?></span>
			</div>
			<p>
				<?php echo parse_bbcode(stripslashes(nl2br($reply_message['message']))); ?>
			</p>
		</div>
	</div>
<?php else: ?>
	<?php if ($ticket_data['status'] == 'solved'): ?>
		<br clear="all" />
		<div class="alert" style="margin-top:5px;">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Ticket closed:</strong> This ticket was closed with no reply.
		</div>
	<?php endif ?>
<?php endif ?>

<?php if ($ticket_data['status'] == 'pending'): ?>
	<form method="POST" action="/staff_panel/reply_ticket/<?php echo $ticket_data['ticket_id'] ?>" class="right" style="width:540px; margin-top:10px; margin-right:100px;">
<textarea name="message" tabindex="1" id="message" style="width:97%; font-family:'lucida grande', arial, sans-serif; height:90px;" placeholder="What would you like to reply?">Hello <?php echo $ticket_data['username'] ?>,

</textarea>
		<div class="clearfix">
			<div class="left span4">
				<label class="checkbox">
					<input type="checkbox" name="resolve" checked="checked"> Resolve this ticket
			    </label>
			</div>
			<div class="right span4">
				<button type="submit" class="btn btn-primary right">Reply to ticket &rsaquo;</button>
				<button type="submit" class="btn right" style="margin-right:5px;" id="solved_ticket"><i class="icon-ok-circle"></i> Close</button>
			</div>
		</div>

		<input type="hidden" name="auto_solve" id="auto_solve" value="no" />
	</form>
	<script type="text/javascript">
		setTimeout(function(){
			$('#solved_ticket').on('click', function(){
				if (confirm('Are you sure you want to close this ticket without a response?')) {
					$('#auto_solve').val('yes');
				} else {
					return false;
				}
			});
		}, 500);
	</script>
<?php endif ?>
</div>

<?php if ($ticket_data['status'] == 'solved'): ?>
	<form method="POST" action="/staff_panel/unlock_ticket/<?php echo $ticket_data['ticket_id'] ?>" class="right" style="width:540px; margin-top:10px; margin-right:100px;">
		<button type="submit" class="btn right"><i class="icon-lock"></i> Re-open Ticket</button>
	</form>
<?php endif; ?>
<br clear="all" />