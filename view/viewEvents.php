
<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li class="active">Events</li>
</ol>

<div class='page-header'><h1>Upcoming events</h1></div>

<?php 
$events = new event();
$events = $events->listEvents();

echo tableHeader(array('Name','Start','End','Duration','Location'),'sort');

foreach ($events as $event) {
  echo tableCells(array(
    "<a href='?action=viewEvent&event=$event->id'>$event->name</a>", 
    timestamp($event->start), 
    timestamp($event->end), 
    singular($event->duration,'Hour','Hours'), 
    icon('map-marker')." <a href='https://www.google.com/maps/place/".urlencode($event->location)."' target='_blank'>$event->location</a>"
  ));
}

echo tableFooter();
