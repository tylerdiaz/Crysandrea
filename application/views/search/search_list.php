<div id="search-container">
	<div id="search-results">
	<?php if (!empty($results)): ?>
	<?php foreach($results->result() as $result): ?>
		<?php $this->load->view('search/partials/'.$search_type, $result); ?>
	<?php endforeach; ?>
	<?php else: ?>
		<p>Sorry boss, we couldn't find anything.</p>
	<?php endif; ?>
	</div>

	<!-- clear: both -->
	<div id="search-options">
		<!-- float: left -->
		<div id="search-type">
		<?= form_open('/search/get_items', array('id' => 'search-type')) ?>
		<select name="search_type">
			<option value="items">Items</option>
			<option value="topic_titles">Topic Titles</option>
			<option value="site_features">Site Features</option>
			<option value="users">Users</option>
			<option value="mailbox">Mailbox</option>
		</select>
		</form>
		<script type="text/javascript">
		$(document).ready(function() {
			var search_results = $('#search-results');
			$('search_type').blur(function() {
				// TODO:: Implement AJAX
				// Replace #search_results
			});
		});
		</script>

		<!-- float: right -->
		<div id="search-link">
			<a href="search/get_all_items">View all results</a>
		</div>
	</div>
</div>
