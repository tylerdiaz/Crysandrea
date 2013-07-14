$(document).ready(function(){

	$('#tab_canaster div').eq(0).show();
	$('#tab_canaster > div').not(':eq(0)').hide();

	$('#dashboard_tabs li a').on('click', function(){
		var link = $(this);
		$('#dashboard_tabs li.active').removeClass('active');
		link.parent().addClass('active');
		var place = $("#dashboard_tabs > li").index($("#dashboard_tabs > li.active"));
		$('#tab_canaster div').eq(place).show();
		$('#tab_canaster > div').not(':eq('+place+')').hide();

		return false;
	});

	var delay = 2000;
	var autoload_checkbox = $('#autoload_new_topics');
	function load_new_topics(amount){
		if (autoload_checkbox.is(':checked')) {
			$.ajax({
			    type: "GET",
			    url: "/home/load_recent_topics",
			    cache: false,
			    async: true,
			    dataType: "json",
			    success: function(json){
			    	$('#dashboard_active_top').html(json.html);
			    	setTimeout(load_new_topics, delay);
			    }
			});
		} else {
			setTimeout(load_new_topics, delay);
		}
	}


	load_new_topics(delay);

});