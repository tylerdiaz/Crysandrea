/*! Crysandrea - Javascript for auth/avatar_signup - Created at February 17, 2013*/
$(document).ready(function(){

	// .default_equipped

	function reload_avatar(){
		d = new Date();
		$("#avatar_img").attr("src", "/auth/get_preview/?"+d.getTime());

		return false;
	}

	$('#save_and_continue').on('click', function(){

	});

	$('.equippable_default').on('click', function(){
		var self = $(this),
			item_id = parseInt(self.attr('data-item-id')),
			item_type = self.attr('data-item-type');

		self.parent().find('a.default_equipped').removeClass('default_equipped');
		self.addClass('default_equipped');

		$.ajax({
		    type: "POST",
		    url: "/auth/swap_equipment",
		    data: { item_id: item_id, item_type: item_type },
		    dataType: "json",
		    success: function(json){
		    	if (json.success) {
		    		reload_avatar();
		    	} else {

		    	}
		    },
		});

		return false;
	});

	$('.base_pallet').on('click', function(){
		var self = $(this),
			item_id = parseInt(self.attr('data-item-id')),
			item_type = self.attr('data-item-type');

		self.parent().find('a.default_skin').removeClass('default_skin');
		self.addClass('default_skin');

		$.ajax({
		    type: "POST",
		    url: "/auth/swap_equipment",
		    data: { item_id: item_id, item_type: item_type },
		    dataType: "json",
		    success: function(json){
		    	if (json.success) {
		    		reload_avatar();
		    	} else {

		    	}
		    },
		});

		return false;
	});

	jQuery.jQueryRandom = 0;
	jQuery.extend(jQuery.expr[":"], {
	    random: function(a, i, m, r) {
	        if (i == 0) {
	            jQuery.jQueryRandom = Math.floor(Math.random() * r.length);
	        };
	        return i == jQuery.jQueryRandom;
	    }
	});

	$('#gender_swap').on('change', function(){
		var self = $(this);

		$.ajax({
		    type: "POST",
		    url: "/auth/swap_equipment/1",
		    data: { value: self.val() },
		    dataType: "json",
		    success: function(json){
		    	if (json.success) {
		    		reload_avatar();
		    	} else {

		    	}
		    },
		});

	});

	$("#item_shuffle").on('click', function(){
		$('.choice_select').each(function(){
			var rand_select = rand(0, $(this).find('.equippable_default').length);
			$(this).find('.equippable_default').eq(rand_select).trigger('click');
		});

		return false;
	});
});