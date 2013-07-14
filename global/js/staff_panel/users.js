/*! Crysandrea - Javascript for staff_panel/users - Created at February 3, 2013*/
$(document).ready(function(){
	var cached_items = {};
	var selected_items = {};

	$('#item_name').typeahead({
		source: function(query, process){
			console.log(query);
			$.ajax({
			    type: "POST",
			    url: "/staff_panel/search_item/",
			    data: { q: query },
			    dataType: "json",
			    success: function(json){
			    	if (json.item_array.length > 0) {
			    		process(json.items);
			    		$.extend(cached_items, json.item_obj);
			    	}
			    },
			});
		},
		updater: function(source){
			var item_data = cached_items[source];
			if (typeof selected_items[item_data['item_id']] !== 'undefined') {
				selected_items[item_data['item_id']]['amount']++;
			} else {
				item_data['amount'] = 1;
				selected_items[item_data['item_id']] = item_data;
			}

			update_selected_items(selected_items);
		}
	});

	$('#item_amount').live('blur', function(){
		var item_id = $(this).parent().parent().attr('data-item-id');
		selected_items[item_id]['amount'] = parseInt($(this).val());

		update_selected_items(selected_items);
	});

	$('#refund_items').on('submit', function(){
		$.ajax({
		    type: "POST",
		    url: "/staff_panel/refund_items",
		    data: { items: selected_items, user_id: $('#user_id').val() },
		    dataType: "json",
		    success: function(json){
		        document.location = '/staff_panel/users?success=1';
		    }
		});
		return false;
	});

	function update_selected_items(items){
		$('#selected_items .media:not(#skeleton_template)').remove();
		var keys = Object.keys(items), key;

		while( key = keys.pop() ) {
			var new_item_list = $('#skeleton_template').clone();
			new_item_list.removeClass('hide');
			new_item_list.attr('id', '');
			new_item_list.attr('data-item-id', items[key]['item_id']);
			new_item_list.find('#item_thumbnail').attr('src', '/images/items/'+items[key]['thumb']);
			new_item_list.find('#item_name').text(items[key]['name']);
			new_item_list.find('#item_amount').val(items[key]['amount']);

			$('#selected_items').append(new_item_list);
		}
	}

});