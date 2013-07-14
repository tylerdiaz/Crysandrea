<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'donate')); ?>
<?php // $this->load->view('partials/notices/notice', array('data' => 'The donation system works just fine. It\'s not at its prettiest, but it works just as stable as our old one. Some design elements are temporary.')) ?>

<style type="text/css">
	.tyi_preview_avatar {
		float:left;
	}
	.item_name {
		font-size:15px;
		color:#111;
		font-family:Arial
	}
	.donation_container {
		overflow:hidden;
		border-bottom:1px solid #ccc;
		margin-bottom:10px;
		margin-top:5px;
	}
	.donation_container:last-child {
		border:none;
	}
</style>
<div style="height:500px; overflow:hidden; position:relative;">
	<img src="/images/tyi_images/feb2013/bigart.jpg" alt="" style="margin-top:-10px;">
</div>
<?php if ($this->input->get('success')): ?>
	<?php $this->load->view('partials/notices/success', array('header' => 'Your item has been purchased!', 'data' => 'Your item has been gently placed inside your inventory, thank you for purchasing a donation item!')) ?>
<?php endif ?>
<div style="overflow:hidden">
	<div class="grid_2">
		<?php if($this->session->userdata('user_id')): ?>
		<h3 class="donate_small_header">You currently own <span><?php echo number_format($user['user_'.$this->currency_name]) ?></span> <?php echo $this->currency_name ?></h3>
		<?php endif ?>
		<a href="#show_gems" data-toggle="modal" id="gem_popup">See <?php echo ucfirst(substr($this->currency_name, 0, -1)) ?> prices</a>
	</div>
	<div class="grid_4" style="margin-top:10px">
		<?php foreach ($items as $item): ?>
		<div class="donation_container">
			<img src="/avatar/preview_item/<?php echo $item['item_id'] ?>" alt="" class="tyi_preview_avatar" />
			<strong class="item_name"><?php echo $item['name'] ?></strong><br />
			<p>Item description</p>
			<a href="#" data-item-id="<?php echo $item['item_id'] ?>" class="thumbnail_toggle">
				<img src="/images/items/<?php echo $item['thumb'] ?>" alt="" />
			</a>
			<?php foreach ($item['children'] as $children): ?>
			<a href="#" data-item-id="<?php echo $children['item_id'] ?>" class="thumbnail_toggle">
				<img src="/images/items/<?php echo $children['thumb'] ?>" alt="" />
			</a>
			<?php endforeach ?>
			<div style="border-top:1px solid #ddd; margin-top:10px; padding-top:5px; float:left; width:290px;">
				<form action="/donate/purchase_item" method="POST">
				<span>Price: <?php echo ($item['type'] == 'tyi' ? $prices['tyi'] : 15) ?> <?php echo ucfirst($this->currency_name) ?></span> &bull;
				Total:
				<select name="total" style="width:60px; height:auto; line-height:auto; padding:0; margin-bottom:0;">
					<option value="1">x1</option>
					<option value="2">x2</option>
					<option value="3">x3</option>
					<option value="5">x5</option>
					<option value="10">x10</option>
				</select>
				<br />
				<input type="hidden" name="item_id" value="<?php echo $item['item_id'] ?>" />
				<button class="main_button" type="submit">Purchase item</button>
				</form>
			</div>
		</div>
		<?php endforeach ?>
	</div>
</div>

<!-- Modal -->
<div id="show_gems" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="show_gems" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="myModalLabel" class="donate_small_header" style="margin-top:0">Crysandrea's <?php echo ucfirst($this->currency_name) ?></h3>
	</div>
	<div class="modal-body">
		<div style="float:left; width:190px; margin-right:10px; margin-left:10px;">
			<label style="font-size:12px; line-height:1.1" for="total_donation_amount">Total donation:</label>
			<input type="text" value="5" id="total_donation_amount" name="total_donation_amount" class="input-small" style="background:url(/images/icons/money.png)no-repeat 6px center; padding-left:20px;" />
		</div>
		<div style="float:left; width:320px;">
			<h5 style="line-height:1.4; margin:0">Bundle bonuses:</h5>
			<ul>
				<li><strong>$2.50</strong> = 35 <?php echo ucfirst($this->currency_name) ?></li>
				<li><strong>$5</strong> = 75 <?php echo ucfirst($this->currency_name) ?></li>
				<li><strong>$10</strong> = 160 <?php echo ucfirst($this->currency_name) ?></li>
				<li><strong>$25</strong> = 415 <?php echo ucfirst($this->currency_name) ?></li>
				<li><strong>$50</strong> = 865 <?php echo ucfirst($this->currency_name) ?></li>
			</ul>
		</div>
	</div>
	<div class="modal-footer">
		<span style="font-size:12px; color:#555; margin-right:10px;">You will receive <span id="total_currency">75</span> <?php echo ucfirst($this->currency_name) ?></span>
		<?php echo $paypal_form?>
	</div>
</div>

<script type="text/javascript">
	var discounts = <?php echo json_encode($discounts) ?>, bundles = <?php echo json_encode($bundles) ?>;
</script>
<style type="text/css">
	h3.donate_small_header {
		font-size:14px;
		color:#444;
		text-align:center;
		margin-top:25px;
	}
	#gem_popup {
		background:#7FAF1B;
		display:inline-block;
		color:white;
		padding:7px 20px;
		margin:5px 38px 20px;
		border-radius:4px;
		font-size:15px;
	}
	#gem_popup:hover {
		background:#90c12a;
		text-decoration:none;
	}
	#gem_popup:active {
		color:#ddd;
		background:#5f8d13;
		text-decoration:none;
		box-shadow:inset 0 2px 2px rgba(0, 0, 0, 0.1);
	}
</style>