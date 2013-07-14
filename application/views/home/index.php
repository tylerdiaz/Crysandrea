<style type="text/css">
	#welcome_header {
		font-size:20px;
		line-height:1.7;
		margin:0;
		font-family:Arial;
	}
	.dashboard_widget {
		margin-bottom:15px;
		margin-top:0;
	}
	.dashboard_widget .widget_header h4 {
		font-family:arial;
		font-size:16px;
		line-height:2;
		margin:0;
	}
	.widget_header { margin-bottom:2px; }
	.dashboard_widget .widget_action { line-height:31px; }

	#dashboard_active_top {
		list-style:none;
		margin:0;
		padding:0;
	}
	#dashboard_active_top li { margin:0; padding:2px 0; border-bottom:1px solid #ddd; }
	#dashboard_active_top li:last-child { border-bottom:none; }
	#dashboard_active_top li a { display:block; overflow:hidden; padding:6px; border-radius:3px; }
	#dashboard_active_top li a span.db_topic_title { font-size:15px; }
	#dashboard_active_top li a p.db_topic_info { margin:0; font-size:12px; color:#aaa }

	#dashboard_active_top li a:hover {
		text-decoration:none;
		background:#e3f5fb;
		color:#0067af;
	}
	#dashboard_active_top li a:hover span.db_topic_title { text-decoration:underline; }
	#dashboard_active_top li a:hover p.db_topic_info { color:#555; }

</style>
<div class="clearfix" style="margin:1px 5px; padding:5px; border-bottom:1px solid #ddd; padding-bottom:15px;">
	<h2 id="welcome_header">Welcome back, <?php echo $user['username'] ?>!</h2>
	<span class="muted">
		<?php if ($latest_announcement): ?>
			<?php if (strtotime($latest_announcement['topic_time']) > time()-86400): ?>
				<span class="label label-important">New!</span>
			<?php endif ?>
			Latest announcement: <a href="/topic/view/<?php echo $latest_announcement['topic_id'] ?>"><?php echo $latest_announcement['topic_title'] ?></a></span>
		<?php else: ?>
			<span class="muted">No new announcements yet!</span>
		<?php endif ?>
</div>
<br />
<div class="clearfix row" style="margin:10px -5px;">
	<div class="span7">

		<div class="dashboard_widget">
			<div class="clearfix widget_header">
				<h4 class="pull-left">Active Topics</h4>
				<!-- <a href="#" class="pull-right widget_action">All your notifications &rsaquo;</a> -->
			</div>
			<div class="clearfix widget_content" style="margin-top:-2px;">
				<ul id="dashboard_active_top">
					<?php echo $this->load->view('partials/dashboard_topics', array('latest_topics' => $latest_topics)) ?>
				</ul>
			</div>
		</div>
		<label for="autoload_new_topics"><input type="checkbox" id="autoload_new_topics" checked="true" /> Auto-reload active topics</label>
	</div>

	<div class="span4">
		<div class="dashboard_widget">
			<div class="clearfix widget_header">
				<h4 class="pull-left">Recent notifications</h4>
				<!-- <a href="#" class="pull-right widget_action">All your notifications &rsaquo;</a> -->
			</div>
			<div class="clearfix widget_content">
				<?php if (count($notifications) > 0): ?>
					<?php foreach ($notifications as $notification): ?>
						<div class="media">
							<a class="pull-left" href="#"><img class="media-object" src="http://crysandrea.com/images/avatars/<?php echo $notification['sender_id'] ?>_headshot.png" width="42" height="42"></a>
							<div class="media-body">
								<div style="margin-bottom:2px;">
									<small>
									<strong class="media-heading"><?php echo anchor('/user/'.urlencode($notification['sender']), $notification['sender']) ?></strong> &#8901; <span class="muted"><?php echo human_time($notification['timestamp']) ?></span>
									</small>
								</div>
								<?php echo anchor($notification['attatchment_url'], $notification['notification_text']) ?>
							</div>
						</div>
					<?php endforeach ?>
				<?php else: ?>
					<span class="muted">You have no notifications</span>
				<?php endif ?>
			</div>
		</div>

		<?php if ($spotlight_topic): ?>
		<div class="dashboard_widget">
			<div class="clearfix widget_header">
				<h4 class="pull-left">Spotlight Topic</h4>
			</div>
			<div class="clearfix widget_content" style="background:#ffa;">
				<div class="media">
					<a class="pull-left" href="#"><img class="media-object" src="http://crysandrea.com/images/avatars/<?php echo $spotlight_topic['user_id'] ?>_headshot.png" width="42" height="42"></a>
					<div class="media-body">
						<div style="margin-bottom:2px;">
							<small>
							<strong class="media-heading"><?php echo anchor('/user/'.urlencode($spotlight_topic['username']), $spotlight_topic['username']) ?></strong> &#8901; <span class="muted"><?php echo human_time($spotlight_topic['timestamp']) ?></span>
							</small>
						</div>
						<?php echo anchor('/topic/view/'.$spotlight_topic['topic_id'], $spotlight_topic['topic_title']) ?>
					</div>
				</div>
			</div>
		</div>
		<?php endif ?>

	</div>
</div>