<?php $this->load->view('layout/feature_navigation', array('routes' => $routes, 'active_url' => $active_url, 'core' => 'shops')); ?>
<style>
	#shops_directory {
		list-style: none;
		overflow:hidden;
		margin:3px 1px;
	}
	#shops_directory li a {
		padding:10px 7px;
		overflow:hidden;
		float:left;
		width:332px;
		margin:5px;
		min-height:108px;
		border:2px solid #91bae0;
		border-radius:6px;
		-webkit-border-radius:6px;
		-moz-border-radius:6px;
        -webkit-transition: all 250ms ease;
		-moz-transition: all 250ms ease;
        -ms-transition: all 250ms ease;
        -o-transition: all 250ms ease;
		transition: all 250ms ease;
	}
	#shops_directory p {
		color:#444;
		margin:0;
		overflow:hidden;
		font-size:12px;
	}
	#shops_directory li a strong {
		display:block;
		margin-top:5px;
		font-weight:bold;
		font-family:Helvetica;
		line-height:1.2;
		font-size:18px;
		color:#0097AE;
        -webkit-transition: all 250ms ease;
		-moz-transition: all 250ms ease;
        -ms-transition: all 250ms ease;
        -o-transition: all 250ms ease;
		transition: all 250ms ease;
	}
	#shops_directory li a:hover {
		text-decoration:none;
		border-color:orange;
	}
	#shops_directory li a:hover strong {
		text-decoration:underline;
		color:orange;
	}
</style>
<ul id="shops_directory">
	<?php foreach($shops as $shop): ?>
		<li>
			<a href="<?=site_url('shops/view/'.$shop['shop_id'])?>">
				<?= image('images/npc_head/'.strtolower($shop['shop_keeper']).'.png', "class=\"left\" style=\"margin-right:7px\" width=\"70\" height=\"70\"") ?>
				<strong><?=$shop['shop_name']?></strong>
				<p><?=$shop['short_description']?></p>
			</a>
		</li>
	<?php endforeach ?>
</ul>