<div class="feature_header">
	<h2 class="">Edit your post</h2>
	<ul class="feature_navigation">
		<li><a href="/topic/view/<?php echo $topic['topic_id'] ?>">&lsaquo; Return to <?php echo $topic['topic_title'] ?></a></li>
	</ul>
</div>
<br />
<div class="grid_9" style="padding:3px 10px;">
	<?php if(validation_errors()): ?>
		<?php $this->load->view('partials/notices/error.php', array('data' => validation_errors())) ?>
	<? endif; ?>
	<?php echo form_open('/forum/edit_post/'.$this->uri->rsegment(3, 0), array('id' => 'edit_post')) ?>
		<?php if ($first_post): ?>
			<div class="control-group">
				<label class="control-label" for="title" id="title_label">Topic title:</label>
				<div class="controls"><input type="text" id="title" name="title" value="<?php echo stripslashes($topic['topic_title']) ?>" class="input-xxlarge" /></div>
			</div>
		<?php endif ?>
		<div class="control-group">
			<label class="control-label" for="message">Edit your post</label>
			<div class="controls">
				<textarea name="message" id="message" cols="30" rows="10" class="input-xxlarge"><?php echo stripslashes($post['text']) ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="main_button" id="signin_btn" autocomplete="off" data-toggle="button" data-loading-text="Saving changes...">Save changes</button>
			</div>
		</div>
	</form>
</div>