<?php 

$badge = new badge();
$canhasbadge = $badge->canApplyForBadge($_SESSION['userid'],$_GET['badge']);
$roster = $badge->getBearers($_GET['badge']);
$badge = $badge->getBadge($_GET['badge']);
$team = new team();

?>
<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewTeams">Teams</a></li>
  <li>
  <?php echo "<a href='?action=viewTeam&team=$badge->teamid'>$badge->teamname</a>";?>
  </li>
  <li class="active">The '<?php echo $badge->name; ?>' badge</li>
</ol>


<?php
echo "<h1 style='text-align: center;'>".renderBadge($badge)."</h1>";
echo "<hr />";
echo "<p class='lead'>This is a <em>$badge->name</em> badge from team ";
echo teamLink($badge->teamname, $badge->teamid);
echo ". It says '$badge->description'.</p>";
echo "<h2>Badge Bearers</h2>";
echo tableheader(array('Name'));
foreach ($roster as $member) {

  if ($_SESSION['userid'] == $badge->lead || $user->isAdmin()) {

    if ('R' == $member->status) {
      $class = 'info';
      $button = " (requested) <a href='?action=approveBadge&badge=$badge->id&user=$member->user' class='btn btn-xs btn-success'>".icon('check')."</a>";
      $button.= " <a href='?action=revokeBadge&badge=$badge->id&user=$member->user' class='btn btn-xs btn-danger'>".icon('times')."</a>";
    } else {
      $class = '';
      $button = " <a href='?action=revokeBadge&badge=$badge->id&user=$member->user' class='btn btn-xs btn-danger'>".icon('times')."</a>";
    }
  } elseif ('R' == $member->status) {
    $class = 'info';
    $button = ' (pending)';
  } else {
    $class = '';
    $button = '';
  }
  echo tablecells(
    array(
      userLink($member->publicname, $member->user).$button
    ), $class
  );
}
echo tablefooter();

if ($_SESSION['userid'] == $badge->lead || $user->isAdmin()) { ?>
<hr />
<h2>Grant Badge</h2>
<form method="POST" action="<?php echo "?action=grantBadge&badge=$badge->id";?>" class="form">
<div class="row">
<div class="col-md-4">
<?php
  
  $grantlist = $team->getTeamMembers($badge->team);

  echo "<select name='user' class='form-control'>";
  foreach ($grantlist as $member) {
    echo "<option value='$member->id'>$member->publicname</option>";
  }
  echo "</select>"; ?>

</div>
<div class="col-md-4">
  <div class="checkbox">
    <label>
      <input type="checkbox"> Notify user
    </label>
  </div>
</div>
<div class="col-md-4">
  <button type="submit" class="btn btn-success btn-block">
    Grant Badge
  </button>
</div>
</div>
</form>

<?php } elseif(TRUE == $canhasbadge) {
  echo "<h2>Request Badge</h2>";
  echo "<a href='?action=requestBadge&badge=$badge->id' class='btn btn-block btn-success'>Request the <em>$badge->name</em> badge</a>";
} else {
  
} ?>