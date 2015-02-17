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

<?php
echo "<div class='page-header'><h1>$event->name <small>";
echo singular($event->duration,'Hour','Hours')."</small></h1></div>";
echo "<div class='row'><div class='col-md-6'><strong>Begins</strong>";
echo "<h1>".timestamp($event->start)."</h1></div><div class='col-md-6'>";
echo "<strong>Ends</strong>";
echo "<h1>".timestamp($event->end)."</h1></div></div>";
echo "<p class='lead'>$event->description</p>";

echo "<hr />";

echo "<h2>Shifts <small>Shifts in blue are scheduled to begin before or end after their parent event.</small></h2>";

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
    $count.=" <a href='?action=deleteShift&shift=$shift->id&event=$shift->event&verify=1' class='btn btn-danger btn-xs' title='Delete Shift'>Delete Shift ".icon('remove')."</a>";
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