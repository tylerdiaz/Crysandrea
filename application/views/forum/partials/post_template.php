<div class="post-grid" id="<?= $post['post_id'] ?>">
	<div class="left">
		<a href="<?php echo site_url('user/'.urlencode($post['username'])) ?>">
			<img src="/images/avatars/<?php echo $post['user_id'] ?>.png" alt="" class="topic_avatar" width="180" height="270" />
		</a>
	</div>
	<div class="post-toolbar">
        <span class="left">Posted <?php echo _datadate($post['post_time'])?></span>
		<span class="right">
		<a href="#message" onclick="quote_post(<?= urlencode($post['post_id']);?>);" title="Quote post">Quote</a> |
    		<?php echo anchor('user/'.urlencode($post['username']), 'View Profile') ?>
    		<?php echo user_online($post['last_action']) ?>
		</span>
	</div>
	<div class="post-content">
		<span class="post_author">
		    <a href="#message" class="reply_at" title="@<?= $post['username']?>:"><?= $post['username'] ?></a> said:
		</span>
		<?php echo display_ribbons($post, $this->system->userdata) ?>
		<?php echo parse_bbcode(stripslashes((nl2br($post['post_body'])))); ?>
	</div>
	<div class="user_signature">
		<?php echo parse_bbcode(stripslashes(nl2br($post['user_signature']))) ?>
	</div>
</div>