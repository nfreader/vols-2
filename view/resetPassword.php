<?php if ($user->isLoggedIn()) {
  die('You are already logged in.');
}

if (isset($_GET['link'])) { 

  if(!$user->isPasswordResetValid($_GET['link'])) {
    die('This password reset link has expired.');
  }

  ?>

<div class="page-header">
  <h1>Reset Password</h1>
</div>

<p class="lead">
  Enter a new password below.
</p>

<form class="form" method="POST" action="?action=changePassword&link=<?php echo $_GET['link']; ?>">
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
  </div>
  <div class="form-group">
    <label for="password-2">Confirm Password</label>
    <input type="password" class="form-control" id="password-2" placeholder="Confirm" name="password-2">
  </div>
  <button type="submit" class="btn btn-success">Submit</button>
</form>

<?php } else {

?>
<div class="page-header">
  <h1>Reset Password</h1>
</div>

<p class="lead">
  Enter your email address and we'll email you with a link to reset your password.
</p>

<form class="form" method="POST" action="?action=sendPasswordReset">
  <div class="form-group">
    <label for="email">Email Address</label>
    <input type="email" class="form-control" id="email" placeholder="Email Address" name="email">
  </div>
  <button type="submit" class="btn btn-success">Send</button>
</form>

<?php } ?>