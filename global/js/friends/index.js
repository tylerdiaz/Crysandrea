/*! Crysandrea - Javascript for friends/index - Created at August 28, 2012*/
$(document).ready(function(){
	var success_ux = 0;
	var friend_timeout = false, cancel_countdown = false;

	$('.friend_bunch li a').click(function(){
		var user_id = $(this).attr('data-userid');

		if(cancel_countdown) clearInterval(cancel_countdown);
		if(friend_timeout) clearTimeout(friend_timeout);
		$('.remove_confirm').remove();
		$('.remove_friend').show();

		$.ajax({
		    type: "GET",
		    url: "/friends/get_user_data/"+user_id,
		    dataType: "json",
		    success: function(json){
		    	if(success_ux == 5){
		    		setTimeout(function(){
		    			$('#friend_instructions').fadeTo(200, 0.1).attr('src','/images/headers/good_work.jpg').fadeTo(500, 1);
		    			setTimeout(function(){
		    				$('#friend_instructions').fadeOut(1000);
		    			}, 3500);
		    		}, 500)
		    	}
		    	$('#data_username').text(json.username);
		    	$('#user_timestamp').text('Last seen online '+json.user_last_login);
		    	$('#user_avatar').attr('src', '/images/avatars/'+json.user_id+'.png');
		    	success_ux++;

		    	$('.remove_friend').attr('href', '/friends/remove_friend/'+json.user_id);
		    	$('.remove_friend').attr('data-friend-id', json.user_id);
		    	$(".profile_link").attr('href', '/user/'+json.username);
		    	$(".send_trade").attr('href', '/trade/new?to='+json.username);
		    	$(".send_message").attr('href', '/mailbox/create_message?to='+json.username);
		    	// TODO: Make the links href[] change dynamically with the data from JSON
		    	// TODO: Remove friend
		    },
		});

		return false;
	});

	$('.cancel_removal').live('click', function(){
		clearInterval(cancel_countdown);
		clearTimeout(friend_timeout);
		$('.remove_confirm').remove();
		$('.remove_friend').show();

		return false;
	});

	$('.quick_removal').live('click', function(){
		var remove_obj = $('.remove_friend');

		$.ajax({
		    type: "POST",
		    url: "/friends/remove_friend",
		    data: { friend_id: remove_obj.attr('data-friend-id') },
		    dataType: "json",
		    success: function(json){
		    	window.location.replace("/friends/?removed");
		    }
		});

		return false;
	});

	$('.remove_friend').live('click', function(){
		var remove_obj = $(this);
		remove_obj.hide();
		remove_obj.after('<span class="remove_confirm right">Removing friend in <span id="cancel_countdown">5</span> (<a href="#" class="cancel_removal">Cancel</a> &bull; <a href="#" class="quick_removal">Remove</a>)</span>');

		var cancel_countdown = setInterval(function(){
			$('#cancel_countdown').text(parseInt($('#cancel_countdown').text())-1);
			if(parseInt($('#cancel_countdown').text())-1 >= 5){
				alert('There was a problem removing this user.');
				clearInterval(cancel_countdown);
				clearTimeout(friend_timeout);
				$('.remove_confirm').remove();
				$('.remove_friend').show();
			}
		}, 1000);

		friend_timeout = setTimeout(function(){
			$.ajax({
			    type: "POST",
			    url: "/friends/remove_friend",
			    data: {
			    	friend_id: remove_obj.attr('data-friend-id')
			    },
			    dataType: "json",
			    success: function(json){
			    	window.location.replace("/friends/?removed");
			    }
			});
		}, 5000);

		return false;
	});
});