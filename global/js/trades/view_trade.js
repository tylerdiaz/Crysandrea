$(document).ready(function(){

	var modal_objects = ['#loading_large_transaction', '#accept_confirmation'];

	for (var i = 0; i < modal_objects.length; i++) {
		$('body').append($(modal_objects[i]).clone());
		$('#content '+modal_objects[i]).remove();
	};

	var config = {
		avatar_tabs: "#avatar_items",
		cache: {
			trade_items: $('#'+user_role+'_container .tc_items')
		}
	}

	$(config.avatar_tabs+' a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});

	$(config.avatar_tabs+' a:first').tab('show');

	$('a[data-type="inventory"]').live('click', function(){
		var self = $(this);

    	var amount = 1;
    	if(self.attr('data-format') == 'bug'){
    		amount = prompt('How many bugs do you want to trade?');
    		if(amount > 40){
    			$('#loading_large_transaction').modal('show');
    		}
    	} else {
			config.cache.trade_items.find('div.empty_offer').hide();
			config.cache.trade_items.append(self);

			self.attr('data-type', 'trade_item');
			self.css({ backgroundColor: "#ffffff" });
			self.addClass('modify_item');
    	}

		$.ajax({
		    type: "POST",
		    url: "/trades/add_item/"+trade_id+"/"+self.attr('data-key'),
		    data: { type: self.attr('data-format'), amount: amount },
		    dataType: "json",
		    success: function(json){
		    	if(self.attr('data-format') == 'bug'){
		    		document.location.reload(true);
		    	}
		    },
		    error: function(xhr, status, error){
		    	self.removeClass('modify_item');
		    	self.attr('data-type', 'inventory');

		    	$('.tab-content #'+self.attr('data-tab')).append(self);
		    	$(config.avatar_tabs+' a[href="#'+self.attr('data-tab')+'"]').tab('show');
		    	var total_items = $('a.modify_item').length;
		    	if(total_items == 0) config.cache.trade_items.find('div.empty_offer').show();

		    	alert('An error occured when adding your item, please refresh the page.');
		    }
		});

		return false;
	});

	$('a.modify_item').live('click', function(){
		var self = $(this);
    	var amount = 1;
    	if(self.attr('data-format') == 'bug' && self.attr('data-amount') > 1){
    		amount = prompt('How many bugs do you want to trade?');
    		if(amount > 40){
    			$('#loading_large_transaction').modal('show');
    		}
    	} else {
    		if(self.attr('data-format') == 'bug'){
    			document.location.reload(true);
    		} else {
	    		self.removeClass('modify_item');
	    		self.attr('data-type', 'inventory');

	    		$('.tab-content #'+self.attr('data-tab')).append(self);
	    		$(config.avatar_tabs+' a[href="#'+self.attr('data-tab')+'"]').tab('show');
	    		var total_items = $('a.modify_item').length;
	    		if(total_items == 0) config.cache.trade_items.find('div.empty_offer').show();
    		}
    	}

		$.ajax({
		    type: "POST",
		    url: "/trades/remove_item/"+trade_id+"/"+self.attr('data-key'),
		    data: { type: self.attr('data-format'), amount: amount },
		    dataType: "json",
		    success: function(json){
		    	if(self.attr('data-format') == 'bug'){
		    		document.location.reload(true);
		    	}
		    },
		    error: function(xhr, status, error){
		    	if(self.attr('data-format') == 'bug'){
		    		document.location.reload(true);
		    	} else {
					config.cache.trade_items.find('div.empty_offer').hide();
					config.cache.trade_items.append(self);

					self.attr('data-type', 'trade_item');
					self.css({ backgroundColor: "#ffffff" });
					self.addClass('modify_item');

					alert('An error occured when adding your item, please refresh the page.');
		    	}
		    }
		});

		return false;
	});

	var animation_queue = false, selected_items = false;

	$('#search_inventory_query').typeahead({
		source: inventory_keys,
		items: 6,
		updater: function(item){
			if(selected_items){
				selected_items.css({ backgroundColor: "#ffffff" });
			}

			selected_items = $('#inventory_items a[data-key="'+inventory_match[item]+'"]');
			if(selected_items.length > 0){
				var tab_id = selected_items.parent().attr('id');
				$(config.avatar_tabs+' a[href="#'+tab_id+'"]').tab('show');
				selected_items.css({ backgroundColor: "#ffff00" });

				clearInterval(animation_queue);

				animation_queue = setTimeout(function(){
					selected_items.animate({ backgroundColor: "#ffffff" }, 2000, function(){
						selected_items.css({ backgroundColor: "#ffffff" });
					});
				}, 2000);
			}
		}
	});

	$('#search_inventory').on('submit', function(){
		var search_query = $('#search_inventory_query').val();
		return false;
	})

	$('#warn_pre_accept').on('click', function(){
		$('#accept_confirmation').modal('show');
	});

	$('#wait_for_offer').on('click', function(){
		$('#accept_confirmation').modal('hide');
	});

	// $('#add_currency').on('submit', function(){
	// 	var currency_type = $('#trade_currency').val();
	// 	var currency_amount = $('#total_currency_amount').val();
	// 	alert('Searching for '+search_query);
	// 	return false;
	// })
});