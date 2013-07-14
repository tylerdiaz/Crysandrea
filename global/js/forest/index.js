/*! Crysandrea - Javascript for forest/index - Created at Oct 28, 2012*/
function percent(num, limit) { return Math.round(((num / limit) * 100));}
function number_format(number,decimals,dec_point,thousands_sep){number=(number+'').replace(/[^0-9+\-Ee.]/g,'');var n=!isFinite(+number)?0:+number,prec=!isFinite(+decimals)?0:Math.abs(decimals),sep=(typeof thousands_sep==='undefined')?',':thousands_sep,dec=(typeof dec_point==='undefined')?'.':dec_point,s='',toFixedFix=function(n,prec){var k=Math.pow(10,prec);return''+Math.round(n*k)/k};s=(prec?toFixedFix(n,prec):''+Math.round(n)).split('.');if(s[0].length>3){s[0]=s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,sep)}if((s[1]||'').length<prec){s[1]=s[1]||'';s[1]+=new Array(prec-s[1].length+1).join('0')}return s.join(dec)}
function rand(min,max){var argc=arguments.length;if(argc===0){min=0;max=2147483647}else if(argc===1){throw new Error('Warning: rand() expects exactly 2 parameters, 1 given');}return Math.floor(Math.random()*(max-min+1))+min}

$(document).ready(function(){
	var js_hunts = 0;
    var loading = false;
	var caught_bug = 0;
	var berry_queue = false;
	var berry_munches = [
		'Yummy! That one tasted like strawberries!',
		'Delicious! That one tasted like cherries!',
		'Exquisite! That one tasted like blueberries!',
		'Divine! That one tasted like grapes!',
		'Mmm... tasty! That one tasted like juneberries!',
		'Scrumptious! That one tasted like salmonberry!',
		'Appetizing! That one tasted like mulberries!',
		'Heavenly! That one tasted like redcurrants!',
		'Enticing! That one tasted like gooseberries!',
		'Delightful! That one tasted like jelly jam!',
		'Delectable! That one tasted like honey!',
		'Interesting, that one tasted like sugar rocks...',
		'Divine! That one tasted like pinnaple.',
		'Delicious, that one tasted like kiwi!',
		'Wonderful, that one tasted like an apricot!',
		'Enticing! That one tasted like a star fruit!',
	];

    var forest = {
    	occupied: false,
    	success_timeout: false,
    	error_timeout: false,
    	hunting_permission: true,
    	palla_flash: false,
    	clear_int: function(obj){
    		return parseInt($("#"+obj).text().split(',').join(''));
    	},
        toggle_ajax: function(toggle){
        	if ( ! toggle) {
        		forest.occupied = false;
        		$("#cover_pane").stop().fadeOut(200);
        		$(".forest_ajax_bubble").stop().fadeOut(200);
        	} else {
        		forest.occupied = true;
        		$("#cover_pane").stop().fadeIn(200);
        		$(".forest_ajax_bubble").stop().fadeIn(400);
        	}
        },
        decrease_energy: function(amount){
            amount = (typeof(amount) != 'undefined' ? amount : 1);
            var new_energy = (forest.clear_int('energy')-amount);
            var new_percent = (percent(new_energy, forest.clear_int('max_energy')));
			$("#energy").text(number_format(new_energy));
			$(".energy_bar .value").animate({width: (new_percent)+'%'}, 200);
		},
		add_energy: function(amount){
			amount = (typeof(amount) != 'undefined' ? amount : 1);
			var new_energy = (forest.clear_int('energy')+amount);
			var max_energy = forest.clear_int('max_energy');
			var new_percent = (percent(new_energy, max_energy));
			$("#energy").text(number_format(Math.min(new_energy, max_energy)));
			$(".energy_bar .value").animate({width: (new_percent)+'%'}, 200);
		},
		add_exp: function(exp){
			var new_exp = (forest.clear_int('exp_user')+exp);
			var new_percent = (percent(new_exp, forest.clear_int('exp_level')));

			$("#exp_user").text(number_format(new_exp));
			$(".exp_bar .value").animate({width: (new_percent)+'%'}, 300);
			$(".add_up").show().css({ opacity: 0.8}).text("+"+exp).animate({marginTop:"-30px", opacity:0.6}, 400, function(){
				$(".add_up").animate({opacity:0}, 150, function(){
					$(".add_up").css({marginTop:"0px"}).hide();
				});
			});
		},
		add_palladium: function(amount){
			var new_palla = forest.clear_int('palladium_count')+parseInt(amount);
			$("#palladium_count").text(number_format(new_palla)).css({ backgroundColor: '#cbe3a3'}).stop().animate({ backgroundColor: '#ffff00', color: '#888800' }, 200);
			clearTimeout(forest.palla_flash);
			forest.palla_flash = setTimeout(function(){
				$("#palladium_count").animate({color: '#334c00', backgroundColor: '#cbe3a3'}, 1000, function(){
					$(this).css({ backgroundColor: 'transparent'});
				});
			}, 1000)
		},
		remove_palladium: function(amount){
			var new_palla = forest.clear_int('palladium_count')-parseInt(amount);
			$("#palladium_count").text(number_format(new_palla)).css({ backgroundColor: '#cbe3a3'}).stop().animate({ backgroundColor: '#ffff00', color: '#888800' }, 200);
			clearTimeout(forest.palla_flash);
			forest.palla_flash = setTimeout(function(){
				$("#palladium_count").animate({color: '#334c00', backgroundColor: '#cbe3a3'}, 1000, function(){
					$(this).css({ backgroundColor: 'transparent'});
				});
			}, 1000)
		},
		error: function(error){
			forest.occupied = true;

			$("#cover_pane").stop().fadeIn(200);
			$(".forest_alert_bubble span").text(error);
			$(".forest_alert_bubble").stop().fadeIn(400);

			clearTimeout(forest.error_timeout);

			forest.error_timeout = setTimeout(function(){
				forest.occupied = false;
				$(".forest_alert_bubble").stop().fadeOut(200, function(){
					$(".forest_alert_bubble span").text('');
				});
				setTimeout(function(){
					$("#cover_pane").stop().fadeOut(300);
				}, 100);
			}, 4200);
		},
		success: function(notice){
			forest.occupied = true;

			$("#cover_pane").stop().fadeIn(200);
			$(".forest_success_bubble span").text(notice);
			$(".forest_success_bubble").stop().fadeIn(400);

			clearTimeout(forest.success_timeout);

			forest.success_timeout = setTimeout(function(){
				forest.occupied = false;
				$("#cover_pane").stop().fadeOut(200);
				$(".forest_success_bubble").stop().fadeOut(200, function(){
					$(".forest_success_bubble span").text('');
				});
			}, 4200);
		},
		reload_leaderboards: function(){
			js_hunts = 0;
			$.getJSON("/forest/get_leaderboards", function(json){
				var leaderboard_html = "";
				for(var i = 0; i < json.length; i++){
					leaderboard_html += '<a href="/forest/museum/'+json[i]['username_url']+'" class="leaderboard_thumbnail" title="'+json[i]['username']+'\'s Museum">';
					leaderboard_html += '<img src="/images/avatars/'+json[i]['user_id']+'_headshot.png" width="56" height="56" alt="">';
					leaderboard_html += '<span>#'+(i+1)+'</span>';
					leaderboard_html += '</a>';
				}

				$("#leaderboard_container").html(leaderboard_html);
				$('.leaderboard_thumbnail').tooltip({
					placement: 'bottom'
				});
			});
		}
    }

    var last_splash_img;

    $('#close_levelup_bubble').click(function(){
    	forest.occupied = false;

    	$("#cover_pane").fadeOut(200);
    	$("#forest_levelup_bubble").stop().fadeOut(400);

    	return false;
    });

	$("#go_hunting").live('click', function(){
		if(loading == false && forest.hunting_permission){

			forest.occupied = false;

			$("#cover_pane").fadeOut(200);
			$("#forest_levelup_bubble").stop().fadeOut(400);

			$("#bug_image").attr('src', '/images/insects/unset.gif');
            $("#bug_contents").stop().animate({ opacity:1 }, 100);
			$("#success_indicator").fadeOut(100);

			$(this).text('Hunting...').stop().animate({opacity:0.5}, 250);
			forest.toggle_ajax(true);
			$.getJSON("/forest/hunt/"+Math.random(), function(json){
				forest.toggle_ajax(false);

				if(typeof json.error == 'undefined'){
					$("#go_hunting").text('Go hunting!').animate({opacity:1}, 100);

					js_hunts = (js_hunts+1)
					if(js_hunts == 10) forest.reload_leaderboards();

					if(json['event'] == "bug"){
						forest.decrease_energy(1);
						$("#capture_title").text('You caught a...');
						$('#bug_data').show();
						$('#total_bug_value').text(json.value);
						$('#bug_rarity').text(json.rarity);
						$("#bug_image").attr('src', json.image);
						last_splash_img = json.image;
						$("#bug_name").html(json.name)
						$("#bug_description").html(json.description)
						$("#bug_contents a.proceed").show().text("Sell for "+json.value+" palladium");
						caught_bug = json.id;
						forest.add_exp(json.experience);
						$("#release").show().children('span').text('release bug');
						$("#bug_contents a.proceed").attr('id', 'quick_sell_bug');

						$("#caught_bugs li:last").fadeOut(250, function(){
							$(this).remove();
							$("#caught_bugs").prepend($('<li><a href="/forest/my_bugs/'+json.id+'" title="'+json.name+'"><img src="'+json.image+'" alt=""></a></li>'))
						})

						if(json.level_up == true){
							forest.occupied = true;

							$("#cover_pane").fadeIn(200);
							$("#forest_levelup_bubble").stop().fadeIn(400);
							$("#forest_levelup_bubble #new_level_amount").text(json.new_level);
							$("#forest_levelup_bubble #new_bug_possibilities").text(json.new_bugs);

							forest.add_energy(forest.clear_int('max_energy')-forest.clear_int('energy'));

							$("#exp_level").text(number_format(json.new_exp));
							$("#level").text(number_format(json.new_level));

							var new_exp = (forest.clear_int('exp_user'));
							var new_percent = (percent(new_exp, forest.clear_int('exp_level')));

							$("#exp_user").text(number_format(new_exp));
							$(".exp_bar .value").animate({width: (new_percent)+'%'}, 300);
							$(".add_up").show().css({ opacity: 0.8}).text("+"+exp).animate({marginTop:"-30px", opacity:0.6}, 400, function(){
								$(".add_up").animate({opacity:0}, 150, function(){
									$(".add_up").css({marginTop:"0px"}).hide();
								});
							});
						}
					} else if(json['event'] == "snap") {

						$("#go_hunting").attr("disabled", "disabled");

						$('#net_auxilliary .main_button').remove();
						$('#net_auxilliary').html('<p></p>');
						$('#net_auxilliary p').html(json.auxilary.message);
						$('#net_auxilliary').fadeIn(250);

						switch(json.auxilary.response){
							case 'shop_purchase':
								if (json.auxilary.options.length > 0) {
									var purchase_dropdown = '<select id="net_type" name="net_type">';
									var purchase_button = $('<button type="submit" id="quick_buy_net">Buy</button>');
									purchase_button.addClass('main_button');
									purchase_button.bind('click', function(){
										var self = $(this);
										forest.toggle_ajax(true);

										$.ajax({
										    type: "POST",
										    url: "/forest/quick_shop_purchase",
										    data: { shop_item_id: $('#net_type').val() },
										    cache: false,
										    async: true,
										    dataType: "json",
										    success: function(json){
										    	$('#net_auxilliary').fadeOut(250);
										    	forest.toggle_ajax(false);
										    	forest.success('You now have a net equipped, you\'re all set to keep hunting.');
										    	forest.hunting_permission = true;
										    	$("#forest_net .value_result").css({textDecoration:'none'}).text(json.tag);
										    	forest.remove_palladium(json.price);
										    	$("#go_hunting").removeAttr("disabled");
										    	$("#go_hunting").text('Go hunting!').animate({opacity:1}, 100);
										    },
										    error: function(xhr, status, error){
										        forest.error(error);
										    }
										});

										return false;
									});

									for (var i = 0; i < json.auxilary.options.length; i++) {
										purchase_dropdown += '<option value="'+json.auxilary.options[i]['shop_item_id']+'">'+json.auxilary.options[i]['label']+' for '+json.auxilary.options[i]['price'];
										purchase_dropdown += '</option>';
									};

									purchase_dropdown += '</select>'
									$('#net_auxilliary p').after(purchase_button);
									$('#net_auxilliary p').after(purchase_dropdown);
								}
							break;
							case 'already_owned':
								if(json.auxilary.options.length > 0){

									var equip_button = $('<a href="/avatar/equip/'+json.auxilary.options[0]['id']+'/ajax/">'+json.auxilary.options[0]['label']+'</a>');
									equip_button.addClass('main_button');
									equip_button.attr('tag', json.auxilary.options[0]['tag']);
									equip_button.bind('click', function(){
										var self = $(this);
										forest.toggle_ajax(true);
										$.getJSON(self.attr('href'), function(json) {
											$.getJSON('/avatar/save', function(json) {
												$('#net_auxilliary').fadeOut(250);
												forest.toggle_ajax(false);
												forest.success('You now have a net equipped!');
												forest.hunting_permission = true;
												$("#forest_net .value_result").css({textDecoration:'none'}).text(self.attr('tag'));
												$("#go_hunting").removeAttr("disabled");
												$("#go_hunting").text('Go hunting!').animate({opacity:1}, 100);
											});
										});
										return false;
									});

									$('#net_auxilliary p').after(equip_button);
								} else {

								}
							break;
						}

						forest.hunting_permission = false;
						console.log(json.auxilary);
						$("#bug_image").attr('src', json.image)
						last_splash_img = json.image;
						$("#bug_name").html(json.name)
						$("#bug_description").html(json.description)
						$("#capture_title").text('Oh-no!');
						$('#bug_data').hide();
						$("#forest_net .value_result").css({textDecoration:'line-through'}).text('none')
					} else {
						if(json['event'] == "berry"){
							var total_berries = forest.clear_int('forest_berries .value_result');
							$('#forest_berries .value_result').text(number_format(total_berries+1));
						}

						$("#go_hunting").text('Go hunting!').animate({opacity:1}, 100);

						forest.decrease_energy(1);
						$("#capture_title").text('You found a...');
						$('#bug_data').hide();
						$("#bug_image").attr('src', json.image)
						last_splash_img = json.image;
						$("#bug_name").html(json.name)
						$("#bug_description").html(json.description);
						$("#quick_sell_bug").hide();
						$("#release").hide();
					}
				} else {
					forest.error(json.error_msg);
					$("#bug_image").attr('src', last_splash_img);
				}
			});
		}

		return false;
	});

	$('#start_hunting').bind('click', function(){
		$.getJSON("/forest/prehunt_checks", function(json) {
			if(typeof json.success !== 'undefined'){
				var showcase = $('#bug_showcase');
				showcase.removeClass('center_shine')
				showcase.find('div.hunt_placeholder').hide();
				showcase.find('div.hunt_template').show();

				$("#go_hunting").trigger('click');

				var sidebar = $('#forest_sidebar');
				sidebar.find('div.before_hunting').hide();
				sidebar.find('div.after_hunting').show();
			} else {
				forest.error(json.error_msg);
			}
		});

		return false;
	});

	$("#berry_snack").click(function(){
		$.ajax({
		    type: "POST",
		    url: "/forest/snack",
		    dataType: "json",
		    success: function(json){
		    	if(typeof json.error === 'undefined'){
		    		forest.success(berry_munches[rand(0, berry_munches.length-1)]);
		    		var total_berries = forest.clear_int('forest_berries .value_result');
		    		$('#forest_berries .value_result').text(number_format(total_berries-1));
		    		forest.add_energy(10);
		    	} else {
		    		forest.error(json.error);
		    	}
		    },
		    error: function(xhr, status, error){
		        forest.error('Oops, the forest had some troubles doing what you asked of it. Could you send a developer this error: '+error);
		    }
		});
		return false;
	});

	$("#auto_fix_nets").click(function(){
		$.ajax({
		    type: "POST",
		    url: "/forest/fix_nets",
		    dataType: "json",
		    success: function(json){
		    	if(typeof json.error === 'undefined'){
		    		forest.success(json.message);
		    	} else {
		    		forest.error(json.message);
		    	}
		    },
		    error: function(xhr, status, error){
		        forest.error('Oops, the forest had some troubles doing what you asked of it. Could you send a developer this error: '+error);
		    }
		});
		return false;
	});


	$('#stop_hunting').click(function(){
		var showcase = $('#bug_showcase');
		showcase.addClass('center_shine')
		showcase.find('div.hunt_placeholder').show();
		showcase.find('div.hunt_template').hide();

		var sidebar = $('#forest_sidebar');
		sidebar.find('div.before_hunting').show();
		sidebar.find('div.after_hunting').hide();
		return false;
	});

	$("#snack").live('click', function(){
		if(berry_queue == false){
			$.getJSON("/forest/snack", function(json){
				if(json.error != undefined){
					forest.error(json.error);
				} else {
					$("#berries").text(json.berries);
					forest.add_energy(10);
					forest.success(berry_munches[rand(0, 9)])
				}
			});
		}
		return false;
	});

	$(".forest_alert_bubble, .forest_success_bubble").live('click', function(){
		$(this).fadeOut(200);
		$("#cover_pane").fadeOut(200);
		berry_queue = false;
		forest.occupied = false;
	});

	forest.reload_leaderboards();

	$('#fix_broken_nets').click(function(){
		$.getJSON('/forest/fix_nets', function(data) {
			if(data.type == "success") forest.success(data.message);
			if(data.type == "error") forest.error(data.message);
		});
		return false;
	});


});
