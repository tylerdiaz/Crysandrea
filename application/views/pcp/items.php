		<h1>{page_title}</h1>
		<?php echo $this->pagination->create_links();?>
		<div class="clear"></div>
			<table cellpadding="0" cellspacing="0" class="clean">
				<thead>
					<tr>
						<th>Thumbnail</th>
						<th>Name</th>
						<th>Manage</th>
						<th>Gender</th>
						<th>Layer</th>
					</tr>
				</thead>

			<?php $i = 0; foreach($items as $item){ ?>	

				<tr class="<?=($i%2 ? 'alt' : '')?>">
					<td> <img src="<?=site_url('images/items/'.$item['thumb'])?>" /> </td>
					<td> <?=$item['name']?> (#<?=$item['item_id']?>)</td>
					<td><a href="<?=site_url('item_manager/item_id/'.$item['item_id'])?>" title="<?=$item['name']?>" target='_blank'  >MANAGE</a></td>
					<td> <?=$item['gender']?> </td>
					<td> <?=$item['layer']?></td>
				</tr>
			<?php $i++; } ?>
			</table>
		
		<?php echo $this->pagination->create_links();?>
