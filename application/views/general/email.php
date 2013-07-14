<?php if ($success): ?>
  <br />
  <div class="alert alert-success">
    <strong>Your email has been added!</strong> Thank you for your interest for beta testing.
  </div>
<?php endif ?>
<div class="span9">
  <form action="/emails/" method="POST">
    <fieldset>
      <legend>Want to signup for beta?</legend>
      <br />
      <label>Your email:</label>
      <input type="email" value="<?php echo $user['user_email'] ?>">
      <small class="help-block" style="width:260px">Make sure you check this email once in a while, since we'll be sending the invitation to it</small>
      <button type="submit" class="btn btn-primary">Sign me up!</button>
    </fieldset>
  </form>
</div>