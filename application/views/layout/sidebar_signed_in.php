<div id="sidebar_content">
	<div class="clearfix" id="user_card">
		<a href="/avatar">
			<img src="/images/avatars/<?php echo $this->session->userdata['user_id'] ?>_headshot.png?<?php echo $user['last_saved_avatar'] ?>" alt="" title="My avatar" width="52" height="52" class="avatar_headshot" style="margin:10px 9px 8px;" />
		</a>
		<div style="padding-top:14px; float:left">
			<a href="/profile" class="username"><?php echo $user['username'] ?></a>
			<ul id="usercard_navigation">
				<li id="my_profile"><a href="/user/<?php echo $user['username'] ?>">Your Profile</a></li>
				<li id="my_settings"><a href="/account">Settings</a></li>
			</ul>
		</div>
	</div>
	<div id="currency_counter" class="clearfix" style="font-size:15px; font-weight:bold; padding:5px 6px 15px 7px; text-shadow:1px 1px 0 rgba(255, 255, 255, 0.3); margin-bottom:5px; color:#334c00">
		<div style="width:100px; float:left; text-align:center;" id="palladium_count"><?php echo number_format($user['user_palladium']) ?></div>
		<div style="width:100px; float:left; text-align:center; margin-left:2px;" id="gem_count"><?php echo number_format($user['user_gems']) ?></div>
	</div>
	<ul id="sidebar_navigation">
		<li id="avatar"><a href="/avatar">Avatar</a></li>
		<li id="friends"><a href="/friends">Friends</a></li>
		<li id="mailbox"><a href="/mailbox">Mailbox <?php if ($unread_mail > 0): ?><span class="small_notification_bubble"><?php echo $unread_mail ?></span><?php endif ?></a></li>
		<li id="forest"><a href="/forest">Forest</a></li>
		<li id="market"><a href="/market">Market</a></li>
		<li id="trades"><a href="/trades">Trades</a></li>
	</ul>
	<?php if ($this->system->is_staff()): ?>
	<?php $unread_tickets = $this->db->get_where('staff_tickets', array('status' => 'pending'))->num_rows(); ?>
		<ul id="sidebar_navigation" style="border-top:1px solid #95ce81; background:rgba(0, 0, 0, 0.1)">
			<li id="staff_tickets"><a href="/staff_panel/tickets">Tickets <?php if ($unread_tickets > 0): ?><span class="small_notification_bubble"><?php echo $unread_tickets ?></span><?php endif ?></a></li>
			<li id="staff_infractions"><a href="/staff_panel/infractions">Warnings</a></li>
			<li id="staff_users"><a href="/staff_panel/users">Users</a></li>
		</ul>
	<?php endif ?>
	<?php if (count($this->system->userdata['online_friends']) > 3): ?>
		<div class="widget clearfix" id="friends_online">
			<h3><?php echo count($this->system->userdata['online_friends']) ?> friends online!</h3>
			<?php foreach ($this->system->userdata['online_friends'] as $friend): ?>
				<a href="/user/<?php echo urlencode($friend['username']) ?>" title="<?php echo $friend['username'] ?>">
					<img src="/images/avatars/<?php echo $friend['friend_id'] ?>_headshot.png" alt="" width="25" height="25" />
				</a>
			<?php endforeach; ?>
		</div>
	<?php endif ?>
	<div id="signout_box">
		<a href="/auth/signout">Sign out of Crysandrea</a>
	</div>
</div>

<style type="text/css">
#sidebar_navigation li a {
	position:relative;
}
.small_notification_bubble {
	background: #fc3f4b; /* Old browsers */
	background: -moz-linear-gradient(top,  #fc3f4b 0%, #cf000e 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fc3f4b), color-stop(100%,#cf000e)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #fc3f4b 0%,#cf000e 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #fc3f4b 0%,#cf000e 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #fc3f4b 0%,#cf000e 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #fc3f4b 0%,#cf000e 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fc3f4b', endColorstr='#cf000e',GradientType=0 ); /* IE6-9 */
	color:white;
	position:absolute;
	top:-5px;
	right:-4px;
	height:15px;
	width:15px;
	border-radius:3px;
	line-height:1;
	line-height:16px;
	text-align:center;
	font-family:helvetica, arial, sans-serif;
	box-shadow:0 1px 1px rgba(0, 0, 0, 0.2);
}
	.instant_notifications {
		overflow:hidden;
		margin:10px 0 0 -18px;
	}
	.instant_notifications li {
		margin:0;
		padding:0;
		overflow:hidden;
		list-style:none;
	}
	.instant_notifications li:hover a.remove_notification { display:block; }
	a.notification_bubble {
		display:block;
		background:#d9ebb7;
		margin:3px 0;
		padding:5px 7px;
		border-radius:4px;
		border:3px solid #75c279;
		overflow:hidden;
		font-size:11px;
		line-height:1.2;
		float:right;
		width:200px;
	}
	a.notification_bubble div.notification_content {
		width:158px;
		float:right;
	}
	a.notification_bubble:hover {
		background:#e1ebce;
		color:#b3680c;
	}
	a.remove_notification {
		display:none;
		float:left;
		margin:10px 0;
		text-align:center;
		line-height:16px;
		font-weight:bold;
		color:#999;
		background:rgba(0, 0, 0, 0.2);
		width:18px;
		height:18px;
		overflow:hidden;
	}
	a.remove_notification:hover { color:#fc4c53; background:rgba(0, 0, 0, 0.5); text-decoration:none; }
	.more_instant_notifications {
		display:block;
		background:#d9ebb7;
		margin:3px 0;
		padding:5px 7px;
		border-radius:4px;
		border:3px solid #75c279;
		overflow:hidden;
		font-size:11px;
		line-height:1.2;
		float:right;
		width:200px;
		text-align:center;
	}
</style>
<ul class="instant_notifications">
	<li class="instant_notification hide" id="notification_template">
		<a href="#" class="remove_notification">&times;</a>
		<a href="#" class="notification_bubble">
			<img src="" alt="" width="35" height="35" class="avatar_headshot" />
			<div class="notification_content"></div>
		</a>
	</li>
	<li class="instant_notification clearfix hide" id="notification_queue">
		<a href="#" class="more_instant_notifications">Show <span id="extended_notification_count">0</span> more notifications &#x25BE;</a>
	</li>
</ul>