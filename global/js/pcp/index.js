/*! Crysandrea - Javascript for market/index - Created at August 28, 2012*/
$(document).ready(function(){
	var skel = '<hr><table>'+$("#skel").html()+'</table>';
	$(".imageAdded").blur(function(){
		if($(this).val() != ''){
			$(skel).appendTo("#grouped");
		}
		$(this).removeClass('imageAdded');
		$(".imageAdded").blur(function(){
			if($(this).val() != ''){
				$(skel).appendTo("#grouped");
			}
		});
	});
});