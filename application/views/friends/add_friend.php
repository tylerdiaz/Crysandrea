<?php $this->load->view('friends/friend_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<div style="margin:10px 0">
	<?php if ($this->input->get('sent') !== FALSE): ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>Awesome!</strong> Your friend request has been sent.
		</div>
	<?php endif ?>

	<?php if ($this->input->get('missing') !== FALSE): ?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<strong>Oops:</strong> The user you tried to add as a friend could not be found, could you make sure you typed their username correctly?
		</div>
	<?php endif ?>

	<div class="grid_4">
		<form action="/friends/add_friend" method="POST" id="add_friend_form">
			<label for="friend_username" class="alt_label">Find a user:</label>
			<input type="text" name="friend_username" id="friend_username" placeholder="Username" value="<?php echo urldecode($this->input->get('username')) ?>"/><br />
			<button type="submit" class="main_button" name="friend_request_btn" id="friend_request_btn">Send friend request</button>
		</form>
	</div>
	<div class="grid_2">
	</div>
</div>