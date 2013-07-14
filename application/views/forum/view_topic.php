<style type="text/css" media="screen">
a.reveal_spoiler {
	background: #1e406d; /* Old browsers */
	padding:5px 12px;
	border-radius:3px;
	color:#afbecf;
}
a.reveal_spoiler:hover {
	text-decoration:none;
	background: #22498e; /* Old browsers */
	color:white;
}
a.reveal_spoiler:active {
	text-decoration:none;
	background: #122341; /* Old browsers */
	color:#7d90a9;
}
.spoiler_value {
	display:none;
	margin-top:10px;
	background:rgba(255, 255, 255, 0.7);
	padding:4px 8px;
	border-radius:4px;
}
.post-grid { float:left; margin:0 0 7px; padding:0 0 5px; clear:both; overflow:hidden; }
.post-grid .topic_avatar { float:left }

.post-content {
	background: #edf6ff; /* Old browsers */
	background: -moz-linear-gradient(top,  #edf6ff 1%, #e0f1ff 11%, #e0f1ff 85%, #d7e7f4 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#edf6ff), color-stop(11%,#e0f1ff), color-stop(85%,#e0f1ff), color-stop(100%,#d7e7f4)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #edf6ff 1%,#e0f1ff 11%,#e0f1ff 85%,#d7e7f4 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #edf6ff 1%,#e0f1ff 11%,#e0f1ff 85%,#d7e7f4 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #edf6ff 1%,#e0f1ff 11%,#e0f1ff 85%,#d7e7f4 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #edf6ff 1%,#e0f1ff 11%,#e0f1ff 85%,#d7e7f4 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#edf6ff', endColorstr='#d7e7f4',GradientType=0 ); /* IE6-9 */
	border: 1px solid #c1cfdb;
	border-top: 1px solid #cfdbe6;
	float:left;
	margin:0 0 5px 5px;
	margin-right:-20px;
	border-radius:8px;
	-moz-border-radius:8px;
	-webkit-border-radius:8px;
	padding:4px 12px 20px 15px;
	width:510px;
	position:relative;
	min-height:70px;
	box-shadow:0 2px 1px 1px rgba(0, 0, 0, 0.075);
	line-height:1.5
}
.post-content img { max-width:510px; max-height:900px; }
.post-toolbar { color:#aaa; float:left; line-height:25px; padding:5px 15px 0; font-size:0.923em; width:513px }
.post_author { display:block; padding:6px 0 3px; font-size:1.154em; font-weight:bold; }
.post_author a { color:#111; border-radius:3px; padding:0 1px }
.post_author a:hover { background: #0f7d99; /* Old browsers */
background: -moz-linear-gradient(top,  #0f7d99 0%, #00617b 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#0f7d99), color-stop(100%,#00617b)); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(top,  #0f7d99 0%,#00617b 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(top,  #0f7d99 0%,#00617b 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(top,  #0f7d99 0%,#00617b 100%); /* IE10+ */
background: linear-gradient(to bottom,  #0f7d99 0%,#00617b 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0f7d99', endColorstr='#00617b',GradientType=0 ); /* IE6-9 */
 color:white; text-decoration:none; }
.post_author a:active {
	background: #035369; /* Old browsers */
	background: -moz-linear-gradient(top,  #035369 0%, #116b81 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#035369), color-stop(100%,#116b81)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #035369 0%,#116b81 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #035369 0%,#116b81 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #035369 0%,#116b81 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #035369 0%,#116b81 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#035369', endColorstr='#116b81',GradientType=0 ); /* IE6-9 */
	color:#ccc;
}

.ribbon { position:absolute; right:-3px; top:-3px; z-index:2; -webkit-transition: opacity 200ms ease; -moz-transition: opacity 200ms ease; -ms-transition: opacity 200ms ease; -o-transition: opacity 200ms ease; transition: opacity 200ms ease;}
.ribbon:hover { opacity:0.2; }
.quote-1 { border-left:2px solid rgba(0, 0, 0, 0.15); font-size:0.923em; margin:2px 5px 10px 4px; padding:8px 8px 10px 10px; background:rgba(0, 0, 0, 0.1) url(/global/css/images/icons/quote_icon.png)no-repeat right bottom; }
.user_signature {
	width:535px;
	float:right;
	text-align:center;
	overflow:hidden;
	max-height:275px;
}
.post-content:after, .post-content:before {
	right: 100%;
	border: solid transparent;
	content: " ";
	height: 0;
	width: 0;
	position: absolute;
	pointer-events: none;
}

.post-content:after {
	border-color: rgba(224, 241, 255, 0);
	border-right-color: #e0f1ff;
	border-width: 8px;
	top: 60px;
	margin-top: -8px;
}
.post-content:before {
	border-color: rgba(193, 207, 219, 0);
	border-right-color: #c1cfdb;
	border-width: 9px;
	top: 60px;
	margin-top: -9px;
}

#user_posts_head {
	background:#81B860;
	border-bottom:1px solid #518A3A;
	height:40px;
	overflow:hidden;
	-moz-border-radius-topright:5px;
	-moz-border-radius-topleft:5px;
	-webkit-border-top-right-radius:5px;
	-webkit-border-top-left-radius:5px;
	color:#fff;
	text-shadow:-1px -1px 0px #62933E
}
.t_locked {
    text-align:center;
    background:#eee;
    border:1px solid #ddd;
    padding:15px 0 0;
    margin-bottom:15px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    color:#777;
}
.t_locked h4{
    color:#444;
}
.t_signed_out {
    text-align:center;
    background:#ffd;
    border:1px solid #ee7;
    padding:15px 0 10px;
    margin-bottom:15px;
    -moz-border-radius:6px;
    -webkit-border-radius:6px;
    color:#757754;
    font-size:12px;
}
.t_signed_out h4{
    color:#444;
    margin:0;
    font-size:14px;
    line-height:2;
}

#go_to_page_label{
	color: #0076AB;
	font-weight: bold;
	padding-left: 5px;
}

    .user_online {
        background:#E1FFCE;
        color:#547823;
        -moz-border-radius:10px;
        -webkit-border-radius:10px;
        padding:2px 8px;
        font-size:11px;
    }
    .delete_post {
        font-weight:bold;
        background:#FFCDCF;
        color:#5D0405 !important;
        font-size:11px;
        padding:1px 6px 2px;
        -moz-border-radius:10px;
        -webkit-border-radius:10px;
    }
    a.bookmark {
    	background:#174386 url(/global/css/images/icons/bookmark_icon_hack.png) left center;
    	padding-left:20px;
    	font-size:12px;
    	margin-top:8px;
    	margin-right:5px;
    	-webkit-transition: all 200ms ease;
    	-moz-transition: all 200ms ease;
    	-ms-transition: all 200ms ease;
    	-o-transition: all 200ms ease;
    	transition: all 200ms ease;
    }
    a.bookmark:hover {
    	background-color:orange;
    	color:orange;
    }
    .bug_error, .bug_success { overflow:hidden; padding:3px; -webkit-border-radius:5px; -moz-border-radius:5px; border-radius:5px }
    .bug_error { background:#FFD8D9; color:#5C2D28; border:2px solid #ffa5a7; }
    .bug_success { background:#C2E79A; color:#3C601D; border:2px solid #9FC676 }

    .new_topic_posts {
    	padding:10px 15px;
    	margin:10px 0;
    	cursor: pointer;
    	display: none;
    }
    .success_bookmark {
    	padding:8px 4px 0;
    	font-size:12px;
    	color:green;
    }
    .success_bookmark img {
    	margin-top:-1px;
    }
</style>
<div class="feature_header">
	<h2 class="friends_icon"><a href="/forum/view/<?php echo $topic['forum_id'] ?>"><?php echo $topic['short_name'] ?></a>: <?php echo stripslashes(sanitize($topic['topic_title'])) ?></h2>

	<?php if ($suscribed): ?>
		<a href="#" class="right bookmark hide">Bookmark this topic</a>
		<span class="right success_bookmark"><img src="/images/icons/tiny_success.png" alt="" /> Bookmarked (<a href="#" class="remove_bookmark">remove</a>)</span>
	<?php else: ?>
		<a href="#" class="right bookmark">Bookmark this topic</a>
		<span class="right success_bookmark hide"><img src="/images/icons/tiny_success.png" alt="" /> Bookmarked (<a href="#" class="remove_bookmark">remove</a>)</span>
	<?php endif ?>
</div>

<?php if ($this->pagination->total_rows > $this->pagination->per_page): ?>
	<div class="topic_head_bottom" style="margin-top:-5px; border-bottom:1px solid #ddd;">
		<?php echo $this->pagination->create_links(); ?>
	</div>
<?php endif ?>

<!-- <div class="alert">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Warning!</strong> Topics are going under some minor tuning. Gears might fly out, it won't last too long!
</div>
 -->
<div style="margin-top:10px; overflow:hidden;" id="topic_post_container">
	<?php $this->load->view('forum/partials/topic_posts') ?>
</div>

<span><?php echo $this->pagination->create_links(); ?></span>

<?php if($topic['topic_status'] == "locked"): ?>
<div class="grid_10">
    <div class="t_locked">
        <h4>This topic has been locked</h4>
        <p>In other words, no replies can be posted to this topic.</p>
    </div>
</div>
<?php elseif( ! $this->session->userdata('user_id')): ?>
<div class="grid_10">
    <div class="t_signed_out">
        <h4>It'll be a lot more fun with you!</h4>
        <p>Having your voice on our little forum would be awesome. Don't have an account? <?=anchor('signup', 'Sign up now!')?></p>
   	</div>
</div>
<?php else: ?>

<div class="success new_topic_posts">There has been <span id="total_new_posts">1 new post</span> since you last refreshed. (click this notice to load them)</div>

<div class="grid_10 clearfix">
	<form action="/forum/topic_reply/<?php echo $topic['topic_id'] ?>" method="post" id="send_post_message" accept-charset="utf-8">
		<textarea name="message" tabindex="1" class="input" id="message" style="width:98%; font-family:'lucida grande', arial, sans-serif; height:90px;" placeholder="What would you like to say?"></textarea>
		<span class="right">
		    <input type="submit" tabindex="2" class="main_button" value="Submit your post" id="submit_post">
		</span>
	</form>
</div>
<? endif; ?>

<?php if ($this->system->is_staff()): ?>
	<hr>
	<strong>Staff only tools:</strong>
	<div class="clearfix">

		<form style="display:inline-block" action="/forum/toggle_lock" method="POST">
			<div class="control-group">
				<input type="hidden" name="topic_id" value="<?php echo $topic['topic_id'] ?>" />
				<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
				<?php if ($topic['topic_status'] == 'unlocked'): ?>
					<button class="btn"><i class="icon-lock"></i> Lock Topic</button>
				<?php else: ?>
					<button class="btn btn-primary"><i class="icon-white icon-lock"></i> Unlock Topic</button>
				<?php endif ?>
			</div>
		</form>

		<form style="display:inline-block" action="/forum/move_topic" method="POST">
			<div class="control-group">
				<input type="hidden" name="topic_id" value="<?php echo $topic['topic_id'] ?>" />
				<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
				<?php $category = $this->db->select('forum_id, forum_name')->get('forums')->result_array();	?>

				<select id="forum_id" name="forum_id" style="vertical-align:top; width:170px;">
					<option value="none">Forum name...</option>
					<?php foreach($category as $forum): ?>
					<option value="<?php echo $forum['forum_id'] ?>"><?php echo $forum['forum_name'] ?></option>
					<?php endforeach; ?>
				</select>
				<button class="btn"><i class="icon-share-alt"></i> Move Topic</button>
			</div>
		</form>


		<form class="pull-right" action="/forum/spotlight_topic" method="POST">
			<div class="control-group">
				<input type="hidden" name="topic_id" value="<?php echo $topic['topic_id'] ?>" />
				<input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
				<button class="btn btn-warning"><i class="icon-star icon-white"></i> Make topic spotlight</button>
			</div>
		</form>

	</div>
<?php endif ?>

<script type="text/javascript">
var topic = {
	id: <?php echo $topic['topic_id'] ?>,
	status: '<?php echo $topic['topic_status'] ?>',
	last_post: <?php echo $topic['total_posts'] ?>,
	submitting: false,
	post_html: "",
	posters: <?php echo (json_encode(array_values(array_unique($authors)), JSON_NUMERIC_CHECK)); ?>
}
</script>