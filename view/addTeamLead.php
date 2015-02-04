<?php
  $team = new team();
  $teaminfo = $team->viewTeam($_GET['team']);
?>

<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewTeams">Teams</a></li>
  <li>
  <?php echo "<a href='?action=viewTeam&team=$teaminfo->id'>$teaminfo->name</a>";?>
  </li>
  <li class="active">Designate team lead</li>
</ol>

<div class="page-header">
  <h1>
    <small>Team</small>
    <?php echo $teaminfo->name; ?>
    <small>lead manager</small>
  </h1>
</div>

<?php if ('' == $teaminfo->lead) {
  alert(array('msg'=>'No team lead assigned! Pick one below','level'=>2));
} else {
  alert(array('msg'=>"Currently lead by $teaminfo->publicname",'level'=>0));
}
?>
<form method="POST" action="<?php echo "?action=designateLead&team=$teaminfo->id";?>" class="form">
<div class="row">
<div class="col-md-4">
<?php
  $roster = $team->getTeamMembers($teaminfo->id);
  echo "<select name='lead' class='form-control'>";
  foreach ($roster as $member) {
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
    Designate Lead
  </button>
</div>
</div>
</form>