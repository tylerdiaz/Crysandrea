/*! Crysandrea - Javascript for avatar/index - Created at August 28, 2012*/

var headlines = ["Lookin' good!","Awesome avatar!","Nice combination!","Stunning look!","Great look!","You look great!","Stunning outfit!","Looks great!","Wonderful look!","Outstanding outfit!"];
var activePop = null;
function closeInactivePop() {
	$('.multi_item').each(function(i)
	{
		if ($(this).hasClass('active') && i!=activePop)
		{
			$(this).removeClass('active');
		}
	});
  return false;
}

function microtime(get_as_float) {
	var now = new Date().getTime() / 1000;
	var s = parseInt(now, 10);
	return (get_as_float) ? now : (Math.round((now - s) * 1000) / 1000) + ' ' + s;
}

function avatar_assign_url(src,href,obj){

	$("#avatar_preview_img").attr('src', src);

	var toEquip = !obj.hasClass('equipped');
	var layer_id = obj.attr('layer');

	$("a[layer="+layer_id+"]").each(function()
	{
		var equiped_layer = !$(this).hasClass('equipped');
		$(this).removeClass('equipped');

		if($(this).attr('sub_item') == 'true' && !equiped_layer)
		{
			$(this).parent().parent().parent().children('.pull_down').removeClass('glowing');
		}
	});

	if(obj.attr('sub_item') == 'true')
	{
		var objectParent = obj.parent().parent().parent()
		obj.parent().parent().children('li').children('a').removeClass('equipped');
		if(!toEquip)
		{
			objectParent.children('.pull_down').removeClass('glowing');
		}
		else
		{
			objectParent.children('a').removeClass('equipped');
			objectParent.children('.pull_down').addClass('glowing');
		}
	}
	else if(obj.attr('parent') == 'true')
	{
		if(!toEquip)
		{
			obj.parent().children('.pull_down').removeClass('glowing');
			obj.parent().children('ul').children('li').children('a').removeClass('equipped');
		}
		else
		{
			obj.parent().children('.pull_down').removeClass('glowing');
			obj.parent().children('ul').children('li').children('a').removeClass('equipped');
		}
	}

	if(!toEquip)
	{
		obj.removeClass('equipped');
	}
	else
	{
		obj.addClass('equipped');
	}
}

$(document).ready(function(){
	var sucess_timeout;
	var headshot_src = $('.avatar_headshot').attr('src');

	$('.avatar_items a img').tooltip({placement: 'bottom'});

	$(".avatar_tabs").tabs("div#tab_glove > div", {
		history: true
	});

	$('#revert').click(function(){
		$.post('/avatar/revert',{  },function(data){
			$('#tab_glove').children().children().children().removeClass('equipped');
			 $.each(
				data.split(';'),
			 function( intIndex, objValue ){
				$(objValue).addClass('equipped');
		});
		$("#avatar_loading").css('display','block');
			var img     = new Image();
			var src     = "/avatar/preview/"+microtime(true);
			img.onLoad  = $("#avatar_preview_img").attr('src', src);
			img.src     = src;
		});
		return false;
	});

	$('#swap_gender').on('click', function(){
		$.ajax({
			type: "POST",
			url: "/avatar/swap_gender",
			dataType: "json",
			success: function(json){
				var img    = new Image();
				var src    = "/avatar/preview/"+microtime(true);
				img.onLoad = $("#avatar_preview_img").attr('src', src);
				img.src    = src;
			},
		});

		return false;
	});

	$('#unequip_all_button').on('click', function(){
		$.post('/avatar/unequip', function(data){
			$('#tops').children().children().removeClass('equipped');
			$('#bottom').children().children().removeClass('equipped');
			$('#head').children().children().removeClass('equipped');
			$('#feet').children().children().removeClass('equipped');
			$('#accessories').children().children().removeClass('equipped');
			$('#items').children().children().removeClass('equipped');
			$("#avatar_loading").css('display','block');

			var img     = new Image();
			var src     = "/avatar/preview/"+microtime(true);
			img.onLoad  = $("#avatar_preview_img").attr('src', src);
			img.src     = src;
		});
		return false;
	});

	$(".multi_item .pull_down").live('click', function(){
		$(this).parent().toggleClass('active');
		return false;
	});

	$('.multi_item').mouseover(function() { activePop = $('.multi_item').index(this); });
	$('.multi_item').mouseout(function() { activePop = null; });

	$(document.body).click(function(){
		closeInactivePop();
	});

	$("a[href*='avatar/equip']").click(function(e){
		e.preventDefault();
		$("#avatar_loading").css('display','block');
		var url = $(this).attr('href');
		var obj = $(this);
		$.get(url+'/ajax/'+microtime(true),{},function(){
			var img		= new Image();
			var src		= "/avatar/preview/"+microtime(true);
			img.onLoad	= avatar_assign_url(src,url,obj);
			img.src		= src;
		});
	});

	$("a[href*='avatar/save']").click(function(e){
		e.preventDefault();
		$("#success_container").stop().hide().fadeIn(400);
		clearTimeout(sucess_timeout);
		var active_headline = headlines[Math.floor(Math.random()*headlines.length)];
		$("#success_container h3").html('<img src="/images/icons/yay.png" alt="" /> '+active_headline);
		$.get($(this).attr('href')+'/ajax/'+microtime(true),{},function(){
			d = new Date();
			$('#my_avatar_headshot').attr('src', headshot_src+'?'+d.getTime());

			sucess_timeout = setTimeout(function(){
				$("#success_container").stop().slideUp(1000);
			}, 3500);
		});
	});

	$("a[href*='account/unequip']").click(function(e){
		e.preventDefault();
		$.get($(this).attr('href')+'/ajax/'+microtime(true),{},function(){
			$("#avatar_preview_img").attr("src", "/avatar/preview/"+microtime(true));
			$("span a").removeClass('equipped');
		});
	});
});