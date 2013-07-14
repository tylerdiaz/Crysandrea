<?php foreach ($latest_topics as $topic): ?>
<li class="<?php echo ($topic['forum_name'] == "Announcements" ? "highlight" : ""); ?>">
	<a href="<?php echo site_url('topic/view/'.$topic['topic_id']) ?>">
		<img src="/images/avatars/<?php echo $topic['topic_author'] ?>_headshot.png" alt="" style="float:left; width:42px;height:42px;margin:-3px 5px 0 -2px;">
		<span class="db_topic_title"> <?php echo (strpos(strtolower($topic['topic_title']), 'think') || strpos(strtolower($topic['topic_title']), 'bandwagon') ? '<span class="label label-success">Wagon</span>' : '') ?>
 <?php echo stripslashes($topic['topic_title']) ?></span>
		<br />
		<p class="db_topic_info"><?php echo human_time($topic['last_post']) ?> <span>in <?php echo $topic['forum_name'] ?></span></p>
	</a>
</li>
<?php endforeach ?>