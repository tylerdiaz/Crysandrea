		<h1 class="left">{page_title}</h1>
		<?php form_open('layers/move/') ?>		
		<div class="clear"></div>
			<table cellpadding="0" cellspacing="0" class="clean">
				<thead>
					<tr>
						<th width="240">Layer name</th>
						<th width="80">Layer order</th>
						<th width="60">Composite</th>
						<th width="60">Move after</th>
					</tr>
				</thead>

			<?php $i = 0; foreach($layers as $layer){ ?>	

				<tr class="<?=($i%2 ? 'alt' : '')?>">
					<td> <?=$layer['name']?> </td>
					<td> <?=$layer['order']?> </td>
					<td> <?=$layer['composite']?> </td>
					<td> <input type="radio" name="layer_id" value="<?=$layer['id']?>" /></td>
				</tr>
			<?php $i++; } ?>
			</table>
			
			<div class="right">Move after: <select name="layer_move">
			<?php foreach($layers as $layer_part){ ?>
				<option value="<?php echo $layer_part['order'] ?>"><?php echo $layer_part['name'] ?></option>
			<?php } ?></select><?=button('Move layer', 'cute')?></div>
			</form>
			
		<div class="clear"></div>
			<br />
			<hr />
			<br />
			
			<h2>Create a new layer:</h2>
			<?php echo form_open('layers/create'); ?>
				<ul class="form">
					<li><label>Name: </label><input type="input" class="input" name="name" /></li>
					<li><label>Add after: </label><select name="layer_move">
			<?php foreach($layers as $layer_part){ ?>
				<option value="<?php echo $layer_part['order'] ?>"><?php echo $layer_part['name'] ?></option>
			<?php } ?></select></li>
					<li><?=button('Create layer', 'cute')?></li>
				</ul>
			</form>
			<div class="clear"></div>
