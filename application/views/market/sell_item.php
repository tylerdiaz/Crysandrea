<link rel="stylesheet" href="/global/styles/marketplace.css">
<script type="text/javascript">
	function number_format(f,c,h,e){f=(f+"").replace(/[^0-9+\-Ee.]/g,"");var b=!isFinite(+f)?0:+f,a=!isFinite(+c)?0:Math.abs(c),j=(typeof e==="undefined")?",":e,d=(typeof h==="undefined")?".":h,i="",g=function(o,m){var l=Math.pow(10,m);return""+Math.round(o*l)/l};i=(a?g(b,a):""+Math.round(b)).split(".");if(i[0].length>3){i[0]=i[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,j)}if((i[1]||"").length<a){i[1]=i[1]||"";i[1]+=new Array(a-i[1].length+1).join("0")}return i.join(d)};

	$(document).ready(function(){
		var item = {}, total_price = 0;

		$("ul.avatar_tabs").tabs("div#tab_glove > div");

		var click_lock = false;
		var current_stage = 1;
		$('.add_item').on('click', function(){
			if(click_lock === true || current_stage > 1){
				return false;
			} else {
				click_lock = true;
			}

			var item_obj = $(this);
			item.id = item_obj.attr('item_id');
			item.name = item_obj.children('img').attr('title');
			item.src = item_obj.children('img').attr('src');
			$('#select_item').removeClass('current').addClass('completed');
			$('#select_item .checkmark').css('display', 'inline-block');
			$('#set_data').removeClass('completed').addClass('current');
			$('.tabs').hide();
			$('.auction_data').show();

			$('#preview_thumbnail').attr('src', item.src);
			$('.item_name').text(item.name);

			$.ajax({
			    type: "POST",
			    url: "/marketplace/get_price_suggestion",
			    data: { id: item.id },
			    dataType: "json",
			    success: function(json){
		    		var append_html = "";
		    		var average_low_price = [];
		    		var total_lowest = 999999999;
			    	for (var i = json.length - 1; i >= 0; i--) {
			    		if(json[i]['price'] < total_lowest){
			    			total_lowest = json[i]['price'];
			    		}
			    		average_low_price.push(json[i]['price']);
			    		append_html += '<li><strong>'+number_format(json[i]['price'])+' <img src="/images/icons/little_palladium.png" alt=""></strong><br /><span>'+json[i]['published']+'</span></li>';
			    	};
			    	if(json.length > 0){
			    		$('#price_input').val(Math.round(total_lowest));
			    		$('#price_input').trigger('change');
			    		$('#lowest_unit_prices').html(append_html);
			    	}
			    },
			});

			click_lock = false;
			return false;
		});


		var fee_percent = 4.5;
		$('#price_input').on('change keyup', function(){
			total_price = $(this).val();
			$('#final_price').text(number_format(total_price));
			$('#total_fee').text(number_format(total_price*(fee_percent/100)));
			$('#total_recieved').text(number_format(total_price-(total_price*(fee_percent/100))));
			$('#price_value').val(total_price);
			$('#item_id_value').val(item.id);
		});

		$('#sell_item').on('submit', function(){
			if(total_price < 50){
				return confirm('You\'re selling this item at an incredibly cheap price, is that what you meant to do?');
			}
		})
	});
</script>
<div class="feature_header">
	<div class="feature_title">
		<h4>Step 1: Choose an item to auction</h4>
		<ul class="right feature_options">
			<li><a href="/marketplace?all_auctions=1" class="all_auctions">All auctions</a></li>
			<li><a href="/marketplace?your_auctions=1" class="your_auctions">Your auctions</a></li>
		</ul>
	</div>
	<div class="feature_action">
		<a href="/marketplace" class="return">&lsaquo; Return back to the marketplace</a>
		<!-- <input type="text" name="search_mp" class="search_input right" placeholder="Search for an item..." /> -->
	</div>
</div>
<div>
	<div class="tabs" style="margin:10px 5px;">
		<?php if(sizeof($inventory) > 0): ?>
			<ul class="avatar_tabs left">                    
				<?php foreach($inventory as $tab => $tab_name){ ?>
					<?php foreach($tab_name as $key => $item1){ ?>
						<li><a href="#<?=strtolower($key)?>"><?=$key?></a>
					<?php } ?>
				<?php } ?>
			</ul>
			<br clear="all" />
			<div id="tab_glove">
			<?php foreach($inventory as $tab => $item1): ?>
				<?php foreach($item1 as $order => $item2): ?>
					<div id="<?=strtolower($order)?>">
					<?php foreach($item2 as $key => $item3): ?>
						<?php foreach($item3 as $key => $item): ?>
						<a href="#" class="stack add_item" quanity="<?=$item['num']?>" item_id="<?=$item['raw_item_id']?>">
							<?=image('images/items/'.$item['thumb'], 'title="'.$item['name'].'" width="42" height="42"')?>
							<?php if($item['num'] > 1): ?>
								<span><?php echo $item['num'] ?></span>
							<?php endif; ?>
						</a>
						<?php endforeach; ?>
					<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			<?php endforeach; ?>
			</div>
		<?php else: ?>
			<strong>You have no items in your inventory.</strong>
		<?php endif; ?>
	</div>
	<style type="text/css">
		.total_price {
			border-top:1px solid #bbb;
			padding-top:5px;
			margin-top:10px;
		}
		#lowest_unit_prices {
			float:left;
			width:491px;
			overflow:hidden;
		}
		#lowest_unit_prices li {
			list-style:none;
			margin:2px 2px;
			float:left;
			background:#eee;
			min-width:100px;
			text-align:center;
			border:2px solid #e0e0e0;
			padding:5px;
			color:#555;
			border-radius:4px;
			-webkit-border-radius:4px;
			-moz-border-radius:4px;
		}
		#lowest_unit_prices li strong { font-size:15px; }
		#lowest_unit_prices li strong img { margin-top:-6px; }
	</style>
	<div class="auction_data" style="overflow:hidden; margin:10px 5px; display:none">
		<div style="float:left; width:270px; background:#ddd; margin-right:10px; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; border:1px solid #ccc; padding:10px; font-size:14px;">
			<h3 style="border-bottom:1px solid #aaa; padding:0 0 3px; margin:0 0 7px;">Price your auction</h3>
			<label for="price">Purchase Price</label><input type="text" id="price_input" value="0" style="float:right; width:90px; border:1px solid #aaa; font-size:14px; padding:1px 2px;" /><br />
			<label for="">Listing Fee (4.5%)</label><div style="float:right;"><span id="total_fee">0</span> Palladium</div>
			<div class="total_price">
				You will recieve <div class="right"><span id="total_recieved">0</span> Palladium</div>
			</div>
		</div>
		<div style="float:left; width:433px; ">
			<h4 style="border-bottom:1px solid #ccc; margin-bottom:10px; padding-bottom:3px;">Auction overview:</h4>
			
			<div>
				<img alt="" id="preview_thumbnail" class="left" style="margin-right:6px;" />
				<strong class="item_name"></strong><br />
				<span id="item_price">for <span id="final_price">0</span> palladium</span>
			</div>
			<br clear="all" />
			<div style="clear:both; border-top:1px solid #aaa; margin:5px 0 0; padding-top:5px;">
				<form action="/marketplace/sell_item" method="POST" id="sell_item">
					<input type="hidden" name="price" id="price_value" />
					<input type="hidden" name="item_id" id="item_id_value" />
					<div style="float:right;">All set? &nbsp;<button class="proceed">Publish auction &rsaquo;</button></div>
				</form>
			</div>
		</div>
		<br clear="all" />
		<div style="clear:both; border:2px solid #add97e; margin:10px 0; border-radius:6px; padding:7px 15px; overflow:hidden">
			<h3 style="float:left; width:190px; line-height:1.2; font-family:helvetica; color:#406127; text-align:center; margin:14px 10px 10px 0;">Lowest item prices:<br /><span style="color:#94bb60; font-size:12px; font-weight:normal;">for <strong class="item_name">a very very long item name</strong></span></h3>
			<ul id="lowest_unit_prices">
			</ul>
		</div>
	</div>
	<ul class="auction_steps group">
		<li class="current" id="select_item">
			<h4><img src="/images/goodtogo.png" alt="" width="12" height="12" class="checkmark">Step 1:</h4>
			<span>Choose an item to auction</span>
		</li>
		<li class="pending" id="set_data">
			<h4>Step 2:</h4>
			<span>Set the price and auction it!</span>
		</li>
	</ul>
</div>