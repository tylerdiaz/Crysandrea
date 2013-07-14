<?php $this->load->view('forest/forest_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>
<style type="text/css">
	#bug_showcase { width:414px; height:205px; padding:10px; border:3px solid #6e9354; margin:5px 0; float:left; border-radius:6px; background:#fff; position:relative; }
	#bug_image { margin:0; float:left; width:180px; height:180px; }
	#bug_showcase h2 { color:#111; margin:0; font-size:22px; line-height:1.9; }
	#bug_showcase h4 { line-height:1.5; font-weight:normal; color:#888; font-size:12px; margin:5px 0 25px; }
	#bug_showcase h5 { line-height:1.5; font-weight:normal; color:#888; font-size:12px; margin:5px 0 0; }
	#bug_showcase p#bug_description { color:#555; margin:0; font-size:14px; line-height:1.4; }
	#bug_showcase p#bug_data { color:#333; margin:10px 0 0; font-size:12px; line-height:1.4; }
	#before_you_go, #after_you_go {
		font-size:17px;
		margin:4px 6px;
		color:#777;
		background:url(/images/headers/before_you_hunt.png) no-repeat;
		height:22px;
		text-indent:-9999px;
		padding-bottom:5px;
	}
	#forest_actions {
		list-style:none;
		margin:0;
	}
	#forest_actions li a {
		display:block;
		background:rgba(0, 0, 0, 0.4);
		border:1px solid #215919;
		border-radius:3px;
		padding:6px 8px;
		margin:6px 0;
	}
	div#bug_showcase.center_shine {
		background: #ffffff; /* Old browsers */
		background: -moz-radial-gradient(center, ellipse cover,  #ffffff 0%, #90b574 100%); /* FF3.6+ */
		background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#ffffff), color-stop(100%,#90b574)); /* Chrome,Safari4+ */
		background: -webkit-radial-gradient(center, ellipse cover,  #ffffff 0%,#90b574 100%); /* Chrome10+,Safari5.1+ */
		background: -o-radial-gradient(center, ellipse cover,  #ffffff 0%,#90b574 100%); /* Opera 12+ */
		background: -ms-radial-gradient(center, ellipse cover,  #ffffff 0%,#90b574 100%); /* IE10+ */
		background: radial-gradient(ellipse at center,  #ffffff 0%,#90b574 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#90b574',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
	}
	.no_shine { background:#fff; }
	.add_up {
		position:absolute;
		left:540px;
		top:25px;
		background: #e3f5ab; /* Old browsers */
		background: -moz-linear-gradient(top,  #e3f5ab 33%, #b7df2d 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(33%,#e3f5ab), color-stop(100%,#b7df2d)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #e3f5ab 33%,#b7df2d 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #e3f5ab 33%,#b7df2d 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #e3f5ab 33%,#b7df2d 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #e3f5ab 33%,#b7df2d 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e3f5ab', endColorstr='#b7df2d',GradientType=0 ); /* IE6-9 */
		border-radius:4px;
		padding:2px 3px;
		font-size:12px;
		letter-spacing:-1px;
		font-weight:bold;
		color:green;
		border:1px solid #e3f5ab;
		opacity:0.8;
		display:none;
	}
	#forest_sidebar {
		width:260px; min-height:205px; padding:10px; background:rgba(0, 0, 0, 0.4); margin:8px 0 0 5px; float:left; border-radius:5px;
	}
	#cover_pane {
		height:100%; width:100%; background:rgba(255, 255, 255, 0.7); position:absolute; top:0; left:0;  display:none;
	}
	.forest_success_bubble, .forest_alert_bubble {
		position:absolute;
		top:35%;
		left:50%;
		width:300px;
		margin-left:-170px;
		padding:15px 20px;
		color:#efffd5;
		background:#268526;
		border:2px solid #195919;
		text-align:center;
		border-radius:6px;
		box-shadow:inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 0 3px rgba(0, 0, 0, .2);
		display:none;
		line-height:1.4;
	}
	.forest_alert_bubble { color:#ffdbcc; background:#8e2f0f; border:2px solid #691c0c }
	.forest_success_bubble img, .forest_alert_bubble img { margin-top:-2px; margin-right:2px }
	.forest_ajax_bubble {
		height:32px; width:32px; padding:15px; background:rgba(0, 0, 0, 0.8); position:absolute; top:37%; left:50%; margin-left:-21px; border-radius:6px; box-shadow:0 0 0 5px rgba(0, 0, 0, .1); display:none;
	}
	#forest_levelup_bubble {
		width:379px;
		height:123px;
		background:url(/images/forest/levelup_box.png);
		position:absolute;
		top:16px;
		left:50%;
		margin-left:-190px;
		padding-top:67px;
		text-align:center;
		display:none;
	}
	#forest_levelup_bubble h4 {
		color:#40580f;
		font-size:16px;
		margin:0 0 8px;
	}
	#forest_levelup_bubble h5 {
		color:#293b0c;
		font-size:13px;
		margin:0 15px 10px;
	}
	#character_statistics {
		height:60px;
		background:#111;
		border-radius:5px;
		padding:5px;
		position:relative;
	}
	.value_holder {
		color:#ccc;
		float:left;
		min-width:60px;
		text-align:center;
		margin:8px 5px 0;
		padding:0 5px;
	}
	.value_label {
		text-transform:uppercase;
		font-size:11px;
		color:#bbb;
	}
	.value_label img { margin-top:-3px;}
	.value_result {
		display:block;
		font-size:22px;
	}

	.energy_bar, .exp_bar {
		height:28px;
		background:#333;
		border-radius:3px;
		overflow:hidden;
		box-shadow:inset 0 -2px 1px 0 #404040;
	}
	.energy_bar .value {
		width:20%; height:100%;
		background: #b8ea4c; /* Old browsers */
		background: -moz-linear-gradient(top,  #b8ea4c 0%, #93c428 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#b8ea4c), color-stop(100%,#93c428)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #b8ea4c 0%,#93c428 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #b8ea4c 0%,#93c428 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #b8ea4c 0%,#93c428 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #b8ea4c 0%,#93c428 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b8ea4c', endColorstr='#93c428',GradientType=0 ); /* IE6-9 */
		box-shadow:inset 0 -2px 1px 0 rgba(255, 255, 255, 0.4);
	}
	.exp_bar .value {
		width:30%; height:100%;
		background: #84dfe8; /* Old browsers */
		background: -moz-linear-gradient(top,  #84dfe8 1%, #74d3dc 18%, #4db9c3 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#84dfe8), color-stop(18%,#74d3dc), color-stop(100%,#4db9c3)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #84dfe8 1%,#74d3dc 18%,#4db9c3 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #84dfe8 1%,#74d3dc 18%,#4db9c3 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #84dfe8 1%,#74d3dc 18%,#4db9c3 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #84dfe8 1%,#74d3dc 18%,#4db9c3 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#84dfe8', endColorstr='#4db9c3',GradientType=0 ); /* IE6-9 */
		box-shadow:inset 0 -2px 1px 0 rgba(255, 255, 255, 0.4);
	}
	.forest_widget {
		width:357px; min-height:110px; background:rgba(0, 0, 0, 0.6); margin:5px 3px; float:left; border-radius:5px; overflow:hidden
	}
	.forest_widget .widget_header {
		padding:10px;
		background:rgba(0, 0, 0, 0.4);
	}
	.forest_widget .widget_header h3 {
		margin:0;
		font-size:13px;
		color:#a2c191;
		font-weight:normal;
		letter-spacing:1px;
	}
	.forest_widget .leaderboard_thumbnail {
		display:block;
		float:left;
		border:2px solid #70b84c;
		background:white;
		opacity:0.6;
		border-radius:4px;
		width:56px;
		position:relative;
		margin:5px 5px;
	}
	.forest_widget .leaderboard_thumbnail span {
		position:absolute;
		bottom:1px;
		right:1px;
		display:block;
		padding:4px;
		border-radius:5px;
		font-size:12px;
		background:#111;
		color:#aaa;
	}
	.forest_widget .leaderboard_thumbnail:hover {
		opacity:1;
	}
	#net_auxilliary {
		border-top:1px solid #5f822d; margin-top:10px; padding:10px 0 0; color:#9eb07f; display:none;
	}
	#net_auxilliary a {
		color:#67b0ee;
	}
	#net_auxilliary a.main_button {
		color:#fff;
	}
</style>
<div class="clearfix">
	<div id="bug_showcase" class="center_shine">
 		<div id="cover_pane"></div>
		<div id="forest_levelup_bubble">
			<h4>You are now level <strong id="new_level_amount">12</strong></h4>
			<h5>You can now catch <strong id="new_bug_possibilities">2</strong> different type of bugs. Your energy has also been fully restored.</h5>
			<a href="#" id="close_levelup_bubble">Ok! Continue hunting &rsaquo;</a>
		</div>
		<div class="forest_success_bubble"><img src="/images/icons/forest/success.png" alt="" /> <span></span></div>
		<div class="forest_alert_bubble"><img src="/images/icons/forest/alert.png" alt="" /> <span></span></div>
		<div class="forest_ajax_bubble"><img src="/images/icons/forest/activity_indicator.gif" alt=""></div>
		<div class="hunt_placeholder" style="text-align:center;">
			<br />
			<h2 style="line-height:1.4;">Ready to start hunting?</h2>
			<h4>You energy will fully restore in about <span id="energy_time_left"> <?php echo substr(human_time(date("Y-m-d H:i:s", time()+$recover_time_left), TRUE), 0, -4) ?></span></h4>
			<a href="#" id="start_hunting" class="main_button">Start hunting</a>
		</div>
		<div class="hide hunt_template">
			<img src="/images/insects/unset.gif" alt="" width="180" height="180" id="bug_image" />
			<h5 id="capture_title">Just letting you know this is a...</h5>
			<h2 id="bug_name">Placeholder bug</h2>
			<p id="bug_description">“Never mind me, I'm just here to take the place of a bug you're about to find...”</p><br />
			<p id="bug_data"><img src="/images/icons/little_palladium.png" style="margin-top:-3px" width="18" height="18" alt=""> <span id="total_bug_value">16</span> palladium &bull; Rarity: <span id="bug_rarity">Uncommon</span></p><br />
		</div>
	</div>
	<div id="forest_sidebar">
		<div class="before_hunting">
			<h2 id="before_you_go">Before you go bug hunting...</h2>
			<ul id="forest_actions">
				<li><a href="#" id="berry_snack">Snack on a berry</a></li>
				<li><a href="#" id="auto_fix_nets">Auto-fix your broken nets</a></li>
				<li><a href="/forest/tour" id="retake_tour">Take the tour again</a></li>
			</ul>
		</div>
		<div class="hide after_hunting">
			<h2 id="after_you_go">Happy hunting!</h2>
			<div style="color:#aaa; padding: 5px 10px;">
				<a href="#" id="go_hunting" class="main_button">Keep hunting</a> or <a href="#" id="stop_hunting" style="color:#35929d">Stop hunting</a>
			</div>
			<div id="net_auxilliary">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod dolore magna aliqua.</p>
			</div>
		</div>
	</div>
</div>
<div class="clearfix" id="character_statistics">
	<div class="value_holder">
		<span class="value_label">Level</span>
		<span id="level" class="value_result"><?php echo number_format($hunter_data['level']) ?></span>
	</div>
	<div class="value_holder" id="forest_net">
		<span class="value_label"><img src="/images/icons/forest/net.png" width="11" height="11" alt="" /> Net</span>
		<span id="level" class="value_result"><?php echo $net ?></span>
	</div>
	<div class="value_holder" id="forest_berries">
		<span class="value_label"><img src="/images/icons/forest/berry.png" width="11" height="11" alt="" /> Berries</span>
		<span id="level" class="value_result"><?php echo number_format($hunter_data['berries']) ?></span>
	</div>
	<div class="clearfix" style="float:right; width:140px; height:60px; margin:0 5px; color:#aaa; font-size:12px; overflow:hidden">
		<div class="clearfix" style="margin:4px 0 5px 0;"><span class="left">Energy</span><span class="right"><span id="energy"><?php echo number_format($hunter_data['energy']) ?></span>/<span id="max_energy"><?php echo number_format($hunter_data['max_energy']) ?></span></span></div>
		<div class="energy_bar"><div class="value" style="width:<?php echo percent($hunter_data['energy'], $hunter_data['max_energy']) ?>%"></div></div>
	</div>
	<div class="clearfix" style="float:right; width:220px; height:60px; margin:0 5px; color:#aaa; font-size:12px;">
		<div class="clearfix" style="margin:4px 0 5px 0;"><span class="left">Experience</span><span class="right"><span id="exp_user"><?php echo number_format($hunter_data['exp']) ?></span>/<span id="exp_level"><?php echo number_format($hunter_data['next_level_exp']) ?></span></span></div>
		<div class="exp_bar"><div class="value" style="width:<?php echo percent($hunter_data['exp'], $hunter_data['next_level_exp']) ?>%"></div></div>
	</div>

	<div class="add_up">+19</div>
</div>
<div class="clearfix">
	<div class="forest_widget">
		<div class="clearfix widget_header">
			<h3 class="left">Under construction</h3>
			<!-- <a href="#" class="right">View others</a> -->
		</div>
		<div style="text-align:center; line-height:65px; color:#718a5f">
			This part of the forest is under construction!
		</div>
	</div>
	<div class="forest_widget">
		<div class="clearfix widget_header">
			<h3 class="left">Friends Leaderboard</h3>
			<a href="/forest/leaderboards" class="right">View Leaderboards</a>
		</div>
		<div style="margin:0 3px" id="leaderboard_container">
			<a href="#" class="leaderboard_thumbnail magicTip" title="Username's Museum"><img src="/images/avatars/30_headshot.png" width="56" height="56" alt=""><span>#1</span></a>
			<a href="#" class="leaderboard_thumbnail magicTip" title="Username's Museum"><img src="/images/avatars/31_headshot.png" width="56" height="56" alt=""><span>#2</span></a>
			<a href="#" class="leaderboard_thumbnail magicTip" title="Username's Museum"><img src="/images/avatars/32_headshot.png" width="56" height="56" alt=""><span>#3</span></a>
			<a href="#" class="leaderboard_thumbnail magicTip" title="Username's Museum"><img src="/images/avatars/37_headshot.png" width="56" height="56" alt=""><span>#4</span></a>
			<a href="#" class="leaderboard_thumbnail magicTip" title="Username's Museum"><img src="/images/avatars/34_headshot.png" width="56" height="56" alt=""><span>#5</span></a>
		</div>
	</div>
</div>