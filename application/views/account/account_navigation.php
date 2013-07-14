<h3 style="text-align:center; font-size:17px; color:#444; margin:16px 5px 20px;"><img src="<?php echo $routes[$active_url]['icon'] ?>" alt="" width="24" height="24" style="margin-top:-2px"/> <?php echo $routes[$active_url]['title'] ?> &nbsp;<span style="font-weight:normal; color:#888; font-size:13px;"><?php echo $routes[$active_url]['description'] ?></span></h3>

<style type="text/css">
label.control-label { font-size:13px }
a.suggested {
	color:#888;
	cursor:default;
	text-decoration:line-through;
	background:url(/images/icons/tiny_success.png)no-repeat 2px center;
	padding-left:16px;
	margin-left:3px;
}
a.suggested:hover {
	text-decoration:line-through;
}
</style>
<ul class="nav nav-tabs" style="box-shadow:inset 0 -2px 6px #f2f2f2; padding-left:122px;">
	<?php foreach ($routes as $url => $data): ?>
		<li class="<?php echo ($active_url === $url ? 'active' : '').' '.($data['enabled'] ? '' : 'disabled') ?>">
			<a href="/account/<?php echo $url ?>" title="<?php echo ($data['enabled'] ? $data['description'] : 'This feature is under construction') ?>"><?php echo $data['title'] ?></a>
		</li>
	<?php endforeach ?>
</ul>