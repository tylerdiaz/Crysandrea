<?php $this->load->view('mailbox/mailbox_navigation', array('routes' => array('index' => '&lsaquo; &nbsp;Return to inbox'), 'active_url' => $active_url)); ?>

<div class="clearfix">
	<?php if (count($sub_messages) > 0): ?>
		<?php foreach ($sub_messages as $sub_mail): ?>
		<div class="grid_5 clearfix" style="padding:10px; border-bottom:1px solid #ccc; opacity:0.8;">
			<img src="/images/avatars/<?php echo $sub_mail['user_id'] ?>_headshot.png?<?php echo $sub_mail['last_saved_avatar'] ?>" alt="" class="avatar_headshot" />
			<div style="width:500px; float:left; background:transparent; padding:8px 2px 5px 10px;">
				<div class="clearfix">
					<strong class="left" style="font-size:14px; line-height:1.8"><a href="#"><?php echo $sub_mail['username'] ?></a> said:</strong>
					<span class="right" style="font-size:12px; color:#777; line-height:1.8">Received <?php echo _datadate($sub_mail['date'])?></span>
				</div>
				<p>
					<?php echo parse_bbcode(stripslashes(nl2br($sub_mail['message']))); ?>
				</p>
			</div>
		</div>
		<?php endforeach ?>
	<?php endif ?>
	<br clear="all" />
	<div class="clearfix" style="border-bottom:1px solid #ccc; margin-top:10px;">
		<div class="post-grid" id="<?php echo $mail['mail_id'] ?>">
			<div class="left">
				<a href="<?php echo site_url('user/'.urlencode($mail['username'])) ?>">
					<img src="/images/avatars/<?php echo $mail['user_id'] ?>.png?<?php echo $mail['last_saved_avatar'] ?>" alt="" class="topic_avatar" width="180" height="270" />
				</a>
			</div>
			<div class="post-toolbar">
		        <span class="left">Received <?php echo _datadate($mail['date'])?></span>
				<span class="right">
		    		<?php echo anchor('user/'.urlencode($mail['username']), 'View Profile') ?>
				</span>
			</div>
			<div class="post-content">
				<span class="post_author">
				    <span class="reply_at"><?php echo $mail['username'] ?></span> said:
				</span>
				<?php echo display_ribbons($mail, $this->system->userdata) ?>
				<?php echo parse_bbcode(stripslashes((nl2br($mail['message'])))); ?>
			</div>
			<div class="user_signature">
				<?php echo parse_bbcode(stripslashes(nl2br($mail['user_signature']))) ?>
			</div>
		</div>
	</div>

	<br />

	<form action="/mailbox/reply_message/<?php echo $mail['mail_id'] ?>" method="post" id="send_post_message" accept-charset="utf-8">
		<?php if (count($reply_to) > 1): ?>
		<div class="left" style="width:180px;">
			<strong>This reply includes...</strong>
			<ul style="list-style:none; margin:0">
				<?php foreach ($reply_to as $user_id => $username): ?>
				<li>
					<input type="checkbox" checked="yes" value="<?php echo $username ?>" name="to[]" checked id="check_<?php echo $username ?>" />
					<img src="/images/avatars/<?php echo $user_id ?>_headshot.png" width="20" height="20" style="margin-top:-2px" alt="" />
					<label style="vertical-align:middle; display:inline" for="check_<?php echo $username ?>"><?php echo $username ?></label>
				</li>
				<?php endforeach ?>
			</ul>
		</div>
		<?php else: ?>
			<input type="hidden" value="<?php echo $mail['username'] ?>" name="to" />
		<?php endif ?>

		<input type="hidden" value="<?php echo $mail['subject'] ?>" name="title" />

		<div class="right" style="width:540px">
			<textarea name="message" tabindex="1" class="input" id="message" style="width:97%; font-family:'lucida grande', arial, sans-serif; height:90px;" placeholder="What would you like to reply?"></textarea>
			<span class="right">
			    <input type="submit" tabindex="2" class="main_button" value="Reply to message" id="reply_message" />
			</span>
		</div>
	</form>
</div>
