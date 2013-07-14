<?php $this->load->view('forest/forest_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<style type="text/css">
	.bug_icon {
		float:left;
		width:170px;
		margin:3px;
		border-radius:6px;
		border:2px solid #aaa;
		background:white;
		position:relative;
	}
	.bug_icon img {
		border-radius:6px;
	}
	.bug_info {
		position:absolute;
		bottom:0;
		left:0;
		width:158px;
		background:rgba(0, 0, 0, 0.8);
		color:white;
		font-size:12px;
		border-radius:0 0 4px 4px;
		padding:5px 6px;
		border-top:1px solid #111;
		overflow:hidden
	}
	.bug_info span.amount { color:#aaa; font-weight:bold }
	.fancy_stats_header {
		font-family:Georgia;
		font-variant: small-caps;
		font-weight:normal;
		font-size:18px;
		margin:0;
		line-height:1.3;
		letter-spacing:1px;
	}
	.quiet { color:#ccc; }
</style>

<div style="height:140px; overflow:hidden; border-bottom:1px solid #5a7f43;">
	<img src="/avatar/view/<?php echo $user_id ?>/1" class="left" alt="" style="margin-top:-20px;">
	<div class="left" style="margin-left:10px; color:#eee; padding:10px 0">
		<h3 class="fancy_stats_header"><?php echo $username ?>'s foresting statistics</h3>
		<ul>
			<li><span class="quiet">Leaderboard place</span>: #<?php echo $leaderboard_place ?></li>
			<li><span class="quiet">Last Hunted</span>: <?php echo human_time($hunter_data['last_hunt']) ?></li>
			<?php foreach ($forest_stats as $key => $value): ?>
				<li><span class="quiet">Total <?php echo $key ?>s owned</span>: <?php echo $caught_stats[$key] ?>/<?php echo $value ?></li>
			<?php endforeach ?>
		</ul>
	</div>
</div>

<div style="overflow:hidden; margin:10px 3px">
	<?php foreach ($bugs as $bug): ?>
		<div class="bug_icon">
			<?php if ( ! is_null($bug['total'])): ?>
				<img src="<?php echo $bug['image'] ?>" alt="" width="170" height="170" />
				<div class="bug_info">
					<span class="left"><?php echo $bug['name'] ?></span>
					<span class="right amount">x<?php echo $bug['total'] ?></span>
				</div>
			<?php else: ?>
				<img src="/images/insects/unset.gif" alt="" width="170" height="170" />
				<div class="bug_info">
					<span class="left" style="opacity:0.6"><?php echo $bug['name'] ?></span>
				</div>
			<?php endif ?>
		</div>
	<?php endforeach ?>
</div>