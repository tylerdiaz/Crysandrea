<div id="zoomit">
	<div class="ta-left">
		<h2 class="single_body_header">
			<img src="/images/icons/star.png" alt="" width="22" height="22" />
			To get started, create your avatar!
		</h2>
		<div class="clearfix">
			<div style="float:left; width:180px; margin-left:-25px; text-align:center">
				<img src="/auth/get_preview" alt="" id="avatar_img" />
				<span style="font-size:16px; margin-right:5px;">I am a</span> <select name="gender_swap" id="gender_swap" class="input-mini">
					<option value="male" <?php echo $gender == 'male' ? 'selected="selected"' : '' ?>>Guy</option>
					<option value="female" <?php echo $gender == 'female' ? 'selected="selected"' : '' ?>>Girl</option>
				</select>
				<br />
				<div class="clearfix" style="margin:0 20px;">
					<a href="#" class="base_pallet <?php echo (in_array(5, $equipped_items) ? 'default_skin' : '') ?>" data-item-id="5" data-item-type="base" style="background:#fee3d4;"></a>
					<a href="#" class="base_pallet <?php echo (in_array(3, $equipped_items) ? 'default_skin' : '') ?>" data-item-id="3" data-item-type="base" style="background:#f7c4ab;"></a>
					<a href="#" class="base_pallet <?php echo (in_array(9, $equipped_items) ? 'default_skin' : '') ?>" data-item-id="9" data-item-type="base" style="background:#db9f83;"></a>
					<a href="#" class="base_pallet <?php echo (in_array(12, $equipped_items) ? 'default_skin' : '') ?>" data-item-id="12" data-item-type="base" style="background:#964227;"></a>
					<a href="#" class="base_pallet <?php echo (in_array(13, $equipped_items) ? 'default_skin' : '') ?>" data-item-id="13" data-item-type="base" style="background:#561c08;"></a>
				</div>
				<br />
				<a href="#" class="btn btn-mini" style="color:#333" id="item_shuffle"><i class="icon-random"></i> Randomize avatar</a>
			</div>
			<div class="left" style="width:365px;">
				<h5 style="margin:3px 0">Hair Styles:</h5>
				<div class="choice_select hairs_group">
					<?php foreach ($signup_options['hairs'] as $key => $item): ?>
						<a class="equippable_default <?php echo (in_array($item['item_id'], $equipped_items) ? 'default_equipped' : '') ?>" data-item-type="hairs" data-item-id="<?php echo $item['item_id'] ?>" href="#"><img src="/images/items/<?php echo $item['thumb'] ?>" width="42" height="42" alt=""></a>
					<?php endforeach ?>
				</div>
				<h5 style="margin:8px 0 3px">Eye Styles:</h5>
				<div class="choice_select eyes_group">
					<?php foreach ($signup_options['eyes'] as $key => $item): ?>
						<a class="equippable_default <?php echo (in_array($item['item_id'], $equipped_items) ? 'default_equipped' : '') ?>" data-item-type="eyes" data-item-id="<?php echo $item['item_id'] ?>" href="#"><img src="/images/items/<?php echo $item['thumb'] ?>" width="42" height="42" alt=""></a>
					<?php endforeach ?>
				</div>
				<h5 style="margin:8px 0 3px">Choose a shirt:</h5>
				<div class="choice_select shirts_group">
					<?php foreach ($signup_options['shirts'] as $key => $item): ?>
						<a class="equippable_default <?php echo (in_array($item['item_id'], $equipped_items) ? 'default_equipped' : '') ?>" data-item-type="shirts" data-item-id="<?php echo $item['item_id'] ?>" href="#"><img src="/images/items/<?php echo $item['thumb'] ?>" width="42" height="42" alt=""></a>
					<?php endforeach ?>
				</div>
				<h5 style="margin:8px 0 3px">Choose a pair of pants:</h5>
				<div class="choice_select pants_group">
					<?php foreach ($signup_options['pants'] as $key => $item): ?>
						<a class="equippable_default <?php echo (in_array($item['item_id'], $equipped_items) ? 'default_equipped' : '') ?>" data-item-type="pants" data-item-id="<?php echo $item['item_id'] ?>" href="#"><img src="/images/items/<?php echo $item['thumb'] ?>" width="42" height="42" alt=""></a>
					<?php endforeach ?>
				</div>
				<h5 style="margin:8px 0 3px">Choose a starter accessory:</h5>
				<div class="choice_select accessory_group">
					<?php foreach ($signup_options['accessory'] as $key => $item): ?>
						<a class="equippable_default <?php echo (in_array($item['item_id'], $equipped_items) ? 'default_equipped' : '') ?>" data-item-type="accessory" data-item-id="<?php echo $item['item_id'] ?>" href="#"><img src="/images/items/<?php echo $item['thumb'] ?>" width="42" height="42" alt=""></a>
					<?php endforeach ?>
				</div>
			</div>
		</div>
		<style type="text/css">
			.choice_select { overflow:hidden }
			.choice_select a {
				float:left;
				display:block;
				padding:3px;
				border-radius:4px;
				border:1px solid #ccc;
				margin:1px;
			}
			.choice_select a:hover { background-color:#ffa; border-color:#cc3; }
			.choice_select a.default_equipped { background:#b7d9f7; border-color:#81b2de; }
			.base_pallet { float:left; display:block; margin:1px; width:22px; height:18px; border-radius:5px; border:2px solid white; }
			.default_skin { border:2px solid #0c6dff; }
		</style>
		<br clear="all" />
		<div class="right" style="margin-top:5px;">
			<a href="/signup">Skip this part</a> or
			<a href="/signup?avatar=1" class="main_button" id="save_and_continue" autocomplete="off" data-toggle="button" data-loading-text="Saving...">Save and continue &nbsp;&rsaquo;</a>
		</div>
		<br clear="all" />
	</div>
</div>
<!-- <div style="text-align:center; color:#38697f; font-size:12px; opacity:0.8; margin:10px;">Don't have an account? You should <a href="#">create your account</a> to get started!</div> -->

