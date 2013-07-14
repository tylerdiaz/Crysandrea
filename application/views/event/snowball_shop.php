<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'snowball')); ?>
<br />
<?php foreach ($items as $item): ?>
	<!-- Array ( [0] => Array ( [item_id] => 24918 [name] => Quilted winter sweater [gender] => Unisex [thumb] => db7fd8a1f6cfc3c07662c475c65469b5.png [order] => 0 [layer] => 56 [default] => 0 [composite] => 0 [type] => [price] => 2000 )  -->
	<div class="well">
		<div class="media">
		<img src="/images/items/<?php echo $item['thumb'] ?>" class="pull-left media-object">
		  <div class="media-body">
			<strong class="media-heading"><?php echo $item['name'] ?></strong><br>
			<p>Price: <span style="<?php echo ( ! $item['can_afford'] ? 'color:red; font-weight:bold;' : '') ?>"><?php echo $item['price'] ?> <?php echo $currency_name ?>s</span></p>
			<?php if ($item['can_afford']): ?>
				<form action="/snowball/purchase_item" method="POST">
					<input type="hidden" name="item_id" value="<?php echo $item['item_id'] ?>" />
					<button class="main_button">Purchase item</button>
				</form>
			<?php else: ?>
				You don't have enough <?php echo $currency_name ?>s to afford this item
			<?php endif ?>
		  </div>
		</div>
	</div>
<?php endforeach ?>
