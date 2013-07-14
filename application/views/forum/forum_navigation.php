<div class="feature_header">
	<h2 class="friends_icon"><?php echo (isset($navigation_header) ? $navigation_header : $page_title) ?></h2>
	<?php if ($this->session->userdata('user_id')): ?>
		<ul class="feature_navigation">
			<?php foreach ($routes as $url => $data): ?>
			<li class="<?php echo ($active_url === $url ? 'active' : '') ?>">
				<a href="/forum/<?php echo $url ?>"><?php echo $data ?></a>
			</li>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
</div>
