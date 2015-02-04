<?php
$team = new team();
var_dump($team->isUserOnTeam(1,1));
var_dump($team->isUserOnTeam(1,2));
var_dump($team->isUserOnTeam(1,3));

if ($team->isUserOnTeam(1,2)) {
  alert(array(
    'msg'=>"User is on team",
    'level'=>1
  ));
}

if (!$team->isUserOnTeam(1,1)) {
  alert(array(
    'msg'=>"User is not on team",
    'level'=>2
  ));
}

$badge = new badge();
var_dump($badge->canApplyForBadge(1,1));
var_dump($badge->canApplyForBadge(1,2));
var_dump($badge->canApplyForBadge(1,3));
var_dump($badge->userHasBadge(4,2));