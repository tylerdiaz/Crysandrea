<link rel="stylesheet" href="/global/styles/marketplace.css?2.0">
<style type="text/css">
	.special_star {
		margin-top:-3px;
	}
	.preview_box_holder {
		position:absolute;
		left:0;
		top:0;
		width:180px;
		height:270px;
		display:none;
	}
	.preview_arrow_box {
		position: relative;
		background: #ffffff;
		border: 2px solid #c2e1f5;
		border-radius:5px;
		-moz-border-radius:5px;
		-webkit-border-radius:5px;
		width:180px;
		height:270px;
	}
	.preview_arrow_box:after, .preview_arrow_box:before {
		right: 100%;
		border: solid transparent;
		content: " ";
		height: 0;
		width: 0;
		position: absolute;
		pointer-events: none;
	}

	.preview_arrow_box:after {
		border-right-color: #ffffff;
		border-width: 7px;
		top: 50%;
		margin-top: -7px;
	}
	.preview_arrow_box:before {
		border-right-color: #c2e1f5;
		border-width: 10px;
		top: 50%;
		margin-top: -10px;
	}
	.new_items_added {
		background:#dfe9c6;
		color:#2d4908;
		padding:5px;
		text-align:center;
		font-size:12px;
		margin:3px 0;
		border-radius:3px;
		-moz-border-radius:3px;
		-webkit-border-radius:3px;
		display:none;
	}
/*    .arrow_box {

    	background: #303030;
    	border: 3px solid #4a4a4a;
    	padding: 10px;
    	background-color: #363636;
        background-color: #434343;
        background-image: -webkit-gradient(linear, left top, left bottom, from(rgb(67, 67, 67)), to(rgb(34, 34, 34)));
        background-image: -webkit-linear-gradient(top, rgb(67, 67, 67), rgb(34, 34, 34));
        background-image: -moz-linear-gradient(top, rgb(67, 67, 67), rgb(34, 34, 34));
        background-image: -o-linear-gradient(top, rgb(67, 67, 67), rgb(34, 34, 34));
        background-image: -ms-linear-gradient(top, rgb(67, 67, 67), rgb(34, 34, 34));
        background-image: linear-gradient(top, rgb(67, 67, 67), rgb(34, 34, 34));
        filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0,StartColorStr='#434343', EndColorStr='#222222');
        border-radius: 4px;
        box-shadow: 0 0 3px #4A4A4A;
    }
    .arrow_box:after, .arrow_box:before {
    	bottom: 100%;
    	border: solid transparent;
    	content: " ";
    	height: 0;
    	width: 0;
    	position: absolute;
    	pointer-events: none;
    }

    .arrow_box:after {
    	border-bottom-color: #434343;
    	border-width: 10px;
    	left: 50%;
    	margin-left: -10px;
    }
    .arrow_box:before {
    	border-bottom-color: #4d4d4d;
    	border-width: 14px;
    	left: 50%;
    	margin-left: -14px;
    }
    .arrow_box_cover a{
        color: white;
    }
    .arrow_box_cover a:hover{
        color: #aaaaaa;
    }
*/
.arrow_box_cover{
    margin-left: 6px;
    margin-top: 69px;
    padding: 10px;
    position: absolute;
    width: 231px;
    z-index: 3000;
    display: none;
}

.arrow_box{
	position: relative;
	width: 180px;
	border: 1px solid #bfc3d1;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 0px 0px 5px 5px;
	-moz-background-clip: padding;
	-webkit-background-clip: padding-box;
	background-clip: padding-box;
	background-color: #fff;
	-moz-box-shadow: 0 2px 0 rgba(192,195,210,.6);
	-webkit-box-shadow: 0 2px 0 rgba(192,195,210,.6);
	box-shadow: 0 2px 0 rgba(192,195,210,.6);
}
.arrow_box a {
	display: block;
	height: 30px;
    padding-top: 10px;
    text-indent: 20px;
	border-top: 1px solid #E2E2E2;
	border-bottom: 1px solid #dedede;
	background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDI0MCA1MCIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGxpbmVhckdyYWRpZW50IGlkPSJoYXQwIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjUwJSIgeTE9IjEwMCUiIHgyPSI1MCUiIHkyPSItMS40MjEwODU0NzE1MjAyZS0xNCUiPgo8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjZWZmMGY1IiBzdG9wLW9wYWNpdHk9IjEiLz4KPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjZmZmIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgPC9saW5lYXJHcmFkaWVudD4KCjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIyNDAiIGhlaWdodD0iNTAiIGZpbGw9InVybCgjaGF0MCkiIC8+Cjwvc3ZnPg==);
	background-image: -moz-linear-gradient(90deg, #eff0f5 0%, #fff 100%);
	background-image: -o-linear-gradient(90deg, #eff0f5 0%, #fff 100%);
	background-image: -webkit-linear-gradient(90deg, #eff0f5 0%, #fff 100%);
	background-image: linear-gradient(90deg, #eff0f5 0%, #fff 100%);
	color: #808393;
	text-shadow: 0 1px 0 #fff;

}

.arrow_box a:hover{
	border-top: 1px solid #1e6cb6;
	border-bottom: 1px solid #1e6cb6;
	-moz-box-shadow: inset 0 1px 0 rgba(255,255,255,.2);
	-webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.2);
	box-shadow: inset 0 1px 0 rgba(255,255,255,.2);
	background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDI0NiA1NiIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+PGxpbmVhckdyYWRpZW50IGlkPSJoYXQwIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjUwJSIgeTE9IjEwMCUiIHgyPSI1MCUiIHkyPSItMS40MjEwODU0NzE1MjAyZS0xNCUiPgo8c3RvcCBvZmZzZXQ9IjAlIiBzdG9wLWNvbG9yPSIjMzE4YmRkIiBzdG9wLW9wYWNpdHk9IjEiLz4KPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNWFhYWY1IiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgPC9saW5lYXJHcmFkaWVudD4KCjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIyNDYiIGhlaWdodD0iNTYiIGZpbGw9InVybCgjaGF0MCkiIC8+Cjwvc3ZnPg==);
	background-image: -moz-linear-gradient(90deg, #318bdd 0%, #5aaaf5 100%);
	background-image: -o-linear-gradient(90deg, #318bdd 0%, #5aaaf5 100%);
	background-image: -webkit-linear-gradient(90deg, #318bdd 0%, #5aaaf5 100%);
	background-image: linear-gradient(90deg, #318bdd 0%, #5aaaf5 100%);
	color: #fff;
	text-shadow: 0 -1px 0 #1f6db5;
	text-decoration: none;
}

</style>
<script type="text/javascript" src="/global/scripts/handlebars.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

		var last_published = "<?php echo (isset($items[0]['published_at']) ? $items[0]['published_at'] : date('Y-m-d H:i:s', time())) ?>";

		var live_marketplace = {
			pagination_limit: <?php echo $pagination_configuration['per_page'] ?>,
			unloaded_items: 0,
			stored_items: [],
			interval: 2500,
			empty_attempts: 0,
			initiate: function(){
				setTimeout($.proxy(this.run, this), this.interval);
			},
			run: function(){
				var self = this;

				if(search.searching){
					self.initiate();
					return false;
				}

				$.ajax({
					type: "POSt",
					url: "/marketplace/poll_updates",
					data: { timestamp: last_published },
					cache: false,
					async: true,
					dataType: "json",
					success: function(json){
						if(json.length <= 0){
							self.delay();
						} else {
							$('.new_items_added').slideDown(500);
							last_published = json.timestamp;
							for (var i = 0; i < json.items.length; i++) {
								self.stored_items.push(json.items[i]);
							};
							self.unloaded_items += json.items.length;
							$('#total_new_items').text(self.unloaded_items);
							self.initiate();
							if(self.interval > 5000){
								self.interval = 3500;
							}
						}
					},
					error: $.proxy(self.delay, self)
				});
			},
			delay: function(){
				if( ++this.empty_attempts < 20 ){
					this.interval += 500;
					this.initiate();
				}
			}
		};

		live_marketplace.initiate();

		var reset_poll = setInterval(function(){
			live_marketplace.initiate();
			live_marketplace.interval = 3500;
			live_marketplace.empty_attempts = 0;
		}, 120000);

		var search = {
			searching: false,
			original_html: "",
			thread: false,
			obj_search: false,
			query: function(search_query){
				clearInterval(this.thread);
				if(search_query.length < 2){
					if(search.original_html) {
						search.searching = false;
						$('#marketplace_container').show();
						$('#marketplace_container .paginate').show();
						$('#empty_search').hide();
						$("#marketplace_container").html(search.original_html)
					}
					return false;
				}
				$.getJSON('/marketplace/search/', { q: search_query }, function(json){
					$('#marketplace_container .paginate').hide();
					if (typeof json == 'object' && json.length > 0) {
						search.searching = true;
						$('#marketplace_container').fadeIn(200);
						$('#empty_search').hide();
						search.parse_search(json);
					} else {
						search.searching = true;
						$('#empty_search').fadeIn(200);
						$('#marketplace_container').hide();
					}
				})
			},
			parse_search: function(json){
				var source = $("#market_search_response").html();
				var template = Handlebars.compile(source);
				$(".marketplace_table").html(template({items: json}));
			}
		};

		$('#load_new_items').on('click', function(){
			// load new items!
			$('.new_items_added').hide();
			var source = $("#market_search_response").html();
			var template = Handlebars.compile(source);
			if(($(".marketplace_table tr").length+live_marketplace.stored_items.length) > live_marketplace.pagination_limit){
				$('.marketplace_table tr').slice(-((live_marketplace.stored_items.length+18)-18)).remove();
			}
			$(".marketplace_table").prepend(template({items: live_marketplace.stored_items}));
			live_marketplace.unloaded_items = 0;

			return false;
		});

		$filter_bubble_showing = false; //used to keep track of filter pointers
		$forced_hide = false;
		$filter_bubble = $('.arrow_box_cover');
		$('.search_input').on('keyup', function(e){
		var word = $(this).val();
		if(!word.match('(by:)+([a-zA-Z]){0,3}')){
			hide_search_terms();
			$forced_hide = false;
			$filter_bubble_showing = false
		}
		if($filter_bubble_showing === true && $forced_hide != true){
			if(e.keyCode === 32 || e.keyCode === 27 ){
				hide_search_terms();
				$forced_hide = true;
				$filter_bubble_showing = false;
			}
		}else{
			var word = $(this).val();
			if(word.match('(by:)+([a-zA-Z]){0,3}')){
				//is a match
				if($forced_hide === false){
					$filter_bubble.fadeIn();
					$filter_bubble_showing = true;
					//nothing at the moment, sub-filtering to come
				}else{
					$('body').bind('click', function(){
						hide_search_terms();
						$('body').unbind('click');
						$filter_bubble_showing = false;
						$forced_hide = true;
					});
				}
			}else{
				$forced_hide = false;
				hide_search_terms();
			}
		}
		});

		//hide_search_terms($forced_hide = false);
		function hide_search_terms(){
			$filter_bubble.fadeOut();
		}

		// grab whole area
		// remove unwanted chunk
		// insert wanted chunk
		$('.arrow_box .filter_option').click(function(){
			var text = $('.search_input').val();
			var newtext = text.replace(/(by:)+([a-zA-Z]){0,15}/, $(this).html());
			$('.search_input').val(newtext);
			hide_search_terms();
			$filter_bubble_showing = false;
			$forced_hide = true;
		})

		$('.search_input').on('keyup keydown', function(){
			if($(this).val().length > 2){
				clearInterval(search.thread);
				search.thread = setTimeout(function(){
					search.query($('.search_input').val());
				}, 250);
			} else {
				if(search.original_html) {
					search.searching = false;
					$("#marketplace_container").html(search.original_html)
				}
			}
		});

		search.original_html = $("#marketplace_container").html();

		$('.purchase_item').live('submit', function(){
			return confirm('Are you sure you would like to buy this item?')
		});

		var preview_box = $('.preview_box_holder');

		$(".preview_item").live({
			mouseenter: function(){
				var this_data = $(this);
				var position = this_data.offset();
				preview_box.find('img').attr('src', '/global/styles/images/elements/preview_loader.gif');
				preview_box.stop().css({top: position.top-(preview_box.height()/2)+(this_data.height()/2), left: (position.left+this_data.width())+10 }).fadeTo(200, 1);
				var preview_img = new Image();
				preview_img.src	= '/preview/item/'+this_data.attr('item_id');
				preview_img.onload = function(event){
					preview_box.find('.preview_arrow_box').html($(preview_img));
				};
			},
			mouseleave: function(){
				$('.preview_box_holder').stop().fadeOut(200);
			}
		});
	});
</script>
<div class="arrow_box_cover"><div class="arrow_box"><a class="filter_option" href="#">by:price</a><a href="#" class="filter_option">by:time</a></div></div>
<div class="feature_header">
	<h2>Market auctions</h2>
	<ul class="feature_navigation">
		<li class="active"><a href="#">All auctions</a></li>
		<li><a href="#">Your auctions</a></li>
	</ul>
</div>
<div class="new_items_added"><strong id="total_new_items">0</strong> new items have been added since you refresh the page &bull; <a href="#" id="load_new_items">Load in the new items</a></div>
<div id="feature_content">
	<div id="marketplace_container">
		<table class="marketplace_table">
			<?php if (count($items) > 0): ?>
				<?php foreach ($items as $item): ?>
			 		<tr>
						<td class="action_item_data">
							<div class="thumbnail_placeholder">
								<img src="/images/items/<?php echo $item['item_thumbnail'] ?>" alt="" class="item_thumbnail" />
								<a href="#" class="preview_item" item_id="<?php echo $item['item_id'] ?>"></a>
							</div>
							<strong>
								<?php if ($item['item_type'] == 'tyi'): ?>
									<img src="/images/special_item.png" class="special_star magicTip" width="14" height="14" title="This is a Thank You item! These are rare items that can be difficult to find after they're gone" />
								<?php endif ?>
								<?php echo $item['item_name'] ?></strong><br />
							<span>by <a href="/user/<?php echo urlencode($item['username']) ?>"><?php echo $item['username'] ?></a></span>
						</td>
						<td style="<?php echo $this->system->userdata['user_palladium'] >= $item['price'] ? '' : 'color:#9b2c2d' ?>"><img src="/images/icons/little_palladium.png" alt="" width="13" height="13" style="margin-top:-5px"> <?php echo number_format($item['price']) ?> Palladium<!-- <br /><span class="highest_bid">Highest Bid: <?php echo number_format($item['price']) ?></span> --></td>
						<td>
							<?php if ($this->system->userdata['user_palladium'] >= $item['price'] && $this->system->userdata['user_id'] != $item['user_id']): ?>
								<form action="/marketplace/purchase_item" method="POST" class="purchase_item">
									<input type="hidden" name="page" value="<?php echo $this->uri->segment(3, 0) ?>" />
									<input type="hidden" name="auction_id" value="<?php echo $item['id'] ?>" />
									<button type="submit" class="buy_now_button">Buy now</a>
								</form>
							<?php else: ?>
								<?php if ($this->system->userdata['user_id'] == $item['user_id']): ?>
									<span title="You cannot purchase your own auctioned items" class="inactive_buy_now_button magicTip">Buy now</span>
								<?php else: ?>
									<span title="You do not enough palladium to purchase this item" class="inactive_buy_now_button magicTip">Buy now</span>
								<?php endif ?>
								<!-- <button type="submit" class="bid_button">Bid</button> -->
							<?php endif ?>
						</td>
						<td class="item_timestamp"><span><?php echo human_time($item['finishes_at'], TRUE) ?></span><br />Posted <?php echo human_time($item['published_at']) ?></td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<div style="border:2px solid #ddd; padding:20px; margin:10px; text-align:center; font-family:arial; font-size:17px; color:#555; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; line-height:80px;">
					There are no items listed in the marketplace at this moment
				</div>
			<?php endif ?>
		</table>
		<span><?php echo $this->pagination->create_links()?></span>
	</div>
	<div id="empty_search" style="display:none; border:2px solid #ddd; padding:20px; margin:10px; text-align:center; font-family:arial; font-size:17px; color:#555; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; line-height:80px;">
		No items were found with that search
	</div>
</div>
	<div class="preview_box_holder">
		<div class="preview_arrow_box">
			<img src="/avatar/preview/134" alt="">
		</div>
	</div>
<script id="market_search_response" type="text/x-handlebars-template">
	{{#items}}
	<tr>
		<td class="action_item_data">
			<div class="thumbnail_placeholder">
				<img src="/images/items/{{item_thumbnail}}" alt="" class="item_thumbnail" />
				<a href="#" class="preview_item" item_id="{{item_id}}"></a>
			</div>
			<strong>{{item_name}}</strong><br />
			<span>by <a href="/user/{{url_username}}">{{username}}</a></span>
		</td>
		<td><img src="/images/icons/little_palladium.png" alt="" width="13" height="13" style="margin-top:-5px"> {{price}} Palladium</td>
		<td>
			<form action="/marketplace/purchase_item" method="POST" class="purchase_item">
				<input type="hidden" name="auction_id" value="{{id}}" />
				<button type="submit" class="{{button_class}}">Buy now</a>
			</form>
		</td>
		<td class="item_timestamp"><span>{{time_left}}</span><br />Posted {{time_published}}</td>
	</tr>
	{{/items}}
</script>