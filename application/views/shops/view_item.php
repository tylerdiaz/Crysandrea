<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'shops')); ?>

<div>
	<img src="/avatar/preview_item/<?php echo $item_data['item_id'] ?>" alt="" style="float:left;" />
	<div style="overflow:hidden; padding:10px 5px 5px;">
		<img src="/images/items/<?php echo $item_data['thumb'] ?>" alt="" style="float:left; margin-right:10px;" />
		<div style="overflow:hidden; float:left; width:300px;">
			<strong style="line-height:1.6; font-size:15px;"><?php echo $item_data['name'] ?></strong><br />
			Price: <strong><?php echo $item_data['price'] ?> palladium</strong>
			<?php if ($item_data['insect_id'] > 0): ?>
				+ <?php echo 'x'.$item_data['second_price'].' '.$item_data['insect_name'] ?>
			<?php endif ?>


		</div>
	</div>
	<?php if ($owns_item): ?>
		<small>You already own this item, would you like to purchase another?</small>
	<?php endif ?>
	<div style="margin:10px 0;">
		<form action="/shops/purchase_item/" method="POST">
			<input type="hidden" name="item_id" value="<?php echo $item_data['shop_item_id'] ?>" />
			<button type="submit" class="main_button">Purchase item</button>
		</form>
	</div>
	<?php if ($this->system->is_staff()): ?>
		<br />
		<h3 style="color:#999; font-weight:normal; font-size:13px;">Staff data</h3>
		<strong class="label label-info">Item_id:</strong> <?php echo $item_data['item_id'] ?>
	<?php endif ?>
	<?php if (count($children) > 0): ?>
		<br />
		<h3 style="color:#999; font-weight:normal; font-size:13px;">Also avaliable in...</h3>
		<div style="overflow:hidden">
			<?php foreach ($children as $item): ?>
				<a href="/shops/view_item/<?php echo $item['shop_item_id'] ?>"><img src="/images/items/<?php echo $item['thumb'] ?>" alt="" /></a>
			<?php endforeach ?>
		</div>
	<?php endif ?>
</div>