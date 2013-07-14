<div class="row">
	<div class="span6">
		<form method="POST" action="/ticket">
		  <fieldset>
		    <legend>Create a support ticket</legend>
		    <?php if ($success): ?>
			    <div class="alert alert-success">
			    	<strong>All set!</strong> Your ticket has been created. Depending on the amount of pending tickets, you should expect a reply between 1-12 hours.
			    </div>
		    <?php endif ?>
		    <label>Issue type:</label>
		    <select class="input" name="issue">
		    	<optgroup label="- Topics -">
		    		<option value="Move Topic" data-relevant-url="true">Move topic</option>
		    		<option value="Delete Thread" data-relevant-url="true">Delete thread</option>
		    		<option value="Delete Post" data-relevant-url="true">Delete post</option>
		    		<option value="Lock Topic" data-relevant-url="true">Lock topic</option>
		    		<option value="Misuse of Signature" data-relevant-url="true">Misuse of Signature</option>
		    		<option value="Bad topic image" data-relevant-url="true">Report image</option>
		    	</optgroup>
		    	<optgroup label="- Report User -">
		    		<option value="User Behavior" data-relevant-url="true">Behavior</option>
		    		<option value="User Scamming" data-relevant-url="true">Scamming</option>
		    		<option value="User Hacking" data-relevant-url="true">Hacking</option>
		    		<option value="User Spamming" data-relevant-url="true">Spamming</option>
		    		<option value="User Trolling" data-relevant-url="true">Trolling</option>
		    		<option value="User Harassment" data-relevant-url="true">Harassment</option>
		    		<option value="User Plagiarism" data-relevant-url="true">Plagiarism</option>
		    	</optgroup>
		    	<optgroup label="- Crysandrea -">
		    		<option value="Glitch encountered" selected>Glitch encountered</option>
		    		<option value="Staff behavior">Staff behavior</option>
		    	</optgroup>
		    	<optgroup label="- Other -">
		    		<option value="Username change">Username change</option>
		    		<option value="Item Refund">Item Refund</option>
		    		<option value="Palladium Refund">Palladium Refund</option>
		    		<option value="Other reason">Other reason</option>
		    	</optgroup>
		    </select>
		    <label>Description:</label>
		    <textarea id="ticket_description" style="width:345px; height:150px;" placeholder="Tell us more about how can we help you, what do you think should we do to help you as fast as we can?" name="description" class="input"></textarea>
		    <div class="hide" id="relevant_url">
		    	<br />
		    	<label>Relevant URL:</label>
		    	<input type="text" placeholder="http://crysandrea.com/" name="url" />
		    	<span class="help-block" style="margin:0; padding:0; line-height:1;">URL of user profile, topic, post, etc...</span>
		    </div>
		    <br />
		    <button type="submit" class="btn btn-primary">Send Ticket</button>
		  </fieldset>
		</form>
	</div>
	<div class="span4">
		<h4></h4>
	</div>
</div>