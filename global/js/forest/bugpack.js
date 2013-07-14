/*! Crysandrea - Javascript for forest/bugpack - Created at October 11, 2012*/
$(document).ready(function(){
	$("#sell_all").click(function(event) {
		event.preventDefault();
  		var answer = confirm("Are you sure you would like to empty your bugpack? There is no way to reverse this action.");
  		
  		if(answer == true)
  			 window.location = "/forest/sell_all_bugs"
  		else
  			location.reload();
	});
});