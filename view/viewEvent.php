<?php 
$event = new event();
$event = $event->getEvent($_GET['event']);
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

$shift = new shift();
if ($shift->userCanEdit($event->id)) {
$shifts = $shift->getShifts($event->id);
echo tableHeader(array('Team','Start','End','Duration','Slots'));
foreach ($shifts as $shift) {
  if ($shift->end <= $event->start || $shift->start >= $event->end) {
    $class='info';
  } else {
    $class='';
  }
  echo tableCells(array(
    teamLink($shift->teamname, $shift->teamid),
    timestamp($shift->start), 
    timestamp($shift->end),
    singular($shift->duration,'hour','hours'),
    "<a href='?action=manageShift&shift=$shift->id'>View</a>"),$class);
}
?>

<form method="POST" action="<?php echo "?action=addShift&event=$event->id";?>">
  <tr>
    <td colspan="5"><h3 class="center-block">Add new shift</h3></td>
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


<?php
echo tableFooter();
}