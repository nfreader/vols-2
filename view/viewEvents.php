<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li class="active">Events</li>
</ol>

<div class='page-header'><h1>Upcoming events</h1></div>

<?php 
$events = new event();
$events->listEvents();


