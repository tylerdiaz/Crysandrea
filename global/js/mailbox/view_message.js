/*! Crysandrea - Javascript for mailbox/view_message - Created at October 6, 2012*/
$(document).ready(function(){
	$("#message").keydown(function(e){
		if ((e.keyCode == 13 && e.shiftKey) || (e.keyCode == 83 && e.ctrlKey)){
			$("#send_post_message").submit();
			e.preventDefault();
		}
	});

});