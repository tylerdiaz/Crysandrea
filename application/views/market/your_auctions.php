<link rel="stylesheet" href="/global/styles/marketplace.css">
<div class="feature_header">
	<div class="feature_title">
		<h4>All marketplace auctions</h4>
		<ul class="right feature_options">
			<li><a href="/marketplace?all_auctions=1" class="all_auctions">All auctions</a></li>
			<li><a href="/marketplace?your_auctions=1" class="your_auctions">Your auctions</a></li>
		</ul>
	</div>
	<div class="feature_action">
		<!-- <a href="#">&lsaquo; Return back to the marketplace</a> -->
		<input type="text" name="search_mp" class="search_input left" placeholder="Search for an item..." />
		<a href="/marketplace/sell_item" class="button proceed right">+ Auction an item</a>
	</div>
</div>
<div>
	<table class="marketplace_table">
		<?php if (count($items) > 0): ?>
			<?php foreach ($items as $item): ?>
		 		<tr>
					<td class="action_item_data">
						<div class="thumbnail_placeholder">
							<img src="/images/items/<?php echo $item['item_thumbnail'] ?>" alt="" class="item_thumbnail" />
						</div>
						<strong><?php echo $item['item_name'] ?></strong><br />
						<span>by <a href="/user/<?php echo urlencode($item['username']) ?>"><?php echo $item['username'] ?></a></span>
					</td>
					<td><img src="/images/icons/little_palladium.png" alt="" width="13" height="13" style="margin-top:-5px"> <?php echo number_format($item['price']) ?> Palladium</td>
					<td>
						<form action="/marketplace/remove_item" method="POST">
							<input type="hidden" name="auction_id" value="<?php echo $item['id'] ?>" />
							<button type="submit" class="remove_button">Remove</a>
						</form>
					</td>
					<td class="item_timestamp"><span><?php echo human_time($item['finishes_at'], TRUE) ?></span><br />Posted <?php echo human_time($item['published_at']) ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<div style="border:2px solid #ddd; padding:20px; margin:10px; text-align:center; font-family:arial; font-size:17px; color:#555; border-radius:6px; -moz-border-radius:6px; -webkit-border-radius:6px; line-height:80px;">
				You have no auctions listed in the marketplace at this moment
			</div>
		<?php endif ?>
	</table>
</div>