/*! Crysandrea - Javascript for auth/signup - Created at November 11, 2012*/
$(document).ready(function(){

	var username_timeout = false, email_timeout = false, password_timeout = false;

	$('#signup_username').on('keydown blur', function(){
		if ($('#signup_username').val().length) {
			clearTimeout(username_timeout);
			username_timeout = setTimeout(function(){
				$.ajax({
				    type: "POST",
				    url: "/auth/validate/username",
				    data: { q: $('#signup_username').val() },
				    dataType: "json",
				    success: function(json){
				    	console.log(json);
				        if(typeof json.error == 'undefined'){
				        	$('#signup_username').parent().find('.help-block').css({ color: "#16a604"}).text(json.success);
				        } else {
				        	$('#signup_username').parent().find('.help-block').css({ color: "red"}).text(json.error);
				        }
				    },
				});
			}, 500);
		};
	});

	$('#signup_email').on('keydown blur', function(){
		if ($('#signup_email').val().length) {
			clearTimeout(email_timeout);
			email_timeout = setTimeout(function(){
				$.ajax({
				    type: "POST",
				    url: "/auth/validate/email",
				    data: { q: $('#signup_email').val() },
				    dataType: "json",
				    success: function(json){
				        if(typeof json.error == 'undefined'){
				        	$('#signup_email').parent().find('.help-block').css({ color: "#16a604"}).text(json.success);
				        } else {
				        	$('#signup_email').parent().find('.help-block').css({ color: "red"}).text(json.error);
				        }
				    },
				});
			}, 500);
		};
	});

	$('#signup_password').on('keydown blur', function(){
		clearTimeout(password_timeout);
		password_timeout = setTimeout(function(){
			var password_text = $('#signup_password').val();
			if(password_text.length > 5){
				$('#signup_password').parent().find('.help-block').css({ color: "green"}).text("Good job! Your password is OK.");
			} else {
				if (password_text.length > 0) {
					$('#signup_password').parent().find('.help-block').css({ color: "red"}).text("Your password is too short. It must be at least 6 characters long.");
				} else {
					$('#signup_password').parent().find('.help-block').text("");
				}
			}
		}, 500);
	});
});