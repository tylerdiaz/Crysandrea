<!-- overflow: hidden; -->
<div class="search_item">
	<!-- float: left; -->
	<div class="thumbnail">
		<img src="/images/items/<?= $thumb ?>" />
	</div>

	<!-- float: right -->
	<div class="item_info">
		<h4><?= $name ?></h4>
		<?php if (!empty($marketplace_item_id)): ?>
		<span>Available in <a href="/market/<?= $marketplace_item_id ?>">marketplace</a></span>
		<?php endif; ?>
		<?php if (!empty($shop_id)): ?>
		<span>Buy from <a href="/shops/<?= $shop_id ?>">shops</a></span>
		<?php endif; ?>
	</div>
</div>
