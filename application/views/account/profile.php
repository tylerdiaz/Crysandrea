<?php $this->load->view('account/account_navigation', array('routes' => $routes, 'active_url' => $active_url)); ?>

<div style="margin:15px 80px; position:relative;">
	<?php if(validation_errors()): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
	<? endif; ?>

	<?php foreach ($success as $key => $success_notice): ?>
		<div class="alert alert-success">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<strong><?php echo repeater('More ', $key).($key == 0 ? 'G' : 'g') ?>ood news!</strong> <?php echo $success_notice ?>
		</div>
	<?php endforeach ?>

	<div style="margin-top:15px;">
		<?php echo form_open('/account/profile', array('id' => 'save_profile_info')) ?>
			<div class="control-group">
				<label class="control-label" for="likes">Likes:</label>
				<div class="controls">
					<input type="text" placeholder="Oranges, zebras, music, Crysandrea" id="likes" name="likes" value="<?php echo $user['likes'] ?>" class="input-xxlarge" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dislikes">Dislikes:</label>
				<div class="controls">
					<input type="text" placeholder="Lawyers, freezing cold, hard gum" id="dislikes" name="dislikes" value="<?php echo $user['dislikes'] ?>" class="input-xxlarge" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="hobbies">Hobbies:</label>
				<div class="controls"><input placeholder="What do you do in your spare time?" type="text" id="hobbies" name="hobbies" value="<?php echo $user['hobbies'] ?>" class="input-xxlarge" /></div>
			</div>
			<div class="control-group">
				<label class="control-label" for="profile_bio">A short description about you:</label>
				<div class="controls">
					<textarea name="profile_bio" id="profile_bio" cols="30" rows="10" placeholder="Where are you from? What do you enjoy doing? What would you like people to know from you?" class="input-xxlarge"><?php echo $user['profile_bio'] ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="profile_css">Custom profile theme code: <strong style="background:#ffc; font-size:12px;">(Ignore this if you're not into coding)</strong>:</label>
				<div class="controls">
					<textarea name="profile_css" id="profile_css" cols="30" rows="3" placeholder="/* body { background:#222; color:#777; } */" class="input-xxlarge" style="resize:vertical;"><?php echo $user['profile_css'] ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="main_button" id="signin_btn" autocomplete="off" data-toggle="button" data-loading-text="Saving changes...">Save changes</button>
				</div>
			</div>
		</form>
	</div>
</div>