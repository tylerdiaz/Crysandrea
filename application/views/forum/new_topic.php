<div class="feature_header">
	<h2 class="">Create a topic in <?php echo $forum_data['forum_name'] ?></h2>
	<ul class="feature_navigation">
		<li><a href="/forum/view/<?php echo $forum_data['parent_id'] ?>">&lsaquo; Return to <?php echo $forum_data['forum_name'] ?></a></li>
	</ul>
</div>
<br />
<div class="grid_9" style="padding:3px 10px;">
	<?php if(validation_errors()): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
	<? endif; ?>
	<?php echo form_open('/forum/new_topic/'.$this->uri->rsegment(3, 0), array('id' => 'create_topic')) ?>
		<div class="control-group">
			<label class="control-label" for="title" id="title_label">Topic title:</label>
			<div class="controls"><input type="text" id="title" name="title" value="" class="input-xxlarge" /></div>
		</div>
		<div class="control-group">
			<label class="control-label" for="message">What is the topic about?</label>
			<div class="controls">
				<textarea name="message" id="message" cols="30" rows="10" class="input-xxlarge"></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="main_button" id="signin_btn" autocomplete="off" data-toggle="button" data-loading-text="Posting topic...">Create topic</button>
			</div>
		</div>
	</form>
</div>