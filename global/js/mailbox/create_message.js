/*! Crysandrea - Javascript for mailbox/create_message - Created at October 5, 2012*/
$(document).ready(function(){
	var friend_array = [];

	$('#to').focus(function(){
		if(friend_array.length == 0){
			$.getJSON('/friends/get_friends/', function(json){
				friend_array = json;
				$('#to').asuggest(friend_array, {
					'endingSymbols': ',',
					'minChunkSize': 2,
					'stopSuggestionKeys': [$.asuggestKeys.RETURN],
					'delimiters': ',',
					'cycleOnTab': false
				});
			});
		}
	});

	$('#to').blur(function(){
		var targets = $(this).val();
		if(targets[targets.length-1] == ','){
			targets = targets.slice(0, -1);
			$(this).val(targets);
		}
	});


	$("#message").keydown(function(e){
		if ((e.keyCode == 13 && e.shiftKey) || (e.keyCode == 83 && e.ctrlKey)){
			$("#create_message").submit();
			e.preventDefault();
		}
	});


});