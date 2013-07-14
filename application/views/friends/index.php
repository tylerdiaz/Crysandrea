<style type="text/css">
	.friend_actions {
		position: relative;
		background: #fff;
		border: 2px solid #cdd19c;
		padding:9px 12px;
		border-radius:5px;
	}
	.friend_actions:after, .friend_actions:before {
		bottom: 100%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
	}

	.friend_actions:after {
		border-color: rgba(255, 255, 255, 0);
		border-bottom-color: #fff;
		border-width: 5px;
		left: 5%;
		margin-left: -5px;
	}
	.friend_actions:before {
		border-color: rgba(205, 209, 156, 0);
		border-bottom-color: #cdd19c;
		border-width: 8px;
		left: 5%;
		margin-left: -8px;
	}
	a.remove_friend, span.remove_confirm {
		color:#607990;
		font-size:11px;
		display:inline-block;
		margin:17px 5px 0;
	}
	a.remove_friend:hover {
		color:red;
		text-decoration:none;
	}
	.friend_bunch { margin:10px 0 0; overflow:hidden }
	.friend_bunch li {
		float:left;
		list-style:none;
	}
	.friend_bunch li a {
		display:block;
		width:81px;
		background:#fff;
		overflow:hidden;
		height:71px;
		border:1px solid #e7da79;
		text-align:center;
		margin:5px;
		font-size:12px;
		padding:10px 5px;
		border-radius:5px;
		-webkit-transition: all 100ms ease;
		-moz-transition: all 100ms ease;
		-ms-transition: all 100ms ease;
		-o-transition: all 100ms ease;
		transition: all 100ms ease;
		box-shadow:none;
	}
	.friend_bunch li a:hover {
		border-color:orange;
		box-shadow:0 0 2px 1px #ff0;
	}
	.friend_bunch li a:active {
		border-color:#aaa;
		box-shadow:none;
		opacity:0.8;
	}
	.username_data {
		font-size:16px; padding:4px 0 2px;
	}
	#user_timestamp {
		font-size:11px; font-weight:normal
	}
	#user_avatar {
		margin:-25px 0 -200px 0; float:left; position:absolute;
	}
	.friend_request_header {
		display:block;
		line-height:1.8;
	}
</style>

<?php $this->load->view('friends/friend_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<?php if ($this->input->get('acceped') !== FALSE): ?>
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<strong>Awesome!</strong> You are now friends with <?php echo urldecode($this->input->get('u')) ?>.
	</div>
<?php endif ?>

<?php if ($this->input->get('removed') !== FALSE): ?>
	<div class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<strong>Thought we'd let you know:</strong> You've successfully removed the friend. Hopefully you guys can work things out one day. :(
	</div>
<?php endif ?>

<?php if (count($requests) > 0): ?>
	<?php foreach ($requests as $request): ?>
		<div style="margin:10px 0 5px;">
			<div class="alert alert-block">
				<img src="/images/avatars/<?php echo $request['user_id'] ?>_headshot.png" alt="" class="avatar_headshot" height="60" width="60" style="margin:-10px 10px 0 -5px;">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<strong class="friend_request_header"><a href="/user/<?php echo urlencode($request['username']) ?>"><?php echo $request['username'] ?></a> has sent you a friend reqest</strong>
				<form action="/friends/accept_request/" style="display:inline" method="POST">
					<button class="tiny_button" type="submit">Accept request</button>
					<input type="hidden" name="friendship_id" value="<?php echo $request['friendship_id'] ?>" />
					<input type="hidden" name="user_id" value="<?php echo $request['user_id'] ?>" />
				</form> or <a href="/friends/ignore_request/<?php echo $request['friendship_id'] ?>">Ignore</a>
			</div>
		</div>
	<?php endforeach ?>
<?php endif ?>

<?php if (count($friends) > 0): ?>
	<div class="feature_content">
		<div style="height:185px; width:727px; background:url(/images/banners/friend_background.jpg); overflow:hidden; position:relative;">
			<canvas style="height:140px; width:170px; position:absolute; bottom:0px; left:10px;"></canvas>
			<img src="/images/avatars/unknown_avatar.png" width="180" height="270" alt="" id="user_avatar" />
			<div style="float:right; width:530px; margin-right:10px;">
				<div class="clearfix">
					<h3 class="left username_data"><span id="data_username">Username</span> <span id="user_timestamp">Last seen online 30 minutes ago</span></h3>
					<a href="#" class="right remove_friend"><strong>&times;</strong> Remove friend</a>
				</div>
				<div class="friend_actions">
					<a href="#" class="profile_link">View Profile</a> &bull;
					<a href="#" class="send_trade">Send Trade</a> &bull;
					<a href="#" class="send_message">Send Message</a>
				</div>
			</div>
		</div>
		<div style="text-align:center; margin:12px 0 2px;">
			<img src="/images/headers/friend_instruction.jpg" alt="" id="friend_instructions" />
		</div>
		<ul class="friend_bunch">
			<?php foreach ($friends as $friend): ?>
				<li>
					<a href="#" data-userid="<?php echo $friend['friend_id'] ?>">
						<img src="/images/avatars/<?php echo $friend['friend_id'] ?>_headshot.png?1340621489" alt=""><br /><?php echo $friend['username'] ?>
					</a>
				</li>
			<?php endforeach ?>
		</ul>
	</div>
<?php else: ?>
	<br />
	<div class="empty_notice">
		No friends to show. Why don't you <a href="/forums">explore the forums</a> and meet someone?
	</div>
<?php endif ?>