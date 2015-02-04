<?php 
$shift = new shift();
$shift = $shift->getShift($_GET['shift']);
$event = new event();
$event = $event->getEvent($shift->event);
?>

<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewEvents">Events</a></li>
  <li><a href="?action=viewEvent&event=<?php echo $event->id;?>">
  <?php echo $event->name;?></a></li>
  <li class="active">Manage shift</li>
</ol>

<?php

echo "<div class='page-header'><h1>Viewing a ".teamlink($shift->teamname,$shift->teamid)." shift for ".eventlink($event->name,$event->id)."</h1>";

?>

<div class="row text-center">
  <h3>
    <div class="col-md-3">
      <span class="label label-primary">
        <?php echo timestamp($shift->start); ?>
      </span>
    </div>
    <div class="col-md-1">
      <span class="label label-primary">
        <i class="fa fa-arrow-right"></i>
      </span>
    </div>
    <div class="col-md-3">
      <span class="label label-primary">
        <?php echo singular($shift->duration,'hour','hours'); ?>
      </span>
    </div>
    <div class="col-md-1">
      <span class="label label-primary">
        <i class="fa fa-arrow-right"></i>
      </span>
    </div>
    <div class="col-md-3">
      <span class="label label-primary">
        <?php echo timestamp($shift->end); ?>
      </span>
    </div>
  </h3>
</div>
