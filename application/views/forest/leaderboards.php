<?php $this->load->view('forest/forest_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<style type="text/css">
	.leaderboard_header {
		text-align:center;
		color:white;
		margin:5px 0 15px;
		font-weight:normal;
		font-family:Georgia;
		color:#a1c79b;
		letter-spacing:2px;
		font-variant:small-caps;
	}
</style>
<div class="clearfix" style="margin:10px 0;">
	<div class="grid_3">
	<h3 class="leaderboard_header">Global leaderboards</h3>
	<?php if (count($lb_global) > 0): ?>
		<ul class="ladder">
			<li>
				<span class="left">
					Includes all those who ever hunted...
				</span>
				<span class="right">
					 Place #<?php echo number_format($your_place) ?>
				</span>
			</li>
			<?php foreach ($lb_global as $key => $user): ?>
				<?php if ($key == 0): ?>
					<li id="first_place">
						<?php echo  anchor('user/'.$user['username'], image('avatar/headshot/'.$user['user_id'], 'class="left" style="margin-right:10px;" title="'.$user['username'].'"')) ?>
						<h3><?php echo $key+1 ?>) <?php echo anchor('user/'.urlencode($user['username']), $user['username'])?></h3>
						<p><?php echo $user['username']?> has achieved the level <strong><?php echo $user['level']?></strong>! What a skilled hunter!</p>
					</li>
				<?php else: ?>
					<li class="<?php echo ($user['user_id'] == $this->session->userdata('user_id') ? 'special_me' : '')?>"><?php echo $key+1 ?>) <strong><?php echo anchor('user/'.urlencode($user['username']), $user['username'])?></strong> is a forester in level <?php echo $user['level']?></li>
				<?php endif ?>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	</div>

	<div class="grid_3">
	<h3 class="leaderboard_header">Friend leaderboards</h3>
	<?php if (count($lb_friends) > 0): ?>
		<ul class="ladder">
			<li>
				<span class="left">
					Includes all your friends who ever hunted...
				</span>
			</li>
			<?php foreach ($lb_friends as $key => $user): ?>
				<?php if ($key == 0): ?>
					<li id="first_place">
						<?php echo  anchor('user/'.$user['username'], image('avatar/headshot/'.$user['user_id'], 'class="left" style="margin-right:10px;" title="'.$user['username'].'"')) ?>
						<h3><?php echo $key+1 ?>) <?php echo anchor('user/'.urlencode($user['username']), $user['username'])?></h3>
						<p><?php echo $user['username']?> has achieved the level <strong><?php echo $user['level']?></strong>! What a skilled hunter!</p>
					</li>
				<?php else: ?>
					<li class="<?php echo ($user['user_id'] == $this->session->userdata('user_id') ? 'special_me' : '')?>"><?php echo $key+1 ?>) <strong><?php echo anchor('user/'.urlencode($user['username']), $user['username'])?></strong> is a forester in level <?php echo $user['level']?></li>
				<?php endif ?>
			<?php endforeach ?>
		</ul>
	<?php endif ?>
	</div>

</div>
