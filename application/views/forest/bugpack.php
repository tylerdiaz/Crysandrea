<?php $this->load->view('forest/forest_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<style type="text/css">
	.bug_canaster {
		float:left;
		width:347px;
		background:white;
		height:150px;
		margin:5px;
		border-radius:5px;
		overflow:hidden;
		position:relative;
		padding:3px;
	}
	.bug_canaster .bug_img {
		border-radius:6px;
		float:left;
		margin-right:4px;
	}
	.bug_canaster h3 {
		font-size:16px;
		margin:8px 0 0;
		line-height:1.6
	}
	.bug_canaster select {
		width:auto;
		height:auto;
		margin:0;
		padding:0;
	}
	.price {
		position:absolute;
		left:4px;
		bottom:4px;
		display:block;
		padding:2px 4px;
		font-size:12px;
		background:rgba(0, 0, 0, 0.8);
		border-radius:4px;
		color:#fff;
	}
	.rarity {
		position:absolute;
		right:4px;
		bottom:4px;
		display:block;
		padding:4px 6px;
		font-size:12px;
		background:rgba(0, 0, 0, 0.8);
		border-radius:4px;
		color:white;
		opacity:0.3
	}
	.amount {
		position:absolute;
		right:4px;
		top:4px;
		display:block;
		padding:4px 6px;
		font-size:12px;
		background:#FF7A4F;
		color:white;
		border-radius:4px;
	}
	.bug_canaster:hover .rarity {
		opacity:1;
	}

	#sell_all{
		margin:5px 0 0 5px;
	}
</style>

<?php if(!empty($insects)): ?>
		<a href="/forest/sell_all_bugs" class="btn" id="sell_all">Sell all</a>
<?php endif; ?>
<div class="clearfix" style="margin:5px 0;">
	<?php foreach ($insects as $insect_id => $insect): ?>
		<div class="bug_canaster">
			<img src="<?php echo $insect['img'] ?>" alt="" height="150" width="150" class="bug_img" />
			<h3><?php echo $insect['name'] ?></h3>
			<p><?php echo $insect['description'] ?></p>
			<form action="/forest/sell_bug" class="sell_bug" method="POST">
				<input type="hidden" name="bug_id" value="<?php echo $insect_id ?>" />
				<?php echo form_dropdown('amount', $insect['dropdown']) ?>
				<button type="submit">Sell</button>
			</form>
			<span class="price"><img src="/images/icons/little_palladium.png" alt="" width="13" height="13" style="margin-top:-2px;"> <?php echo $insect['price'] ?></span>
			<span class="rarity"><?php echo $insect['rarity'] ?></span>
			<span class="amount">x<?php echo $insect['amount'] ?></span>
		</div>
	<?php endforeach ?>
</div>