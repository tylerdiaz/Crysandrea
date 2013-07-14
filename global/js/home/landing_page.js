/*! Crysandrea - Javascript for home/index - Created at August 21, 2012*/
$(document).ready(function(){
	$('input[placeholder], textarea[placeholder]').placeholder();
	$('#avatar_abbr').hover(function(){
		$('#avatar_explination').stop().fadeTo(250, 1);
	}, function(){
		$('#avatar_explination').stop().fadeTo(400, 0);
	});

	$('#signin_button').on('click', function () {
		$(this).button('loading').disable();
	})
});