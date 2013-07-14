/*! Crysandrea - Javascript for account/profile - Created at August 25, 2012*/
$(document).ready(function(){
	$('#profile_css').focus(function(){
		$(this).stop().animate({ height: '200px' }, 500);
	})
	$('#profile_css').blur(function(){
		$(this).stop().animate({ height: '60px' }, 500);
	})
});