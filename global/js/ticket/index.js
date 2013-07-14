/*! Crysandrea - Javascript for ticket/index - Created at February 3, 2013*/
$(document).ready(function(){
	var issue_select = $('select[name="issue"]');

	issue_select.on('change', function(){
		var show_url = ($('select[name="issue"]').find(":selected").attr('data-relevant-url') !== undefined);
		if (show_url) {
			$('#relevant_url').fadeIn(200);
		} else {
			$('#relevant_url').fadeOut(200);
		}

		console.log(show_url);
	})
});