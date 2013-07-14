/*! Crysandrea - Javascript for donate/index - Created at August 28, 2012*/
$(document).ready(function(){
	// var gem_model = $('#show_gems').clone();
	$('body').append($('#show_gems').clone());
	$('#content #show_gems').remove();

	$('#total_donation_amount').on('blur', function(){
		if($('#total_donation_amount').val().length == 0){
			$('#total_donation_amount').val('0');
		}
	});

	$('#total_donation_amount').on('keyup change', function(){
		var new_total = (Math.round(parseFloat($('#total_donation_amount').val())*100)/100);

		if(typeof new_total != 'Nan' && new_total > 0){
			$('#total_donation').text("$"+new_total);
			var currency = Math.round(new_total/0.075);
			var multiplier = 1;

			if (typeof bundles[new_total] !== 'undefined') {
				currency = bundles[new_total];
			} else {
				currency *= multiplier;
			}

			$('input[name=amount]').val(new_total);

			$('#total_currency').text(Math.floor(currency));
		} else {
		}

	});

	$('.thumbnail_toggle').on('click', function(){
		var id = parseInt($(this).attr('data-item-id'));
		var container = $(this).parent();
		container.find(".tyi_preview_avatar").attr('src', '/avatar/preview_item/'+id);
		console.log('/avatar/preview_item/'+id);
		return false;
	})
});