<?php 
$shiftclass = new shift();
$shift = $shiftclass->getShift($_GET['shift']);
$event = new event();
$event = $event->getEvent($shift->event);
$badge = new badge();
$badges = $badge->getTeamBadges($shift->teamid);
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
      <span class="label <?php echo $shift->startsbefore==1 ? 'label-primary':'label-success'; ?>">
        <?php echo timestamp($shift->start); ?>
      </span>
    </div>
    <div class="col-md-1">
      <span class="label <?php echo $shift->startsbefore==1 ? 'label-primary':'label-success'; ?>">
        <i class="fa fa-arrow-right"></i>
      </span>
    </div>
    <div class="col-md-3">
      <span class="label label-success">
        <?php echo singular($shift->duration,'hour','hours'); ?>
      </span>
    </div>
    <div class="col-md-1">
      <span class="label <?php echo $shift->endsafter==1 ? 'label-primary':'label-success'; ?>">
        <i class="fa fa-arrow-right"></i>
      </span>
    </div>
    <div class="col-md-3">
      <span class="label <?php echo $shift->endsafter==1 ? 'label-primary':'label-success'; ?>">
        <?php echo timestamp($shift->end); ?>
      </span>
    </div>
  </h3>
</div>
<hr />
<?php 


echo tableHeader(array('Required Badge','Status'));

$slots = new slot();
$slots = $slots->getSlots($shift->id);

foreach ($slots as $slot) {
  $badge = (object) array(
    'id'=>$slot->badgeid,
    'name'=>$slot->badgename,
    'color'=>$slot->badgecolor,
    'color2'=>$slot->badgecolor2,
    'description'=>$slot->badgedesc,
    'icon'=>$slot->badgeicon
  );
  $badge = renderBadge($badge);

  if (TRUE == $slot->openjoin) {
    $badge = "None required";
  }

  $claim = '';

  if (FALSE == $slot->claimed){
    $claim.= "<a href='?action=claimSlot&slot=$slot->id&shift=$shift->id' class='btn btn-success btn-xs'>Claim Slot ".icon('thumbs-up')."</a>";
  } else {
    $claim.= "Claimed by ".userLink($slot->publicname,$slot->user);
    if($shiftclass->userCanEdit($event->id)){
      $claim.= " <a href='?action=reopenSlot&slot=$slot->id&user=$slot->user&shift=$shift->id' class='btn btn-danger btn-xs' title='Remove user from slot'>Remove $slot->publicname ".icon('remove')."</a>";
    }
    if($slot->user == $user->id) {
      $claim.= " <a href='?action=cancelSlot&slot=$slot->id&shift=$shift->id' class='btn btn-danger btn-xs' title='Cancel Slot'>Cancel slot ".icon('remove')."</a>";
    }
  }

  if($shiftclass->userCanEdit($event->id)){
    $claim.= " <a href='?action=deleteSlot&slot=$slot->id&shift=$shift->id' class='btn btn-danger btn-xs' title='Delete Slot'>Delete slot ".icon('remove')."</a>";
  }

  echo tablecells(array($badge,$claim));
}

if($shiftclass->userCanEdit($event->id)){
?>

<form method="POST" action="<?php echo "?action=addSlot&shift=$shift->id";?>">
  <tr>
    <td colspan="5"><h3 class="text-center">Add new slot</h3></td>
  </tr>
  <tr>
    <td>
      <select class="form-control" name="badge">
      <?php
      echo "<option disabled selected>Select a badge requirement</option>";
      echo "<option value=''>None required</option>";
      foreach ($badges as $badge) {
        echo "<option value='$badge->id'>$badge->name</option>";
      }
      ?>
      </select>
    </td>
    <td>
      <button type="submit" class="btn btn-block btn-primary btn-sm">
        Create Slot
      </button>
    </td>
  </tr>
</form>

<?php
}
echo tableFooter();
