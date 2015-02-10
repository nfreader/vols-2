<ol class="breadcrumb">
  <li><a href="index.php">Home</a></li>
  <li class="active">Teams</li>
</ol>

<div class="page-header">
  <h1><?php echo APP_NAME." teams";?></h1>
</div>

<?php

$team = new team();

$teams = $team->listTeams();
echo tableHeader(array('Name','Description','Members','Lead','Join'),'sort');
foreach ($teams as $team){
  $name = "<a href='?action=viewTeam&team=$team->id'>$team->name</a>";

  $onteam = FALSE;
  $membership = explode(',',$team->members);
  if (in_array($_SESSION['userid'],$membership)){
    $onteam = TRUE;
  }
  if (TRUE == $onteam) {
    $button = '';
  } elseif (FALSE == $team->openjoin) {
    $button = "<a href='?action=joinTeam&team=$team->id' class='btn btn-xs btn-success'>";
    $button.="Apply for team</a>";   
  } else {
    $button = "<a href='?action=joinTeam&team=$team->id' class='btn btn-xs btn-success'>";
    $button.="Join team</a>";    
  }

  if (NULL == $team->lead) {
    $userlink = 'No one';
  } else {
    $userlink = userLink($team->publicname,$team->lead);
  }

  echo tableCells(array($name,
    $team->description,
    $team->membercount,
    $userlink,
    $button));
}
echo tableFooter();
