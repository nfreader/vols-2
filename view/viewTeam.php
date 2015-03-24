<?php
$team = new team();
$teaminfo = $team->viewTeam($_GET['team']);
?>

<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li><a href="?action=viewTeams">Teams</a></li>
  <li class="active"><?php echo $teaminfo->name;?></li>
</ol>

<div class="page-header">
  <h1><?php echo "<small>Team</small> $teaminfo->name";?>
  <?php
  echo "<small>";
  if ('' == $teaminfo->lead) {
    echo "Lead by no one (yet)";
  } else {
    echo "Led by ".userLink($teaminfo->publicname,$teaminfo->lead);
  }
  if ($user->isAdmin()) {
    echo " (<a href='?action=designateLead&team=$teaminfo->id'>Designate</a>)";
  }
  echo "</small>";

  $onteam = FALSE;
  $membership = explode(',',$teaminfo->members);
  if (in_array($_SESSION['userid'],$membership)){
    $onteam = TRUE;
  }
  if (TRUE == $onteam) {
    $button = '';
  } elseif (FALSE == $teaminfo->openjoin) {
    $button = "<a href='?action=joinTeam&team=$teaminfo->id' class='btn btn-success pull-right'>";
    $button.="Apply for team</a>";   
  } else {
    $button = "<a href='?action=joinTeam&team=$teaminfo->id' class='btn btn-success pull-right'>";
    $button.="Join team</a>";    
  }

  echo $button;

  ?>

  </h1>
</div>
  <p class="lead"><?php echo $teaminfo->description; ?></p>
  <h2>Team Members</h2>
<?php 

$roster = $team->getTeamMembers($teaminfo->id);

echo tableHeader(array('Name'));
foreach ($roster as $member) {
  if ('A' === $member->status) {
    $class = 'info';
    $pending = " (pending)";
    if ($user->isAdmin() || $_SESSION['userid'] == $teaminfo->lead) {
      $pending.= " <a href='?action=approveMembership&team=$teaminfo->id&user=$member->id' class='btn btn-success btn-xs'>".icon('check')."</a>";
      $pending.= " <a href='?action=denyMembership&team=$teaminfo->id&user=$member->id' class='btn btn-danger btn-xs'>".icon('times')."</a>";
    }
  } else {
    if ($user->isAdmin() || 
        ($_SESSION['userid'] == $teaminfo->lead && $member->id != $teaminfo->lead)) {
        $pending = " <a href='?action=bounceMember&team=$teaminfo->id&user=$member->id' class='btn btn-danger btn-xs'>".icon('times')."</a>";
        $class = '';
      } elseif ($member->id == $teaminfo->lead) {
        $pending = '';
        $class = 'success';
      } elseif ('A' == $_SESSION['rank']) {
        $pending = '';
        $class = 'info';
      } else {
        $pending = '';
        $class = '';
      }
    //if ($user->isAdmin() || ($member->id != $teaminfo->lead && $_SESSION['//userid'] == $teaminfo->lead)) {
    //  $pending = " <a href='?action=bounceMember&team=$teaminfo->id&user=$//member->id' class='btn btn-danger btn-xs'>".icon('times')."</a>";
    //  $class = '';
    //} else {
    //  $class = '';
    //  $pending = '';
    //}
  }

  echo tableCells(array(userLink($member->publicname,$member->id).$pending),$class);
}
echo tableFooter();

echo "<h2>Team Badges ";
if ($team->isUserLead($teaminfo->id)) {
echo "<a href='?action=addNewBadge&team=$teaminfo->id' class='btn btn-success'>".icon('plus')."</a>";
}
echo "</h2>";

$badges = new badge();
$badges = $badges->getTeamBadges($teaminfo->id);

foreach ($badges as $badge) {
  echo renderBadge($badge);
}

?>