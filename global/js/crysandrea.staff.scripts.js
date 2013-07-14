//FORUM MULTIPLE MOVING/EDITING JAVASCRIPT
$(function() {
	$all_selected = false;
	$('#select_all_topics').click(function(){
		if($all_selected === true){
			$all_selected = false;
			$('.multi_select').prop('checked', false);
		}else{
			$all_selected = true;
			$('.multi_select').prop('checked', true);
		}
		
		return true;
	});
	
	$forum_id = $('#forum_id');
	$forum_id.change(function(){
		$forum = $forum_id.val();
		switch ($forum)
		{
			case 'none': 
				return;
			break;
			default: 
				var answer = confirm('Are you sure you?');
				if (answer){
					submit_form();
				}
				else{
					forms_reset();
				}
			break;
		}		
	});
	
	$('#do').change(function(){
		$action = $('#do').val();
		switch ($action)
		{
			case 'move_to': 
				$forum_id.show();
			break;
			case 'none':
				$forum_id.hide();
				return;
			break;
			default: 
				$forum_id.hide();
				var answer = confirm('Are you sure?');
				if (answer){
					submit_form();
				}
				else{
					forms_reset();
				}
			break;
		}
	});
	
	function forms_reset(){
		$('#do').val('none');
		$forum_id.val('none').hide();
	}
	
	function submit_form(){
		$('#group_change').submit();
	}

});
