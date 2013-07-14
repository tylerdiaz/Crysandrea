<style type="text/css">
	#tab_holder {
		width:470px;
		float:left;
		position:relative;
	}
	#dashboard_tabs {
		list-style:none;
		margin:0;
		padding:0;
		overflow:hidden;
		box-shadow:inset 0 -1px #d6d6d6;
	}
	#dashboard_tabs li {
		float:left;
		padding:8px 12px;
		background:transparent no-repeat center bottom;
	}
	#dashboard_tabs li a {
		font-weight:bold;
	}
	#dashboard_tabs li.active {
		background-image:url(/images/icons/dashboard_ticker.png);
	}
	#dashboard_tabs li.active a {
		color:#10548b;
		opacity:0.8;
	}
	#tab_canaster { padding:1px 5px; }
	#tab_canaster > div { display:none; }
	#tab_canaster > div:first-child { display:block; }
	#dashboard_notifications {
		margin-top:5px;
	}

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
<div class="clearfix">
	<div id="tab_holder">
		<ul id="dashboard_tabs">
			<li class="active"><a href="#">Active Topics</a></li>
			<li><a href="#">Your notifications</a></li>
		</ul>
		<div id="tab_canaster">
			<div>
				<ul id="dashboard_active_top">
					<?php foreach ($latest_topics as $topic): ?>
					<li class="<?php echo ($topic['forum_name'] == "Announcements" ? "highlight" : ""); ?>">
						<a href="<?php echo site_url('topic/view/'.$topic['topic_id']) ?>">
							<img src="/images/avatars/<?php echo $topic['topic_author'] ?>_headshot.png" alt="" style="float:left; width:42px;height:42px;margin:-3px 5px 0 -2px;">
							<span class="db_topic_title"><?php echo stripslashes($topic['topic_title']) ?></span>
							<br />
							<p class="db_topic_info"><?php echo human_time($topic['last_post']) ?> <span>in <?php echo $topic['forum_name'] ?></span></p>
						</a>
					</li>
					<?php endforeach ?>
				</ul>
			</div>
			<div>
				<ul id="dashboard_notifications">
					<?php foreach ($notifications as $notification): ?>
						<li><a href="<?php echo $notification['attatchment_url'] ?>"><?php echo $notification['notification_text'] ?></a> <span style="font-size:12px; color:#888;">(<?php echo human_time($notification['timestamp']) ?>)</span></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
	<div style="overflow:hidden">
		<div style="background:#ccc; width:246px; float:left; height:130px; margin-left:10px; margin-bottom:10px; color:#888; border-radius:4px;">
			<h4 style="text-align:center; line-height:100px; font-weight:normal; text-shadow:1px 1px 0 #ddd">Coming soon</h4>
		</div>
		<div style="background:#ccc; width:246px; float:left; height:130px; margin-left:10px; margin-bottom:10px; color:#888; border-radius:4px;">
			<h4 style="text-align:center; line-height:100px; font-weight:normal; text-shadow:1px 1px 0 #ddd">Coming sooner</h4>
		</div>
		<div style="background:#ccc; width:246px; float:left; height:130px; margin-left:10px; margin-bottom:10px; color:#888; border-radius:4px;">
			<h4 style="text-align:center; line-height:100px; font-weight:normal; text-shadow:1px 1px 0 #ddd">Coming very soon</h4>
		</div>
	</div>
</div>