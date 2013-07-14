<div class="feature_header">
	<h2 class="forums_icon"><?php echo $page_title ?></h2>
	<ul class="feature_navigation">
		<li>
			<a href="/user/<?php echo $username ?>">&lsaquo; Return to <?php echo $username ?>'s profile</a>
		</li>
	</ul>
</div>
<div class="row">
<?php foreach ($posts as $post): ?>
	<div class="grid_6 clearfix" style="padding:10px; border-bottom:1px solid #ccc; opacity:0.8;">
		<img src="/images/avatars/<?php echo $user_id ?>_headshot.png?1340621489" alt="" class="avatar_headshot" />
		<div style="width:630px; float:left; background:transparent; padding:8px 2px 5px 10px;">
			<div class="clearfix">
				<strong class="left" style="font-size:14px; line-height:1.8">
					Posted in <a href="/topic/view/<?php echo $post['tp_id'] ?>/<?php echo (floor($post['tps_id']/12)*12).'#'.$post['ps_id'] ?>"><?php echo $post['topic_title'] ?></a>
				</strong>
				<span class="right" style="font-size:12px; color:#777; line-height:1.8">Posted <?php echo human_time($post['post_time']) ?></span>
			</div>
			<p style="color:#333;">
				<?php echo parse_bbcode(stripslashes((nl2br($post['post_body'])))); ?>
			</p>
		</div>
	</div>
<?php endforeach ?>
<div class="row">
	<span class="left large breath"><?php echo $this->pagination->create_links();?></span>
</div>

</div>
<br clear="all" />