<?php if (!$user->isLoggedIn()) {
  include 'guest.php';
} elseif(!$user->isAdmin()) {
  include 'home.php';
} else { ?>

<div class="page-header">
  <h1>Add New Event</h1>
</div>

<form action="?action=addEvent" method="POST">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="event-name">Event</label>
        <input type="text" class="form-control input-lg" id="event-name" name="event-name" placeholder="Name">
      </div>
      <div class="form-group">
        <label for="event-name">Event Start</label>
        <input type="text" class="form-control input-lg" id="event-start" name="event-start" placeholder="dd/mm/yyyy">
      </div>
      <div class="form-group">
        <label for="event-name">Event End</label>
        <input type="text" class="form-control input-lg" id="event-end" name="event-end" placeholder="dd/mm/yyyy">
      </div>
      <p>Yes, your event could start at 'fish' and end at 'bees' but all that's going to do is make me very disappointed in you so don't do it.</p>
    </div>
  
    <div class="col-md-6">
      <div class="form-group">
        <label for="event-desc">Description</label>
        <textarea class="form-control" name="event-desc" rows="10"></textarea>
      </div>
      <div class="form-group">
        <label for="event-name">Event Location</label>
        <input type="text" class="form-control input-lg" id="event-location" name="event-location" placeholder="123 Fake St. Anytown, XY 12345">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <button type="submit" class="btn btn-block btn-primary btn-lg">
        Create Event
      </button>      
    </div>
  </div>
</form>

<?php } ?>