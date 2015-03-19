<?php 
$event = new event();
$event = $event->getEvent($_GET['event']);
$shiftclass = new shift();
$shifts = $shiftclass->getShifts($event->id);
$canedit = $shiftclass->userCanEdit($event->id);
?>

<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewEvents">Events</a></li>
  <li class="active"><?php echo $event->name;?></li>
</ol>

<div class="page-header">
  <h1><?php echo $event->name;?>
    <small><?php echo singular($event->duration,'Hour','Hours'); ?>
    <?php if ($user->isAdmin()){
        echo modal('Are you sure you want to cancel this event?',
          "cancelevent$event->id",
          "Cancel $event->name",
          "?action=cancelEvent&event=$event->id");
        $btn = " <a href='#' class='btn btn-danger'";
        $btn.= "data-toggle='modal'";
        $btn.= "data-target='#cancelevent$event->id'>";
        $btn.= "Cancel Event ".icon('remove')."</a>";
        echo $btn;
      } ?>
    </small>
  </h1>
</div>

<div class="row">
  <div class="col-md-6">
    <strong>Starts</strong>
    <h1><?php echo timestamp($event->start); ?></h1>
  </div>

  <div class="col-md-6">
    <strong>Ends</strong>
    <h1><?php echo timestamp($event->end); ?></h1>
  </div>
</div>

<p class="lead"><?php echo $event->description;?></p>

<hr />

<h2>Shifts <small>Shifts in blue are scheduled to begin before or end after their parent event.</small></h2>

<?php
echo tableHeader(array('Team',
  'Start',
  'End',
  'Duration',
  'Slots (total/filled)'));
$i = 0;
$totalslots = 0;
$filledslots = 0;
foreach ($shifts as $shift) {
  if (1 == $shift->startsbefore || 1 == $shift->endsafter) {
    $class='info';
  } else {
    $class='';
  }
  $count = "($shift->slots/$shift->filled) <a href='?action=manageShift&shift=$shift->id'>View</a>";
  if ($canedit){
    echo modal("Are you sure you want to delete this shift?",
      "delete$shift->id",
      "Confirm shift deletion",
      "?action=deleteShift&shift=$shift->id&event=$shift->event");
    $count.= " <a href='#' class='btn btn-xs btn-danger' data-toggle='modal'";
    $count.= "data-target='#delete$shift->id'>";
    $count.= "Delete Shift ".icon('remove')."</a>";
    
  }
  echo tableCells(array(
    teamLink($shift->teamname, $shift->teamid),
    timestamp($shift->start), 
    timestamp($shift->end),
    singular($shift->duration,'hour','hours'),
    "$count"),
  $class);
  $i++;
  $totalslots = $totalslots + $shift->slots;
  $filledslots = $filledslots + $shift->filled;
}

if ($canedit) {
?>

<form method="POST" action="<?php echo "?action=addShift&event=$event->id";?>">
  <tr>
    <td colspan="5"><h3 class="text-center">Add new shift</h3></td>
  </tr>
  <tr>
    <td>
      <select class="form-control" name="team">
      <?php
      $team = new team();
      $teams = $team->listTeams();
      echo "<option disabled selected>Select a team</option>";
      foreach ($teams as $team) {
        echo "<option value='$team->id'>$team->name</option>";
      }
      ?>
      </select>
    </td>
    <td style="position: relative;">
      <input class="form-control" name="shift-start" id="shift-start" placeholder="yyyy-mm-dd 00:00:00" data-event-start="<?php echo $event->start;?>">
    </td>
    <td style="position: relative;">
      <input class="form-control" name="shift-end" id="shift-end"  placeholder="yyyy-mm-dd 00:00:00">
    </td>
    <td>
    </td>
    <td>
      <button type="submit" class="btn btn-block btn-primary btn-sm">
        Create Shift
      </button>
    </td>
  </tr>
</form>

<?php
}

echo tableFooter();