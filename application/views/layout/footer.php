				</div>
				<div id="footer">
					<a href="#logo" class="logo" title="&#9786; Back to top">Crysandrea</a>
					<span title="Create a support ticket" class="right" id="helpme"><a href="http://crysandrea.com/ticket" class="help">Staff support</a> </span>
					<div class="first">
						<a href="/?ref=footer">Home</a> &bull;
						<a href="/forum?ref=footer">Forum</a> &bull;
						<a href="/avatar?ref=footer">Avatar</a> &bull;
						<a href="/shops?ref=footer">Shops</a> &bull;
						<a href="/donate?ref=footer">Donate</a> &bull;
						<a href="/avatar?ref=footer">Inventory</a> &bull;
						<a href="/account?ref=footer">Account</a>
					</div>
					<br />
					<div>
						&copy;2012 Crysandrea, All rights reserved (<a href="/general/read_document/tos" >Code of Conduct</a> &bull; <a href="/general/read_document/privacy" >Your Privacy</a>)
					</div>
				</div>
			</div>
			<div id="search_results">
				<h3>Top 5 results for "Starter shirt" &#8901; 32 found in total</h3>
				<ul></ul>
				<div id="search_additional">
					<select name="search_type" id="search_type" class="left">
						<option value="items" default>Items</option>
						<option value="users">Users</option>
						<option value="topic_titles">Topics</option>
						<option value="mailbox">Mailbox</option>
						<option value="items">Trades</option>
						<option value="site_features">Features</option>
					</select>
					<a href="#" class="right" style="line-height:26px; margin-right:5px;">Show more results</a>
				</div>
			</div>
		</div>
<?php foreach ($scripts as $script): ?>
		<script src="<?php echo $script ?>" type="text/javascript" charset="utf-8"></script>
<?php endforeach ?>

<?php if ($this->session->userdata('user_id')): ?>
	<script src="<?php echo substr($this->config->item('base_url'), 0, -1); ?>:8001/socket.io/socket.io.js"></script>
	<script type="text/javascript">
		var raw_url = "<?php echo substr($this->config->item('base_url'), 0, -1); ?>",
			chirp_key = '<?php echo $this->chirp->encrypt_key($this->system->userdata['user_id']) ?>';
	</script>
	<script src="/global/js/chirp.js?123"></script>
<?php endif ?>

	<?php if (FALSE): ?>
		<div style="position:absolute; top:20%; left:30%; z-index:9999">
			<form action="/snowball/collect_item" method="POST">
				<input type="hidden" name="url" value="/<?php echo $this->uri->uri_string() ?>" />
				<input type="image" src="/images/event/snow.png" />
			</form>
		</div>
	<?php endif ?>

	</body>
</html>