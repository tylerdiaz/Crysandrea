<?php $this->load->view('trades/trades_navigation'); ?>

<style type="text/css">
	.def_structure {
		overflow:hidden
	}

	.def_structure div.def_content {
		overflow:hidden;
		padding:10px 0;
		border-bottom:1px solid #ddd;
		/*background:orange;*/
	}

	.def_structure div h3 {
		float:left;
		width:170px;
		font-weight:normal;
		font-size:16px;
		color:#888;
		padding:3px 0 0 20px;
		font-family:'Nunito', "Lucida Grande", "arial", sans-serif;
	}
	div.def_content > div {
		width:536px;
		float:left;
	}
	input.button_sync {
		font-size:16px;
		padding:7px 8px;
		vertical-align:top;
		width:270px;
	}
	.push_up {
		padding-top:5px;
	}
	.bare { margin:0; padding:0; }
	.trade_block {
		display:block;
		border:2px solid #eee;
		margin:5px 0;
		padding:2px 3px 5px;
		width:350px;
		max-width:400px;
		border-radius:5px;
		-webkit-transition: border-color 150ms linear;
		-moz-transition: border-color 150ms linear;
		-o-transition: border-color 150ms linear;
		transition: border-color 150ms linear;
	}
	.trade_block:hover {
		border-color:orange;
	}
	.trade_block span {
		color:#777;
		font-size:12px;
	}
</style>
<br />

<div class="def_structure">
	<div class="def_content">
		<h3>Create a trade</h3>
		<div class="push_up">
			<form action="/trades/create_trade" autocomplete="off" class="bare" method="POST">
				<input type="text" class="button_sync" id="new_trade_username" name="username" placeholder="Who would you like to trade with?" data-suggest-friends="true" />
				<button type="submit" class="main_button" id="create_trade_btn" autocomplete="off" data-toggle="button" data-loading-text="Creating trade...">Create trade</button>
			</form>
		</div>
	</div>
	<div class="def_content">
		<h3>Active trades</h3>
		<div>
		<?php foreach ($trades as $trade): ?>
		<a href="/trades/view_trade/<?php echo $trade['trade_id'] ?>" class="clearfix trade_block">
			<img src="/images/avatars/<?php echo ($trade['trade_receiver'] == $this->system->userdata['user_id'] ? $trade['trade_sender'] : $trade['trade_receiver']) ?>_headshot.png" alt="" class="avatar_headshot" width="60" height="60" />
			<div class="left" style="margin-top:10px">
				<strong><?php echo $trade['trade_title'] ?></strong><br />
				<span>Waiting on <strong>you</strong></span>
			</div>
		</a>
		<?php endforeach ?>
		</div>
	</div>
</div>