<?php $this->load->view('forum/forum_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<?php if(isset($topics[0])): ?>
	<table cellpadding="0" cellspacing="0" class="clean" id="topic_listing">
		<thead>
			<tr>
				<th>Topic title</th>
				<th width="190">Latest post</th>
			</tr>
		</thead>
		<?php foreach($topics as $topic): ?>
			<tr class="<?=cycle('', 'alt')?> <?=$topic['topic_type']?>">
				<td class="selectable">
					<img src="/images/avatars/<?php echo $topic['topic_author'] ?>_headshot.png?1340621489" alt="" style="float:left; width:42px;height:42px;margin:-5px 5px 0 -6px;">
					<?php if($topic['topic_status'] == "locked") echo '<img src="/images/icons/lock.png" alt="Locked:">'; ?>
					<?php if($topic['topic_type'] != "") echo '<strong>'.ucfirst($topic['topic_type']).': </strong>'; ?>

					<?php echo anchor('topic/view/'.$topic['topic_id'], stripslashes( $topic['topic_title'] ), 'class="main_link"') ?>
					<br />
					By: <a href="/user/<?php echo urlencode($topic['username']) ?>" class="quiet_link"><?php echo $topic['username']?></a> &ndash; <?php echo number_format($topic['total_posts']) ?> <?php echo (($topic['total_posts'] > 1) ? 'replies' : 'reply') ?>
				</td>
				<td>
					<span class="large"><?php echo human_time($topic['last_post'])?></span>
					<br />
					by <?php echo substr($topic['last_post_username'], 0, 18)?> |
					<?php echo anchor('topic/view/'.$topic['topic_id'].'/'. get_topic_page($topic['total_posts']).'/#footer', 'View post &rsaquo;'); ?>
				</td>

			</tr>
		<?php endforeach; ?>
	</table>
	<div class="row">
		<span class="left large breath"><?php echo $this->pagination->create_links();?></span>
	</div>
<?php else: ?>
	<br />
	<div class="empty_notice">
		No topics created. You should <a href="/forums">explore the forums</a> and create one!
	</div>
<?php endif; ?>