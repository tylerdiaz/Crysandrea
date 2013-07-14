<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'pcp')); ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th width="42">Thumb</th>
      <th>Item id</th>
      <th>Item name</th>
      <th>Layer</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  	<?php foreach ($items as $item): ?>
	  	<tr>
	  	  <td><img src="/images/items/<?php echo $item['thumb'] ?>" alt=""></td>
	  	  <td><?php echo $item['item_id'] ?></td>
	  	  <td><strong><?php echo $item['name'] ?></strong></td>
	  	  <td><?php echo $item['layer_name'] ?></td>
	  	  <td>
	  	  	<a href="/pcp/edit_item/<?php echo $item['item_id'] ?>" class="btn"><i class="icon-pencil"></i> Edit</a>
	  	  </td>
	  	</tr>
  	<?php endforeach ?>
  </tbody>
</table>
<div class="clearfix">
	<span class="left paginate">
		<?php echo $this->pagination->create_links(); ?>
	</span>
</div>
