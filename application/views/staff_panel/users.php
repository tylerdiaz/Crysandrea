<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'staff_panel')); ?>
<br />
<div class="clearfix">
	<div class="span4">
		<form id="refund_items" method="POST">
		  <fieldset>
		    <legend>Refund an item</legend>
		    <label>Item name</label>
		    <input type="text" id="item_name" placeholder="Find item by name…" />
		    <label>User ID</label>
		    <input type="text" id="user_id" placeholder="Type used id here" />
		    <button type="submit" class="btn">Submit</button>
		  </fieldset>
		</form>
	</div>
	<div class="span4" id="selected_items">
		<h5>Listed items</h5>
		<div class="media hide" id="skeleton_template">
			<img class="media-object pull-left" data-src="holder.js/64x64" src="http://crysandrea.com//images/items/d834e29075486ea8fbf9056eaaf0eb1d.png" id="item_thumbnail">
			<div class="media-body">
				<strong class="media-heading" id="item_name">Item name</strong><br />
				x <input type="text" value="1" class="input-mini" id="item_amount" />
			</div>
		</div>
	</div>
</div>