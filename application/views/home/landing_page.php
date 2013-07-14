<!DOCTYPE html>
<html lang="en" id="crysandrea_homepage">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Crysandrea</title>
		<meta name="author" content="Tyler Diaz">
		<meta name="description" content="Crysandrea connects you to a world filled with people from all over the planet gathered to chat, play, and share their creativity by showing off some of the most unique avatars on the web.">
		<meta name="keywords" content="crysandrea, avatar community, roleplaying game, games, virtual avatar">
		<meta name="robots" content="FOLLOW, INDEX" />
		<meta name="copyright" content="Crysandrea 2012" />
		<link rel="stylesheet" href="/global/css/main.css" type="text/css">
		<link rel="stylesheet" href="/global/css/homepage.css" type="text/css">
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
	</head>
	<body class="home">
		<div id="structure">
			<div id="home_header">
				<h1 id="logo"><a href="/?ref=logo" title="Home" tabindex="0">Crysandrea</a></h1>
				<form action="/auth/signin?ref=homepage" method="POST" id="homepage_signin">
					<div class="helper_bar">
						<input type="checkbox" id="remember_me" name="remember_me" style="margin-top:-1px"/> <label for="remember_me" name="remember_me">Keep me signed in</label> <a href="/auth/forgot_password">I forgot my password</a>
					</div>
					<input type="text" class="signin_data" placeholder="Username or Email" name="username" tabindex="1" />
					<input type="password" class="signin_data" placeholder="Password" name="password" tabindex="2" />
					<button type="submit" id="signin_button">Sign in</button>
				</form>
			</div>
			<div id="home_epicenter">
				<h1>The hangout for fun and friends</h1>
				<h3>Crysandrea connects you to a world filled with people from all over the planet gathered to chat, play, and share their creativity by showing off some of the most unique <span id="avatar_abbr">avatars</span> on the web.</h3>
				<h4>Thousands of awesome people already have their own avatar. Join today to see what all the fun is about!</h4>
				<a href="/auth/signup" id="signup">Join for free</a>
				<div id="avatar_explination"></div>
			</div>
			<div id="home_footer">
				<div style="float:left">
					<strong>Links for the curious:</strong> <a href="/forum">Forums</a> &bull; <a href="/shops">Shops</a> &bull; <a href="/market">Marketplace</a>  &bull; <a href="/donate">Monthly items</a>
				</div>
				<div style="float:right">
					&copy;2012 Crysandrea ( <a href="/general/read_document/tos">Code of Conduct</a> &bull; <a href="/general/read_document/privacy">Private Policy</a> )
				</div>
			</div>
		</div>
	</body>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="global/js/placeholder.js" type="text/javascript" charset="utf-8"></script>
	<script src="global/js/home/landing_page.js" type="text/javascript" charset="utf-8"></script>
</html>