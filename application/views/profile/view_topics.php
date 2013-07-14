<div class="feature_header">
	<h2 class="forums_icon"><?php echo $page_title ?></h2>
	<ul class="feature_navigation">
		<li>
			<a href="/user/<?php echo $username ?>">&lsaquo; Return to <?php echo $username ?>'s profile</a>
		</li>
	</ul>
</div>
<div class="row">
	<div class="grid_6 clearfix" style="padding:10px; border-bottom:1px solid #ccc; opacity:0.8;">
		<img src="/images/avatars/<?php echo $user_id ?>_headshot.png?1340621489" alt="" class="avatar_headshot" />
		<div style="width:630px; float:left; background:transparent; padding:8px 2px 5px 10px;">
			<?php foreach ($topics as $topic): ?>
			<div class="clearfix">
				<strong class="left" style="font-size:14px; line-height:1.8">
					(<?php echo $topic['short_name'] ?>) <a href="/topic/view/<?php echo $topic['topic_id'] ?>"><?php echo $topic['topic_title'] ?></a>
				</strong>
				<span class="right" style="font-size:12px; color:#777; line-height:1.8">Posted <?php echo human_time($topic['topic_time']) ?></span>
			</div>
			<?php endforeach ?>
		</div>
	</div>
<div class="row">
	<span class="left large breath"><?php echo $this->pagination->create_links();?></span>
</div>

</div>
<br clear="all" />