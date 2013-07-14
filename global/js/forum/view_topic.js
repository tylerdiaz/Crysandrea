/*! Crysandrea - Javascript for forum/view_topic - Created at September 5, 2012*/
$(document).ready(function(){

	$(".launch_snowball").live('click', function(){
		var button = $(this);
		$.ajax({
		    type: "POST",
		    url: "/snowball/attack",
		    data: { victim: button.attr('data-user-id') },
		    dataType: "json",
		    success: function(msg){
		    	button.after('Nice shot!');
		        button.remove();
		    },
		    error: function(xhr, status, error){
		        alert("You cannot throw another snowball so soon!");
		    }
		});
		return false;
	});

	topic.title_bar = document.title;
	topic.unloaded_posts = 0;
	topic.new_post_interval = 3000;
	topic.load_new_posts = function(){
		topic.poll = setTimeout(function(){
			$.ajax({
			    type: "POST",
				url: '/forum/load_new_posts/'+topic.id+'/'+topic.last_post,
			    cache: false,
			    async: true,
			    dataType: "json",
				success: function(json){
					if(json.total_posts > 0){
						topic.last_post = json.new_post_id;
						topic.post_html += json.post_html;
						topic.unloaded_posts += json.total_posts;
						$('.new_topic_posts #total_new_posts').text(topic.unloaded_posts+' new '+(topic.unloaded_posts > 1 ? 'posts' : 'post'));
						$('.new_topic_posts').slideDown(750);
						document.title = '('+topic.unloaded_posts+') '+topic.title_bar;
						topic.new_post_interval = 3000;
					} else {
						topic.new_post_interval += 500;
					}

					topic.load_new_posts(); // recurse
				},
				error: function(){
					topic.new_post_interval += 2000;
					topic.load_new_posts();
				}
			});
		}, topic.new_post_interval);
	};

	topic.load_new_posts();

	$('.new_topic_posts').bind('click', function(){
		$(this).fadeOut(500);
		$('#topic_post_container').append(topic.post_html);
		topic.unloaded_posts = 0;
		topic.post_html = "";
		document.title = topic.title_bar;
		return false;
	})

	$('#send_post_message').submit(function(e){
		var action_url = $('#send_post_message').attr('action');
		if(topic.submitting == false){
			topic.submitting = true;
			topic.ajax = $.ajax({
			    type: "POST",
			    url: action_url,
			    data: { message: encodeURIComponent($("#message").val()) },
			    dataType: "json",
			    error: function(){
			        alert('Something went wrong when trying to post your reply. Copy your post, refresh and try again please.');
			    },
			    success: function(json){
			    	$("#message").val('');
			    	clearTimeout(topic.poll);
			    	topic.last_post++;
			    	topic.load_new_posts();
					topic.submitting = false;

					$('#topic_post_container').append(json.post_html);
			    }
			});
		}

		e.preventDefault();
	});

	$(".reply_at").live('click', function(){
		var message = $("#message").val();
		if(message.length > 0){
			message += "\n@"+$(this).text()+": ";
		} else {
			message += "@"+$(this).text()+": ";
		}

		$("#message").val(message);
	});

	$("#message").keydown(function(e){
		if ((e.keyCode == 13 && e.shiftKey) || (e.keyCode == 83 && e.ctrlKey)){
			$("#send_post_message").submit();
			e.preventDefault();
		}
	});

	$('.bookmark').click(function(){
		$.ajax({
		    type: "POST",
		    url: "/forum/toggle_bookmark/"+topic.id,
		    cache: false,
		    async: true,
		    dataType: "json",
		    success: function(msg){
		    	$('.bookmark').hide();
		    	$('.success_bookmark').show();
		    },
		});
		return false;
	});

	$('.remove_bookmark').click(function(){
		$.ajax({
		    type: "POST",
		    url: "/forum/toggle_bookmark/"+topic.id,
		    cache: false,
		    async: true,
		    dataType: "json",
		    success: function(msg){
		    	$('.bookmark').show();
		    	$('.success_bookmark').hide();
		    },
		});

		return false;
	});

	$('#message').asuggest(topic.posters, {
		'endingSymbols': ' ',
		'stopSuggestionKeys': [$.asuggestKeys.RETURN],
		'minChunkSize': 1,
		'delimiters': '\n'
	});

});