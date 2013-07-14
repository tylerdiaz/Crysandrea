<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js"></script>
<h1 id="manage_crys">Crysandrea Navigation Links</h1>
<style>
ul {
	padding:0px;
	margin: 0px;
}
#response {
	padding:10px;
	background-color:#9F9;
	border:2px solid #396;
	margin-bottom:20px;
}
#list li {
	margin: 0 0 3px;
	padding:8px;
	background-color:#333;
	color:#fff;
	list-style: none;
}
</style>
<script type="text/javascript">
$(document).ready(function(){ 	

function slideout(){
  setTimeout(function(){
  	$("#response").slideUp("slow", function () {
  });
    
}, 2000);}
	
    $("#response").hide();
	$(function() {
	$("#list ul").sortable({ opacity: 0.8, cursor: 'move', update: function() {
			
			var order = $(this).sortable("serialize") + '&update=update'; 
			$.post('update_navigation', order, function(theResponse){
				$("#response").html(theResponse);
				$("#response").slideDown('slow');
				slideout();
			}); 															 
		}								  
		});
	});

});	
</script>
<div id="container">
  <div id="list">

    <div id="response"> </div>
    <ul>
    	<?php foreach($navigation as $nav) : ?>
      	<li id="listids_<?=$nav['nav_id']?>"><?php echo $nav['nav_text']; ?> </li>
      	<?php endforeach; ?>
        <div class="clear"></div>
      </li>
    </ul>
  </div>
</div>
