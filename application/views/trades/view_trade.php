<?php $this->load->view('trades/trades_navigation'); ?>

<style type="text/css">
	.trade_container {
		width:360px;
		float:left;
		margin-top:5px;
		overflow:hidden
	}

	.tc_box {
		background:#ddd;
		border-radius:6px;
		padding:5px;
		margin-bottom:5px;
	}
	.tc_header {
		height:60px;
		overflow:hidden
	}
	.tc_header img.avatar {
		float:left;
	}
	.tc_header .tc_user_box {
		margin-left:5px;
		padding-top:10px;
		float:left;
	}
	.tc_items {
		background:#fff;
		padding:5px;
		border-radius:4px;
		margin-top:5px;
		border:1px solid #d0d0d0;
		overflow:hidden;
	}
	.tc_items a {
		border:1px solid #fff;
		float:left;
		margin:1px;
		padding:1px;
		border-radius:3px;
	}
	.tc_items a img {
		float:left;
	}
	.xflip {
	    -moz-transform: scaleX(-1);
	    -webkit-transform: scaleX(-1) translate3d( 0, 0, 0);
	    -o-transform: scaleX(-1);
	    transform: scaleX(-1);
	    -ms-filter: fliph; /*IE*/
	    filter: fliph; /*IE*/
	}
	.modify_item {
		-webkit-transition: all 150ms ease;
		-moz-transition: all 150ms ease;
        -ms-transition: all 150ms ease;
        -o-transition: all 150ms ease;
		transition: all 150ms ease;
	}
	.modify_item:hover { border-color:#ee9d9c; }
	.modify_item:active { border-color:#aaa; background:#eee; opacity:0.7; }
	.empty_offer { text-align:center; color:#aaa; padding:15px; font-size:14px; letter-spacing:1px; }

	#inventory_items > div.tab-pane { overflow:hidden }
	#inventory_items > div.tab-pane > a { float:left; margin:1px 1px; padding:2px; border-radius:4px; }
</style>

<script type="text/javascript">
	var user_role = "<?php echo $role ?>",
		inventory_keys = <?php echo $item_names ?>,
		trade_id = <?php echo $trade_id ?>,
		inventory_match = <?php echo $item_data ?>;
</script>

<?php if ($trade['sender_status'] == 1 && $trade['receiver_status'] == 1): ?>
	<br>
	<div class="alert alert-success">
	  <strong>Trade completed!</strong> This trade has been completed, you can view it for logging purposes only.
	</div>
<?php endif ?>

<?php if ($trade['sender_status'] == 2 && $trade['receiver_status'] == 2): ?>
	<br>
	<div class="alert alert-warning">
	  <strong>Trade canceled:</strong> This trade has been canceled, you can view it for logging purposes only.
	</div>
<?php endif ?>

<div class="clearfix" style="position:relative;">
	<div class="trade_container" style="margin-right:6px" id="sender_container">
		<div class="tc_header">
			<img src="/images/avatars/<?php echo $trade['trade_sender'] ?>_headshot.png" class="avatar xflip" alt="" />
			<div class="tc_user_box">
				<strong><?php echo anchor('/user/'.urlencode($trade['sender']->user->username), $trade['sender']->user->username) ?></strong><br />
				<span>Trade status: <?php echo $trade['sender_status_text'] ?></span>
			</div>
		</div>
		<div class="tc_box">
			<div class="tc_currencies row">
				<div class="currency palladium span3">
					<span>Palladium:</span>
					<strong><?php echo number_format($trade['sender_palladium']) ?></strong>
				</div>
				<div class="currency berries span3">
					<span>Berries:</span>
					<strong><?php echo number_format($trade['sender_berries']) ?></strong>
				</div>
			</div>
			<div class="tc_items">
				<?php if (count($trade['sender']->items) > 0): ?>
					<?php foreach ($trade['sender']->items as $item): ?>

						<?php if ($item['item_type'] == 'item' && $item['amount'] > 1): ?>
							<? for ($i=0; $i < $item['amount']; $i++): ?>
								<?php echo $item['element'] ?>
							<? endfor; ?>
						<?php else: ?>
							<?php echo $item['element'] ?>
						<?php endif ?>

					<?php endforeach ?>
					<div class="empty_offer" style="display:none"> No items in offer </div>
				<?php else: ?>
					<div class="empty_offer"> No items in offer </div>
				<?php endif ?>
 			</div>
		</div>
		<?php if ($trade['trade_sender'] == $this->system->userdata['user_id'] && ($trade['sender_status'] > 0 && $trade['receiver_status'] > 0) == FALSE): ?>
			<?php if ($trade['receiver_status'] != 0): ?>
				<div class="pull-right">
					<?php if ($trade['sender_status'] == 0): ?>
						<form action="/trades/accept_trade/<?php echo $trade_id ?>" method="POST" style="display:inline">
							<button class="btn btn-success" type="submit"><i class="icon-ok icon-white"></i> Accept Trade</button>
						</form>
					<?php else: ?>
						<button class="btn btn-success disabled" type="submit"><i class="icon-ok icon-white"></i> Trade accepted</button>
					<?php endif ?>
					or
					<form action="/trades/accept_trade/<?php echo $trade_id ?>/cancel" method="POST" style="display:inline">
						<button class="btn btn-danger" type="submit"><i class="icon-remove icon-white"></i> Cancel Trade</button>
					</form>
				</div>
			<?php else: ?>
				<!-- <small>Waiting for other party to make offer and accept</small> <button class="btn btn-success disabled"><i class="icon-lock icon-white"></i> Pending on other party...</button> -->
				<?php if ($trade['sender_status'] == 0): ?>
					<button class="btn btn-success" id="warn_pre_accept" href="#"><i class="icon-ok icon-white"></i> Pre-accept Trade</button>
				<?php else: ?>
					<button class="btn btn-success disabled" type="submit"><i class="icon-ok icon-white"></i> Trade accepted</button>
				<?php endif ?>
				or
				<form action="/trades/accept_trade/<?php echo $trade_id ?>/cancel" method="POST" style="display:inline">
					<button class="btn btn-danger" type="submit"><i class="icon-remove icon-white"></i> Cancel Trade</button>
				</form>
			<?php endif ?>
		<?php endif ?>
	</div>
	<div class="trade_container" id="receiver_container">
		<div class="tc_header">
			<img src="/images/avatars/<?php echo $trade['trade_receiver'] ?>_headshot.png" class="avatar" style="float:right; margin-left:5px;" alt="" />
			<div class="tc_user_box pull-right" style="text-align:right; float:right;">
				<strong><?php echo anchor('/user/'.urlencode($trade['receiver']->user->username), $trade['receiver']->user->username) ?></strong><br />
				<span>Trade status: <?php echo $trade['receiver_status_text'] ?></span>
			</div>
		</div>
		<div class="tc_box">
			<div class="tc_currencies">
				<div class="tc_currencies row">
					<div class="currency palladium span3">
						<span>Palladium:</span>
						<strong><?php echo number_format($trade['receiver_palladium']) ?></strong>
					</div>
					<div class="currency berries span3">
						<span>Berries:</span>
						<strong><?php echo number_format($trade['receiver_berries']) ?></strong>
					</div>
				</div>
			</div>
			<div class="tc_items">
				<?php if (count($trade['receiver']->items) > 0): ?>
					<?php foreach ($trade['receiver']->items as $item): ?>
						<?php if ($item['item_type'] == 'item' && $item['amount'] > 1): ?>
							<? for ($i=0; $i < $item['amount']; $i++): ?>
								<?php echo $item['element'] ?>
							<? endfor; ?>
						<?php else: ?>
							<?php echo $item['element'] ?>
						<?php endif ?>

					<?php endforeach ?>
					<div class="empty_offer" style="display:none"> No items in offer </div>
				<?php else: ?>
					<div class="empty_offer"> No items in offer </div>
				<?php endif ?>
			</div>
		</div>
		<?php if ($trade['trade_receiver'] == $this->system->userdata['user_id'] && ($trade['sender_status'] > 0 && $trade['receiver_status'] > 0) == FALSE): ?>
			<div class="pull-left">
				<?php if ($trade['receiver_status'] == 0): ?>
					<form action="/trades/accept_trade/<?php echo $trade_id ?>" method="POST" style="display:inline">
						<button class="btn btn-success" type="submit"><i class="icon-ok icon-white"></i> Accept Trade</button>
					</form>
					or
					<form action="/trades/accept_trade/<?php echo $trade_id ?>/cancel" method="POST" style="display:inline">
						<button class="btn btn-danger" type="submit"><i class="icon-remove icon-white"></i> Cancel Trade</button>
					</form>
				<?php else: ?>
					<button class="btn btn-success disabled" type="submit"><i class="icon-ok icon-white"></i> Trade accepted</button>
					or
					<form action="/trades/accept_trade/<?php echo $trade_id ?>/cancel" method="POST" style="display:inline">
						<button class="btn btn-danger" type="submit"><i class="icon-remove icon-white"></i> Cancel Trade</button>
					</form>
				<?php endif ?>
			</div>
		<?php endif ?>
	</div>
</div>
<?php if (($trade['sender_status'] > 0 && $trade['receiver_status'] > 0) == FALSE): ?>
	<hr>
	<div class="clearfix">
		<div class="def_structure">
			<div class="def_content">
				<h3>Modify currency</h3>
				<div class="push_up">
					<form class="form-search pull-left" id="add_currency" method="POST" action="/trades/add_currency/<?php echo $trade_id ?>">
						<input type="text" name="total_amount" id="total_currency_amount" class="button_sync" style="width:70px;" placeholder="500">
						<select name="currency_type" id="trade_currency" style="width:160px;">
							<?php foreach ($currencies as $currency): ?>
								<option value="<?php echo strtolower($currency) ?>"><?php echo $currency ?></option>
							<?php endforeach ?>
						</select>
						<select name="modify_method" id="modify_method" style="width:70px;">
							<option value="add">Add</option>
							<option value="remove">Remove</option>
						</select>
						<button type="submit" class="main_button" id="modify_currency">Modify</button>
					</form>
				</div>
			</div>
			<div class="def_content">
				<h3>Search your items</h3>
				<div class="push_up">
					<form class="form-search" style="margin-top:3px;" id="search_inventory" method="GET" action="/trades/view_trade/<?php echo $trade_id ?>">
						<input type="text" id="search_inventory_query" name="search_items" class="input-medium button_sync" placeholder="Search inventory...">
						<!-- <button type="submit" class="main_button" id="search_inventory_btn"><i class="icon-search icon-white"></i> Search</button> -->
					</form>
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class="clearfix">
		<?php if (count($items) > 0): ?>
		<ul class="nav nav-tabs" id="avatar_items">
			<?php foreach ($items as $sub_array): ?>
				<?php foreach ($sub_array as $tab_name => $tab_array): ?>
					<?php if (strtolower($tab_name) == 'bugs'): ?>
						<li><a href="#<?php echo strtolower($tab_name) ?>"><i class="icon-leaf"></i> <?php echo $tab_name ?></a></li>
					<?php else: ?>
						<li><a href="#<?php echo strtolower($tab_name) ?>"><?php echo $tab_name ?></a></li>
					<?php endif ?>
				<?php endforeach ?>
			<?php endforeach ?>
			<!-- <li class="pull-right"><a href="#inventory_search_response"><i class="icon-search"></i></a></li> -->
		</ul>

		<div class="tab-content" id="inventory_items">
			<?php foreach ($items as $sub_array): ?>
				<?php foreach ($sub_array as $tab_name => $tab_array): ?>
					<div class="tab-pane" id="<?php echo strtolower($tab_name) ?>">
						<?php foreach ($tab_array as $item_id => $item): ?>
							<?php if ($item['amount'] > 1): ?>
								<?php while ($item['amount']--): ?>
									<?php echo $item['element'] ?>
								<?php endwhile ?>
							<?php else: ?>
								<?php echo $item['element'] ?>
							<?php endif ?>
						<?php endforeach ?>
					</div>
				<?php endforeach ?>
			<?php endforeach ?>
			<!-- <div class="tab-pane" id="inventory_search_response">Here we show the search responses.</div> -->
		</div>
		<?php else: ?>
			<div class="well">You have no items to add into the trade</div>
		<?php endif ?>
	</div>
	<br />
	<!-- Modal -->
	<div id="loading_large_transaction" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="loading_large_transaction" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel">This may take a bit...</h3>
		</div>
			<div class="modal-body" style="text-align:center;">
				<p>We thought it would be polite to let you know that the little server is working hard to trade your large batch of items. Depending on the load of the server and the amount of items you want to trade, this could take about 30-60 seconds.</p>
				<br />
				<img src="/images/icons/large_loader.gif" alt="" />
			</div>
		<div class="modal-footer">
		</div>
	</div>

	<div id="accept_confirmation" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="accept_confirmation" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>A word of caution</h3>
		</div>
		<div class="modal-body" style="text-align:center;">
			<p>
				Are you sure you want to accept the trade before they've made an offer? If they accept the trade, it will be finalized without you receiving anything. You're safer waiting for them to make an offer and accept the trade first.
			</p>
			<hr />
			<p><strong>Note:</strong> Any item or change you make to the trade is offered</p>
		</div>
		<div class="modal-footer">
			<form action="/trades/accept_trade/<?php echo $trade_id ?>" method="POST" style="display:inline">
				<button class="btn btn-success" type="submit"><i class="icon-ok icon-white"></i> I understand, accept anyway</button>
			</form>
			<button id="wait_for_offer" class="btn">Wait for their offer</button>
		</div>
	</div>
<?php endif ?>