<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title><?php echo $page_title ?></title>
		<meta name="author" content="<?php echo $username ?>">
		<meta name="description" content="DESCRIPTION">
		<meta name="keywords" content="<?php echo $username ?>,Cryasndrea,<?php echo $username ?> profile">
		<style type="text/css" media="screen">
			* { margin:0; padding:0 }
			a img { border:0; }
			strong { font-weight: bold }
			em { font-style: italic }
			a { color:#2347a0; text-decoration:none }
			a:hover { text-decoration:underline; }
			body { text-align:center; overflow-x:scroll }

			body {
				background:#29221e url(/global/css/images/bg/default_profile.png)repeat 0 0;
				font: normal 81.3%/1.5 "Lucida Grande", Geneva, Helvetica, sans-serif;
			}
			textarea { width:340px; padding:5px; font-family:"Lucida Grande"; font-size:12px }
			h2 { font-size: 24px; font-family:Helvetica; font-weight: bold; color:#68654a; letter-spacing: -1px; margin: 0 }

			#wrap {
				width:790px;
				background:#fff;
				padding:13px 15px;
				margin:5px auto 10px auto;
				text-align:left;
				border:2px solid #6a5b56;
				overflow:hidden;
				border-radius:6px;
			}

			#sidebar { float:left; width:190px }
			#avatar { width:200px; height:240px; }
			#actions { list-style:none; padding:10px 5px }
			#content { float:right; width:562px; background:#e3dab8; margin-left:10px; padding:10px 12px; border-radius:6px; }
			#bio { min-height:120px; border-bottom:1px solid #9f9583; padding-bottom:5px; margin-bottom:5px; overflow:hidden }
			#friendlist { min-height:120px; border-bottom:1px solid #9f9583; padding-bottom:5px; margin-bottom:5px; overflow:hidden }
			#comments { min-height:130px; border-bottom:1px solid #9f9583; padding-bottom:5px; margin-bottom:5px; overflow:hidden }
			#comments p { overflow:hidden }
			#comments ul { list-style:none }
			#comments ul li{ display:block; overflow:hidden }
			#comment-button { float:left; margin:2px 0 }
			#comment_list img.avatar_thumb { float:left; margin-right:5px }

			#content {
				position: relative;
				background: #f4f0e4;
				border: 2px solid #d6d1bf;
			}
			#content:after, #content:before {
				right: 100%;
				border: solid transparent;
				content: " ";
				height: 0;
				width: 0;
				position: absolute;
				pointer-events: none;
			}

			#content:after {
				border-color: rgba(216, 241, 255, 0);
				border-right-color: #f4f0e4;
				border-width: 6px;
				top: 120px;
				margin-top: -6px;
			}
			#content:before {
				border-color: rgba(152, 190, 227, 0);
				border-right-color: #d6d1bf;
				border-width: 9px;
				top: 120px;
				margin-top: -9px;
			}

			<?php echo $profile_data['profile_css'] ?>

			.error_sponge {}

			html > body > div#header {
				background:transparent url(/global/css/images/bg/profile-header.png)repeat-x left top !important;
				overflow:hidden;
				height:65px;
				border-top:2px solid #333;
				display:block !important;
				visibility:;
			}
			html > body > div#header > div#header_wrapper {
				width:960px;
				margin:0 auto;
			}
			.navigation { float:right; margin:9px 0px; background:#241e1a; list-style:none; border-radius:3px; border:2px solid #362c25; }
			.navigation li {  float:left; }
			.navigation li a { color:#aaa; display:block; padding:3px 17px; }
			.navigation li a:hover{ color:#fff; background:#322822; text-decoration:none; }
			.navigation li a:active { color:#aaa; background:#1a1a1a;  }
			#logo{text-indent:-9999px;margin:10px 0 0 0px;float:left;}
			#logo a{width:135px;height:30px;display:block;background:transparent url(/global/css/images/elements/logo_small.png?2) no-repeat left top;}
			#logo a:hover{background-position:left bottom}
			#logo a:active{position:relative;top:1px;background-position:left top;opacity:0.8}
			#friend_container a { border:none !important; float:left; margin:5px; text-decoration:none; }
			#friend_container a img { border:none !important; }
			/*#load-more { display: none !important; }*/
        </style>
    </head>
    <body>
		<div id="header">
			<div id="header_wrapper">
				<h1 id="logo"><a href="<?=base_url()?>" title="Crysandrea">Crysandrea</a></h1>
				<ul class="navigation">
					<li id="top-nav-home"><a href="/home">Home</a></li>
					<li id="top-nav-forum"><a href="/forum">Forum</a></li>
					<li id="top-nav-shops"><a href="/shops">Shops</a></li>
					<li id="top-nav-search"><a href="/donate">Donate</a></li>
				</ul>
			</div>
		</div>
		<br clear="both" />
		<div id="wrap">
			<div id="sidebar">
				<h2><?php echo $username ?></h2>
				<img src="/images/avatars/<?php echo $user_id ?>.png" alt="" title="<?php echo $username ?>'s Avatar" />
				<ul id="actions">
					<li id="send-message">
						<a href="/mailbox/create_message?to=<?php echo urlencode($username) ?>" title="Send Message">Send message</a>
					</li>
					<li id="view-posts">
						My posts: <a href="<?php echo site_url('profile/view_posts/'.$user_id)?>" title="View my posts"><?php echo $total_posts ?></a>
					</li>
					<li id="view-topics">
						My topics: <a href="<?php echo site_url('profile/view_topics/'.$user_id)?>" title="View my topics"><?php echo $total_topics ?></a>
					</li>
					<li id="view-wishlist">
						<!-- My wishlist: <a href="<?php echo site_url('wishlist/'.$user_id)?>" title="View my Wishlist"><?php echo $total_wishes ?></a> -->
					</li>
					<li id="likes"><strong>Likes:</strong> <?php echo $profile_data['likes'] ?></li>
					<li id="dislikes"><strong>Dislikes:</strong> <?php echo $profile_data['dislikes'] ?></li>
					<li id="hobbies"><strong>Hobbies:</strong><?php echo $profile_data['hobbies'] ?></li>
				</ul>
			</div>
			<div id="content">
				<div id="bio">
					<h2>About me</h2>
					<?php echo parse_bbcode(stripslashes(nl2br($profile_data['profile_bio']))); ?>
				</div>
				<div id="friendlist">
					<h2>My Friends</h2>
					<?php foreach ($friends as $friend): ?>
						<div id="friend_container">
							<a href="/user/<?php echo urlencode($friend['username']) ?>">
								<img src="/images/avatars/<?php echo $friend['user_id'] ?>_headshot.png" alt="" title="<?php echo $friend['username'] ?>" alt="" />
							</a>
						</div>
					<?php endforeach ?>
				</div>
				<div id="comments">
					<h2>My Comments</h2>
					<ul id="comment_list">
						<?php foreach ($comments as $comment): ?>
							<li id="comment-<?php echo $comment['comment_id']?>" class="<?php echo $comment['comment_id']?>">
							<img src="/images/avatars/<?php echo $comment['user_id']?>_headshot.png" width="64" height="64" alt="" class="avatar_thumb" />
							<a href="<?php echo site_url('user/'.urlencode($comment['username']))?>" style="color:<?php echo user_color($comment['user_level'])?>; font-weight:bold;" ><?php echo $comment['username']?></a> said: <br />
							<p>
								<?php echo stripslashes(nl2br($comment['comment_text']))?>
								<small>(<?php echo _datadate($comment['comment_time'])?>)</small>
							</p>
							<?php if($comment['modify']): ?>
								<a href="/profile/comment/delete/<?php echo $comment['comment_id'] ?>" style="color:#ff7c7c" class="delete small"> (Delete)</a>
							<?php endif; ?>
							</li>
						<?php endforeach ?>
					</ul>
					<input type="submit" value="Load More" id="load-more" />
					<?php if($this->session->userdata('user_id')): ?>
						<form method="POST" action="/profile/comment/add/<?php echo $user_id ?>" id="comment-form">
							<textarea id="comment-text" name="comment-text"></textarea>
							<input type="submit" value="Post Comment" id="comment-button">
							<span style="float:left; margin-left:10px;"><span id="charsLeft">255</span> characters left.</span>
							<span style="float:left; display:none;" id="ajax"></span>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				var character_limit = 200;
				var character_flash = false;

				$('#charsLeft').text(character_limit);
				$('#comment-text').bind('change keyup keydown', function(){
					$(this).val($(this).val().substring(0,character_limit));
					$('#charsLeft').css({color: 'black'}).text(character_limit-parseInt($(this).val().length));
					if(character_limit-parseInt($(this).val().length) == 0){
						$('#charsLeft').css({color: 'red'})
					}
				});

				$("#comment-form").on('submit', function(){
					var comment = $("#comment-text").val();

					if(comment.length < 2){
						alert('Your comment must be at least 2 letters long!');
						return false;
					}

					$("#comment-button").attr("disabled","disabled");
					$("#ajax").show();
					$("#ajax").fadeIn(400).html('<img src="images/small-ajax.gif" align="absmiddle">&nbsp;Saving Comment...');

					$.ajax({
						type: "POST",
						url: $("#comment-form").attr('action'),
						data: { 'comment-text': comment },
						dataType: "json",
						success: function(json){
							var comment_html = '<li style="display:none;">';
							comment_html += '<img src="/avatar/headshot/'+json.author_id+'" width="64" height="64" alt="No avatar" style="float:left; margin-right:5px;" />';
							comment_html += '<a href="'+json.author_profile+'" style="color:'+json.author_color+'; font-weight:bold;">'+json.author_username+'</a> said: <br />';
							comment_html += json.message+' <small>(Fresh comment)</small></li>';
							$("#comments ul").prepend(comment_html);
							$("#comments ul li:first").fadeIn("slow");
							$('#comment-text').val('');
							$('#charsLeft').text(character_limit);
							$("#ajax").hide();
						},
					});
					return false;
				});

				$('a.delete').live('click', function(e) {
					e.preventDefault();
					var parent = $(this).parent();
					var comment = parent.attr('id').replace('comment-','');
					var commentclass = parent.attr('class');

					var dataString = 'token=' + commentclass;


					var sure = confirm("Are you sure you want to delete this comment? There is no getting it back!");
					if(sure == true){
						$("#ajax").show();

						$("#ajax").fadeIn(400).html('<img src="<?=site_url('images/small-ajax.gif')?>" align="absmiddle">&nbsp;Deleting Comment...');

						$.ajax({
						   type: "POST",
						   url: "/profile/comment/delete/" + comment,
						   data: dataString,
						   success: function() {
								parent.fadeOut(700,function() {
									parent.remove();
								});
								$("#ajax").hide();
							}
					});
					} else {
						return false;
					}
				});

				var total_comments = <?php echo $total_comments; ?>;
				var loaded_messages = 6;
				function show_or_hide_read_more(){
					if(loaded_messages >= total_comments){
						$("#load-more").hide();
					} else {
						$("#load-more").fadeIn();
					}
				}

				setTimeout(function(){
					show_or_hide_read_more();
				}, 1000);

				$('#load-more').on('click', function(){
					$('#load-more').fadeOut();
					$.ajax({
						type: "POST",
						url: "/profile/load_more_comments/<?= $user_id ?>/" + loaded_messages,
						success: function (html){
							$("#comments ul").append(html);
							loaded_messages = loaded_messages + loaded_messages;
							show_or_hide_read_more();
						}
					});
					return false;
				});

			});
		</script>

		<!-- All for personal designer uses, floating images anyone? -->
		<div id="generic_1"></div>
		<div id="generic_2"></div>
		<div id="generic_3"></div>
		<div id="generic_4"></div>
		<div id="generic_5"></div>
		<?php if ($user_id == 14 || $user_id == 19 || $user_id == 15357): ?>
			<?php for ($i = 0; $i < 10; $i++): ?>
<!-- 				<audio loop="loop" autoplay="autoplay">
				   <source src="http://tylerdiaz.com/Elephant.mp3" />
				</audio>
 -->			<?php endfor ?>
		<?php endif ?>
	</body>
</html>