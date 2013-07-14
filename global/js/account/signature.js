/*! Crysandrea - Javascript for account/signature - Created at August 26, 2012*/
$(document).ready(function(){
	$('#forum_signature').bind('change keyup keydown', function(){
		$(this).val($(this).val().substring(0,255));
		$('#chars_left').css({color: 'black'}).text(255-parseInt($(this).val().length));
		if(255-parseInt($(this).val().length) == 0){
			$('#chars_left').css({color: 'red'})
		}
	});
});