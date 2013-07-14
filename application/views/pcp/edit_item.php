<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'pcp')); ?>

<br />
<div class="row">
	<div class="span4">
		<form class="">
			<div class="control-group">
				<label>Item name</label>
				<input type="text" name="name" value="<?php echo $item['name'] ?>">
			</div>
			<div class="control-group">
				<label>Gender</label>
				<select name="gender" id="itemgender">
				    <option selected="selected">Unisex</option>
				    <option>Male</option>
				    <option>Female</option>
				</select>
			</div>
			<div class="control-group">
				<label>Layer</label>
				<select name="layer">
				    <?php
				    foreach($layers as $k => $v){
				    ?>
				    <option <?php if($item['layer'] == $v['id']){ ?> selected="selected" <?php } ?> value="<?=$v['id'];?>"><?=$v['name'];?> (<?=$k;?>)</option>
				    <?php
				    }
				    ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary">Update main item</button>
		</form>
	</div>
	<div class="span7">
	  <fieldset>
	    <legend><?php echo $item['name'] ?> parts</legend>
	    <?php foreach ($children as $item_part): ?>
	    <div class="clearfix">
		    <form class="pull-left" action="pcp/edit_subitem/<?php echo $item_part['id'] ?>" method="POST" enctype="multipart/form-data">
	    		<label>Gender</label>
	    		<select name="gender" id="itemgender">
	    		    <option <?php echo ($item_part['gender'] == 'Unisex' ? 'selected="selected"' : '') ?>>Unisex</option>
	    		    <option <?php echo ($item_part['gender'] == 'Male' ? 'selected="selected"' : '') ?>>Male</option>
	    		<option <?php echo ($item_part['gender'] == 'Female' ? 'selected="selected"' : '') ?>>Female</option>
	    		</select>
	        	<label>Layer</label>
	        	<select name="layer">
	        	    <?php foreach($layers as $k => $v): ?>
	        	    	<option <?php if($item_part['layer'] == $v['id']){ ?> selected="selected" <?php } ?> value="<?=$v['id'];?>"><?=$v['name'];?> (<?=$k;?>)</option>
	        	    <?php endforeach; ?>
	        	</select>
	        	<label>New image <small>(leave empty to ignore)</small></label>
	        	<input type="file" name="userfile" style="width:240px; line-height:1" />

		        <br clear="all" />
		        <br clear="all" />
		        <button type="submit" class="btn">Update item part</button> or <a href="#" style="color:Red">delete</a>
		    </form>
		    <img src="/images/items/<?php echo $item_part['image_path'] ?>" alt="" class="pull-right sillouete_<?php echo strtolower($item_part['gender']) ?>" style="margin:-20px 0">
	    </div>
	    <hr>
	    <?php endforeach ?>
	  </fieldset>
	</div>
</div>

<style type="text/css">
	.sillouete_male {
		background:url(/images/avatars/male_sil.png)
	}
	.sillouete_female {
		background:url(/images/avatars/female_sil.png)
	}
</style>