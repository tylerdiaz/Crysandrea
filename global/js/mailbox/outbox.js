/*! Crysandrea - Javascript for mailbox/outbox - Created at October 6, 2012*/
function checkUncheckAll(theElement) {
	var theForm = theElement.form, z = 0;
		for(z=0; z<theForm.length;z++){
		if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall'){
			theForm[z].checked = theElement.checked;
		}
	}
}

$(document).ready(function(){
	$("table.organized").delegate('.icon', 'click', function(){
		var read = $(this).parent().attr("class"),
			id = $(this).parent().attr('id'),
			ghost_image = $('<img src="/images/icons/newmail.png" style="visibility:hidden" />'),
			image = $('<img src="/images/icons/newmail.png" />');

		if(read == " read" || read == "alt read"){

			$.get('/mailbox/ajax/unread/'+id, function(data){
				if($("#sub-inbox span").length == ""){
					$("#sub-inbox").append('<span></span>');
					$("#sub-inbox span").html(data);
				} else {
					$("#sub-inbox span").html(data);
				};
			});

			$(this).parent().removeClass('read').addClass('unread')
			$(this).html(image);
		} else {
			$.get('/mailbox/ajax/read/'+id, function(data){
				if(data == 0){
					$("#sub-inbox").children('span').hide().remove();
				} else {
					$("#sub-inbox span").html(data);
				};
			});

			$(this).parent().removeClass('unread').addClass('read')
			$(this).html(ghost_image);
		}
	});
});