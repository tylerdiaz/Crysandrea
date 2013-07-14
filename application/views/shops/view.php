<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'shops')); ?>

<style type="text/css">
	.item_thumbnail {
		display:block;
		width:56px;
		float:left;
		border:1px solid #0a5cc7;
		text-align:center;
		margin:3px 4px;
		border-radius:4px;
		padding:4px 3px;
		font-size:12px;
	}
	.item_thumbnail:hover {
		border-color:orange;
	}
</style>

<?php if ($this->input->get('purchase')): ?>
	<?php $this->load->view('partials/notices/success', array('header' => 'Your item has been purchased', 'data' => 'You should go to your inventory and try out your new item!')) ?>
<?php endif ?>


<div class="clearfix" style="margin:10px 0;">
	<?php foreach ($shop_items as $item): ?>
		<a href="<?php echo '/shops/view_item/'.$item['shop_item_id'] ?>" class="item_thumbnail">
			<img src="/images/items/<?php echo $item['thumb'] ?>" alt="">
			<img src="/images/icons/little_palladium.png" style="margin-top:-2px;" width="12" height="12" /> <?php echo number_format($item['price']) ?>
			<?php if ($item['second_price']): ?>
				<br /><img src="/images/icons/bog.png" style="margin-top:-2px;" width="12" height="12" /> <?php echo number_format($item['second_price']) ?>
			<?php endif ?>
		</a>
	<?php endforeach ?>
</div>
<hr />
<span><?php echo $this->pagination->create_links(); ?></span>