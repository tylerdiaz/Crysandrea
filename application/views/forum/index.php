<style type="text/css">
	#forum_panes {
		margin:2px 1px 0;
	}
	#forum_panes > div {
		display:none;
		overflow:hidden;
		background:#aaa;
		padding:10px 8px 13px;
		min-height:214px;
		-webkit-border-radius: 4px 4px 0px 0px;
		        border-radius: 4px 4px 0px 0px;

	}
	#forum_panes > div:first-child {
		display:block
	}
	#forum_panes div a {
		display:block;
		width:200px;
		padding:12px 12px;
		min-height:70px;
		margin:4px 4px 4px;
		border:2px solid #777;
		float:left;
		-webkit-border-radius: 6px;
	    border-radius: 6px;
	}
	#forum_panes div a:hover {
		text-decoration:none;
		background-color:rgba(255, 255, 255, 0.9);
	}
	#forum_panes div a:active {
		opacity:0.8;
		color:#888;
		box-shadow:inset 0 0 4px 1px rgba(0, 0, 0, 0.45);
	}
	#forum_panes div a h3 {
		font-size:14px;
		margin:0;
		line-height:1.6;
	}
	#forum_panes div a p {
		font-size:11px;
		margin:0;
		line-height:1.4;
	}
	#forum_tabs {
		list-style:none;
		overflow:hidden;
		margin:0 0 0 1px;
	}
	#forum_tabs li {
		float:left;
		margin:0;
	}
	#forum_tabs li a {
		display:block;
		float:left;
		width:181px;
		background:#ccc;
		font-size:16px;
		font-weight:bold;
		line-height:32px;
		text-shadow:1px 0 3px 0 rgba(0, 0, 0, 0.3);
	}
	#forum_tabs li a:hover { text-decoration:underline; color:#ccc; }
	#forum_tabs li a span {
		display:block;
		font-size:16px;
		padding:16px 0 14px 22px;
		font-weight:bold;
		height:32px;
		box-shadow:inset 0 3px 1px 0 rgba(0, 0, 0, 0.15);
		border-right:1px solid #000;

	}
	#forum_tabs li:first-child a {
		-webkit-border-radius: 0px 0px 0px 4px;
		        border-radius: 0px 0px 0px 4px;
	}
	#forum_tabs li:last-child a {
		width:181px;
		-webkit-border-radius: 0px 0px 4px 0px;
		        border-radius: 0px 0px 4px 0px;
	}
	#forum_tabs li#market span {
		border-right:none;
		-webkit-border-radius: 0px 0px 4px 0px;
		        border-radius: 0px 0px 4px 0px;
	}
	#forum_tabs li#crysandrea span {
		-webkit-border-radius: 0px 0px 0px 4px;
		        border-radius: 0px 0px 0px 4px;
	}


	#forum_tabs li#community span {
		background:#51892b;
		color:#b6d5ab;
		border-color:#4a4836;
	}
	div#community_pane {
		background: #5aaa3f; /* Old browsers */
		background: -moz-linear-gradient(top, #5aaa3f 0%, #3f8925 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#5aaa3f), color-stop(100%,#3f8925)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #5aaa3f 0%,#3f8925 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #5aaa3f 0%,#3f8925 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #5aaa3f 0%,#3f8925 100%); /* IE10+ */
		background: linear-gradient(to bottom, #5aaa3f 0%,#3f8925 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#5aaa3f', endColorstr='#3f8925',GradientType=0 ); /* IE6-9 */
	}
	#forum_tabs li#community a.current span {
		box-shadow:none;
		color:#d1e8c8;
		background: #3f8925; /* Old browsers */
		background: -moz-linear-gradient(top, #3f8925 1%, #387c1d 25%, #337019 81%, #2e6614 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#3f8925), color-stop(25%,#387c1d), color-stop(81%,#337019), color-stop(100%,#2e6614)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, #3f8925 1%,#387c1d 25%,#337019 81%,#2e6614 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, #3f8925 1%,#387c1d 25%,#337019 81%,#2e6614 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top, #3f8925 1%,#387c1d 25%,#337019 81%,#2e6614 100%); /* IE10+ */
		background: linear-gradient(to bottom, #3f8925 1%,#387c1d 25%,#337019 81%,#2e6614 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3f8925', endColorstr='#2e6614',GradientType=0 ); /* IE6-9 */
	}
	div#community_pane a { background:#d6f3cc;  border-color:#208209; }

	#forum_tabs li#market span {
		background:#c87c12;
		color:#dbcfa3;
	}
	div#market_pane {
		background: #eaab52; /* Old browsers */
		background: -moz-linear-gradient(top,  #eaab52 1%, #de9f46 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#eaab52), color-stop(100%,#de9f46)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #eaab52 1%,#de9f46 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #eaab52 1%,#de9f46 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #eaab52 1%,#de9f46 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #eaab52 1%,#de9f46 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eaab52', endColorstr='#de9f46',GradientType=0 ); /* IE6-9 */
	}
	#forum_tabs li#market a.current span {
		box-shadow:none;
		color:#ffeedb;
		background: #de9f46; /* Old browsers */
		background: -moz-linear-gradient(top,  #de9f46 1%, #dd9639 25%, #dc8f30 81%, #dc8624 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#de9f46), color-stop(25%,#dd9639), color-stop(81%,#dc8f30), color-stop(100%,#dc8624)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #de9f46 1%,#dd9639 25%,#dc8f30 81%,#dc8624 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #de9f46 1%,#dd9639 25%,#dc8f30 81%,#dc8624 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #de9f46 1%,#dd9639 25%,#dc8f30 81%,#dc8624 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #de9f46 1%,#dd9639 25%,#dc8f30 81%,#dc8624 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#de9f46', endColorstr='#dc8624',GradientType=0 ); /* IE6-9 */
	}
	div#market_pane a { background:#f0e3d9;  border-color:#c98d41; }

	#forum_tabs li#crysandrea span {
		background:#1d95b7;
		color:#cbe5f3;
		border-color:#194214;
	}
	div#crysandrea_pane {
		background: #2ab4d3; /* Old browsers */
		background: -moz-linear-gradient(top,  #2ab4d3 0%, #16a4c4 95%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#2ab4d3), color-stop(95%,#16a4c4)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #2ab4d3 0%,#16a4c4 95%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #2ab4d3 0%,#16a4c4 95%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #2ab4d3 0%,#16a4c4 95%); /* IE10+ */
		background: linear-gradient(to bottom,  #2ab4d3 0%,#16a4c4 95%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#2ab4d3', endColorstr='#16a4c4',GradientType=0 ); /* IE6-9 */
	}
	#forum_tabs li#crysandrea a.current span {
		box-shadow:none;
		color:#d9edff;
		background: #16a4c4; /* Old browsers */
		background: -moz-linear-gradient(top,  #16a4c4 1%, #149bb6 25%, #1293aa 81%, #108ea2 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#16a4c4), color-stop(25%,#149bb6), color-stop(81%,#1293aa), color-stop(100%,#108ea2)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #16a4c4 1%,#149bb6 25%,#1293aa 81%,#108ea2 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #16a4c4 1%,#149bb6 25%,#1293aa 81%,#108ea2 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #16a4c4 1%,#149bb6 25%,#1293aa 81%,#108ea2 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #16a4c4 1%,#149bb6 25%,#1293aa 81%,#108ea2 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#16a4c4', endColorstr='#108ea2',GradientType=0 ); /* IE6-9 */
	}
	div#crysandrea_pane a { background:#d1e2ed;  border-color:#4092b4; }

	#forum_tabs li#art span {
		background:#938d74;
		color:#e0d8c4;
		border-color:#792e07;
	}
	div#art_pane {
		background: #919371; /* Old browsers */
		background: -moz-linear-gradient(top,  #919371 1%, #7f825e 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#919371), color-stop(100%,#7f825e)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #919371 1%,#7f825e 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #919371 1%,#7f825e 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #919371 1%,#7f825e 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #919371 1%,#7f825e 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#919371', endColorstr='#7f825e',GradientType=0 ); /* IE6-9 */
	}
	#forum_tabs li#art a.current span {
		box-shadow:none;
		color:#fff6d1;
		background: #7f825e; /* Old browsers */
		background: -moz-linear-gradient(top,  #7f825e 1%, #777957 25%, #717351 81%, #6a6c4c 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#7f825e), color-stop(25%,#777957), color-stop(81%,#717351), color-stop(100%,#6a6c4c)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #7f825e 1%,#777957 25%,#717351 81%,#6a6c4c 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #7f825e 1%,#777957 25%,#717351 81%,#6a6c4c 100%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #7f825e 1%,#777957 25%,#717351 81%,#6a6c4c 100%); /* IE10+ */
		background: linear-gradient(to bottom,  #7f825e 1%,#777957 25%,#717351 81%,#6a6c4c 100%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7f825e', endColorstr='#6a6c4c',GradientType=0 ); /* IE6-9 */
	}
	div#art_pane a { background:#e9e0c7;  border-color:#7c6f53; }


	#forum_tabs li a.current { background:#aaa; }
	#forum_tabs li a.current span { padding-bottom:16px; box-shadow:none; }
	#forum_tabs li a span img { margin-right:7px; margin-top:-2px; }
</style>

<?php $this->load->view('forum/forum_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<div id="forum_panes">
	<?php foreach ($forums as $category_name => $forum_array): ?>
	<div id="<?php echo strtolower($category_name) ?>_pane">
		<?php foreach ($forum_array as $forum_data): ?>
			<a href="/forum/view/<?php echo $forum_data['forum_id'] ?>" id="forum_<?php echo $forum_data['forum_id'] ?>">
				<h3><?php echo $forum_data['forum_name'] ?> <img src="/images/icons/new.png" alt="" style="display:none" /></h3>
				<p><?php echo $forum_data['forum_description'] ?></p>
			</a>
		<?php endforeach ?>
	</div>
<?php endforeach ?>
</div>

<ul id="forum_tabs">
<?php foreach ($forums as $category_name => $forum_array): ?>
	<li id="<?php echo strtolower($category_name); ?>">
		<a href="#<?php echo strtolower($category_name); ?>" class="<?php echo $category_name == 'Crysandrea' ? 'current' : '' ?>">
			<span><img width="32" height="32" src="/images/forum_icons/<?php echo strtolower($category_name); ?>.png" /><?php echo ucfirst($category_name); ?></span>
		</a>
	</li>
<?php endforeach ?>
</ul>

<br />

<div class="row">
	<div class="grid_4">
		<h5 style="margin:0 0 3px;">There are currently <?php echo count($users_online) ?> online users <span style="color:#8f8f8f; font-weight:normal; font-size:11px;">(<?php echo count($users_online) ?> Crysandreans, 0 Guests)</span></h5>
		<?php foreach ($users_online as $user_key => $user): ?>
			<!-- <a href="/user/<?php echo urlencode($user['username']) ?>" style="<?php echo user_style($user['user_level']) ?>"><?php echo $user['username'] ?></a><?php echo ($user_key != count($users_online)-1 ? ', ' : '') ?> -->
			<a href="/user/<?php echo urlencode($user['username']) ?>" style="color:<?php echo sprintf("#%06x",rand(0,16777215)) ?>; <?php echo ($user['user_level'] != 'user' ? "font-weight:bold; text-decoration:underline" : '') ?>"><?php echo $user['username'] ?></a><?php echo ($user_key != count($users_online)-1 ? ', ' : '') ?>

		<?php endforeach ?>
	</div>
	<div class="grid_2">
		<div style="height: 1px; background-color: #b2d898; padding-left:5px; font-family:arial; margin:5px 0 13px 0; clear:both;">
		  <span style="background-color: white; position: relative; top: -0.73em; padding:0 6px; text-transform:uppercase; color:#476d20; font-size:11px; font-weight:bold;">
			<img src="/images/icons/statistics.png" alt="" style="margin-top:-4px"> Crysandrea's Statistics
		  </span>
		</div>
		<div style="margin:6px 0 0px 15px; font-size:14px;">Total posts: <strong><?php echo number_format($total_posts) ?></strong></div>
		<div style="margin:6px 0 0px 15px; font-size:14px;">Total users: <strong><?php echo number_format($total_users) ?></strong></div>
	</div>
</div>
<br clear="all" />