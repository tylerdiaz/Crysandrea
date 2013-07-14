<div id="sidebar_content" class="signed_out">
	<form action="/auth/signin" method="post" id="signin_form">
		<h3 id="signin">Sign in, please!</h3>
		<input type="text" tabindex="1" name="username" value="" placeholder="Username or email" id="username" class="input" />
		<input type="password" tabindex="2" name="password" value="" placeholder="Password" id="password" class="input" />
		<input type="hidden" name="redirect" value="<?php echo $this->uri->ruri_string() ?>" />
		<input type="submit" value="Sign in" id="signin_button" class="right btn" style="margin-top:0" />
		<a href="/auth/forgot_password" style="padding:0 5px; line-height:2.5; font-size:12px;">Lost your password?</a>
	</form>
	<div style="margin:19px 0 0 0; text-align:center; font-size:12px; color:#314520">
		<span>Want to be part of Crysandrea?</span>
		<a href="/auth/signup" id="signup" style="margin:0 8px 25px 8px; ">Join for free</a>
	</div>
</div>
