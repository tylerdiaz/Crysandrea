<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'staff_panel')); ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Ticket</th>
			<th>Created by</th>
			<th>Created on</th>
			<th>Status</th>
			<th>Solved by</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($tickets as $ticket): ?>
			<tr style="opacity:<?php echo $ticket['status'] == 'pending' ? '1' : '0.7' ?>">
				<td><a href="/staff_panel/view_ticket/<?php echo $ticket['ticket_id'] ?>"><strong><?php echo $ticket['issue'] ?></strong></a></td>
				<td width="130"><?php echo $ticket['username'] ?></td>
				<td width="200"><small><?php echo date('D, M jS, Y (g:i:s A)', strtotime($ticket['timestamp'])) ?></small></td>
				<td width="80"><?php echo ucfirst($ticket['status']) ?></td>
				<td width="80"><?php echo ucfirst($ticket['attended_by']) ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<div class="clearfix">
	<form class="form-search" action="/staff_panel/tickets">
	  <input type="text" class="input-medium search-query" name="user" value="<?php echo $this->input->get('user') ?>" placeholder="Search by username..." />
	  <button type="submit" class="btn">Search</button>
	</form>
</div>
<span class="paginate">
	<?php echo $this->pagination->create_links(); ?>
</span>
