/*!
 *	(Hello!) Chirp Notifications
 *	Version 1.0 - Chirp is hand-coded by Crysandrea developers. Please be respecful.
*/
$(document).ready(function(){
	var favicon = {
		toggle: 0,
		interval: false,
		docHead: document.getElementsByTagName("head")[0],
		change: function(iconURL) {
			if (arguments.length == 2) document.title = optionalDocTitle;
			this.addLink(iconURL, "icon");
			this.addLink(iconURL, "shortcut icon");
		},
		addLink: function(iconURL, relValue) {
			var link = document.createElement("link");
			link.type = "image/x-icon";
			link.rel = relValue;
			link.href = iconURL;
			this.removeLinkIfExists(relValue);
			this.docHead.appendChild(link);
		},
		removeLinkIfExists: function(relValue) {
			var links = this.docHead.getElementsByTagName("link");
			for (var i = 0; i < links.length; i++) {
				var link = links[i];
				if (link.type == "image/x-icon" && link.rel == relValue) {
					this.docHead.removeChild(link);
					return;
				}
			}
		}
	}

	// Chirp. A notification library.
	var chirp = {
		io: {},
		notification_count: 0,
		visible_notifications: 0,
		visible_limit: 8,
		collapsed_queue: false,
		obj_queue: [],
		url_title: 'Crysandrea',
		append_notification: function(json){
			var new_notification = $('#notification_template').clone();
			new_notification.attr('id', '');
			new_notification.addClass(json.type+'_notification');
			new_notification.fadeIn(300, function(){
				new_notification.removeClass('hide');
			});
			new_notification.find('.notification_bubble').attr('href', json.url);
			new_notification.find('.avatar_headshot').attr('src', '/images/avatars/'+json.uid+'_headshot.png');
			new_notification.find('.notification_content').html(json.text.replace(json.from, '<strong>'+json.from+'</strong>'));

			$('.instant_notifications').prepend(new_notification);
			chirp.notification_count++;

			if(chirp.visible_notifications >= chirp.visible_limit && ! chirp.collapsed_queue){
				var notification_obj = $('.instant_notification').eq(chirp.visible_limit);
				var buffer_obj = notification_obj.clone();
				if((chirp.notification_count - chirp.visible_notifications) == 1){
					notification_obj.slideUp(600, function(){
						$(this).remove();
					});
				} else {
					notification_obj.remove();
				}

				chirp.append_queue(buffer_obj);
			} else {
				// favicon.interval = setInterval(function(){
				// 	if(favicon.toggle % 2 == 1){
				// 		favicon.change('/favicon_alert.ico');
				// 		document.title = '(!) '+chirp.url_title;
				// 		favicon.toggle++;
				// 	} else {
				// 		favicon.change('/favicon.ico?2');
				// 		document.title = '('+chirp.notification_count+') '+chirp.url_title;
				// 		favicon.toggle++;
				// 	}
				// }, 800);

				chirp.visible_notifications++;
			}
		},
		append_queue: function(obj){
			if((chirp.notification_count - chirp.visible_notifications) == 1){
				$('#notification_queue').fadeIn();
			}
			chirp.obj_queue.push(obj);
			$('#extended_notification_count').text((chirp.notification_count - chirp.visible_notifications));
		},
		collapse_queue: function(){
			chirp.collapsed_queue = true;
			$('.instant_notifications').prepend(chirp.obj_queue);
			chirp.obj_queue = [];
		},
		events: {
			mailbox: function(json){
				var link_obj = $('ul#sidebar_navigation #mailbox a');
				if(link_obj.find('span.small_notification_bubble').length){
					var counter = link_obj.find('span.small_notification_bubble');
					counter.text(parseInt(counter.text())+1);
				} else {
					link_obj.prepend($('<span class="small_notification_bubble">1</span>'));
				}
			},
			snowball: function(json){
				var snow_chirp = $('.snowball_notification').first();
				snow_chirp.attr('data-url', json.url);
				snow_chirp.attr('data-user-id', json.uid);
				if (json.url.length > 8) {
					snow_chirp.on('click', function(){
						$.ajax({
						    type: "POST",
						    url: $(this).attr('data-url'),
						    data: { victim: $(this).attr('data-user-id') },
						    dataType: "json",
						    success: function(json){
						        snow_chirp.fadeOut(300, function(){
						        	$(this).remove();
						        });

						        chirp.visible_notifications--;
						        chirp.notification_count--;

						        if( ! chirp.notification_count){
						        	document.title = chirp.url_title;
						        	clearInterval(favicon.interval);
						        	favicon.change('/favicon.ico?2');
						        }
						    },
						    error: function(xhr, status, error){
						    	snow_chirp.fadeOut(300, function(){
						    		$(this).remove();
						    	});

						    	chirp.visible_notifications--;
						    	chirp.notification_count--;
						    }
						});
						return false;
					});
				} else {
					snow_chirp.on('click', function(){
						alert('You have to wait at least 30 seconds before launching another snowball');

						snow_chirp.fadeOut(300, function(){
							$(this).remove();
						});

						chirp.visible_notifications--;
						chirp.notification_count--;

						if( ! chirp.notification_count){
							document.title = chirp.url_title;
							clearInterval(favicon.interval);
							favicon.change('/favicon.ico?2');
						}

						return false;
					});
				}
			}
		}
	};

	$('.remove_notification').live('click', function(){
		$(this).parent().fadeOut(300, function(){
			$(this).remove();
		});

		chirp.visible_notifications--;
		chirp.notification_count--;

		if( ! chirp.notification_count){
			document.title = chirp.url_title;
			clearInterval(favicon.interval);
			favicon.change('/favicon.ico?2');
		}

		return false;
	});

	$('.more_instant_notifications').live('click', function(){
		chirp.collapse_queue();
		$(this).fadeOut(200);
		return false;
	});

	var channel_ids = [];
	channel_ids.push(chirp_key);
	if(typeof io !== 'undefined'){
		chirp.io = io.connect(raw_url+':8001/');

		chirp.io.on('connect', function(){
			chirp.io.emit('identifier', user.id);
			chirp.io.emit('notification_subscribe', channel_ids);
			chirp.io.on('new_chirp', function(data){
				chirp.append_notification(data.message);
				if(typeof chirp.events[data.message.type] != 'undefined'){
					chirp.events[data.message.type](data.message);
				}
			});
		});
	}

	chirp.url_title = document.title;

	$(window).focus(function() {
		document.title = chirp.url_title;
		clearInterval(favicon.interval);
		favicon.change('/favicon.ico?2');
	});

});