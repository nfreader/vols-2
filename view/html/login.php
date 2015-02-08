<h2>Login</h2>

<form method="POST" action="?action=login">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" placeholder="Username" name="username">
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" placeholder="Password" name="password">
  </div>
  <button type="submit" class="btn btn-success">Login</button> <a href="?action=forgotPassword" class="btn btn-link pull-right">Forgot your password?</a>
</form>