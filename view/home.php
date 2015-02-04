<?php if (!$user->isLoggedIn()) {
  include 'guest.php';
} else { ?>

<h1>Home</h1>

<div class="row">
  <div class="col-md-6">
    [Event Listing]
  </div>
  <div class="col-md-6">
    [Upcoming shifts]
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    [Open Qualified Shifts]
  </div>
  <div class="col-md-6">
    []
  </div>
</div>

<?php } ?>