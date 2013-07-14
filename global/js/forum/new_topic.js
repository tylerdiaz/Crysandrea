/*! Crysandrea - Javascript for forum/new_topic - Created at September 6, 2012*/
$(document).ready(function(){
	var topic_label = null;

	$('input[name="title"]').blur(function(){

		if($(this).val().length > 2 && topic_label == null){
			$("#title_label").animate({'opacity':0}, 300, function(){
				$(this).text('Your topic title is...').animate({'opacity': 1}, 300);
				topic_label = true;
			})
		}
	});
});