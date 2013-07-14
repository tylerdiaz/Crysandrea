/*! Crysandrea - Javascript for shops/sellback - Created at October 30, 2012*/
$(document).ready(function(){
	var palla_flash = false;
	var allowed_alert = true;

	function clear_int(obj){
		return parseInt($("#"+obj).text().split(',').join(''));
	};

	$('.sell_item_form').on('submit', function(){
		var form_obj = $(this);
		if (allowed_alert) {
			if ( ! confirm('Are you sure you want to sell this item?')) return false;
		}

		form_obj.find('button').attr('disabled', true);

		$.ajax({
		    type: "POST",
		    url: form_obj.attr('action')+"?json=1",
		    data: form_obj.serialize(),
		    dataType: "json",
		    success: function(json){
		    	form_obj.parent().parent().parent().fadeOut(500);

		    	var new_palla = clear_int('palladium_count')+parseInt(json.amount);
		    	$("#palladium_count").text(number_format(new_palla)).css({ backgroundColor: '#cbe3a3'}).stop().animate({ backgroundColor: '#ffff00', color: '#888800' }, 200);
		    	clearTimeout(palla_flash);
		    	palla_flash = setTimeout(function(){
		    		$("#palladium_count").animate({color: '#334c00', backgroundColor: '#cbe3a3'}, 1000, function(){
		    			$(this).css({ backgroundColor: 'transparent'});
		    		});
		    	}, 1000)
		    }
		});

		return false;
	});

	$('#confirmation_notice').change(function(){
		if ($(this).is(':checked')) {
			allowed_alert = false;
			console.log('Gone!');
		} else {
			allowed_alert = true;
		}
	})
});