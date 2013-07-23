<?php $loaded_signatures = array(); ?>
<?php foreach($this->posts as $post): ?>
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
    		<?php if ($post['author_id'] == $this->session->userdata('user_id') || $this->system->is_staff()): ?>
    			| <a href="/forum/edit_post/<?php echo $post['post_id'] ?>">Edit</a>
    		<?php endif ?>
    		<?php if ($this->system->is_staff()): ?>
    		|
    			<form action="/forum/delete_post" style="display:inline; margin:0; padding:0" method="POST">
    				<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
    				<input type="hidden" name="post_id" value="<?php echo $post['post_id'] ?>" />
    				<button type="submit" class="btn btn-link" style="color:#777; padding:0; margin:-2px 0 0; display:inline; font-size:12px;">Delete</button>
    			</form>
    		<?php endif ?>
    		<?php echo user_online($post['last_action']) ?>
		</span>
	</div>
	<div class="post-content">
		<span class="post_author">
		    <a href="#message" class="reply_at" title="@<?= $post['username']?>:"><?php echo $post['username'] ?></a> said:
		</span>
		<?php echo display_ribbons($post, $this->system->userdata) ?>
		<?php echo parse_bbcode(stripslashes(nl2br($post['post_body']))); ?>
	</div>
	<div class="user_signature">
		<?php if ( ! in_array($post['username'], $loaded_signatures)): ?>
			<?php $loaded_signatures[] = $post['username'] ?>
			<?php echo parse_bbcode(stripslashes(nl2br($post['user_signature']))); ?>
		<?php endif ?>
	</div>
</div>
<?php $authors[] = $post['username'] ?>
<? endforeach; ?>