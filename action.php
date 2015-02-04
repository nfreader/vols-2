<?php

$action = $_GET['action'];
$data = $_POST;

switch($action) {
  default:
    $msg[] = array(
      'level'=>0,
      'msg'=>"Unknown action: $action"
    );
    $include = 'home';
    break;

  case 'test':
    $include = 'test';
    break;

  case 'register':
    $msg = $user->register($data['username'],$data['email'],$data['password']);
    $include = 'home';
    break;

  case 'login':
    if(!empty($data)) {
      $msg = $user->login($data['username'],$data['password']);
    }
    $include = 'home';
    break;

  case 'logout':
    $msg = $user->logout();
    $include = 'guest';
    break;

//TEAM ACTIONS

  case 'viewTeams':
    $include = 'viewTeams';
    break;

  case 'viewTeam':
    $include = 'viewTeam';
    break;

  case 'designateLead':
    if(!empty($data)) {
      $team = new team();
      $msg = $team->designateLead($_GET['team'],$data['lead']);
    }
    $include = 'addTeamLead';
    break;

  case 'addTeam':
    if(!empty($data)) {
      $team = new team();
      $msg = $team->addTeam($data['name'],$data['desc'],$data['openjoin']);
    }
    $include = 'addTeam';
    break;

  case 'joinTeam':
    $team = new team();
    $msg = $team->joinTeam($_GET['team']);
    $include = 'viewTeam';
    break;

  case 'approveMembership':
    $team = new team();
    $msg = $team->approveMembership($_GET['team'], $_GET['user']);
    $include = 'viewTeam';
    break;

  case 'denyMembership':
    $team = new team();
    $msg = $team->bounceMember($_GET['team'], $_GET['user'], TRUE);
    $include = 'viewTeam';
    break;

  case 'bounceMember':
    $team = new team();
    $msg = $team->bounceMember($_GET['team'], $_GET['user']);
    $include = 'viewTeam';
    break;

//EVENT ACTIONS

  case 'addEvent':
    if(!empty($data)) {
      $event = new event();
      $msg = $event->addEvent(
        $data['event-name'],
        $data['event-start'],
        $data['event-end'],
        $data['event-desc'],
        $data['event-location']);
    }
    $include = 'addEvent';
    break;

  case 'viewEvents':
    $include = 'viewEvents';
    break;

  case 'viewEvent':
    $include = 'viewEvent';
    break;

//SHIFT ACTIONS

  case 'addShift':
    $shift = new shift;
    $msg = $shift->addShift($_GET['event'],$data['team'],$data['shift-start'],$data['shift-end']);
    $include = 'viewEvent';
    break;

  case 'manageShift':
    $include = 'viewShift';
    break;

//SLOT ACTIONS


//BADGE ACTIONS

  case 'addNewBadge':
     if(!empty($data)) {
      $badge = new badge();
      $msg = $badge->addNewBadge(
        $_GET['team'],
        $data['name'],
        $data['desc'],
        $data['color'],
        $data['color2'],
        $data['icon']);
    }
    $include = 'addNewBadge';
    break;

    case 'requestBadge':
      $badge = new badge();
      $msg = $badge->requestBadge($_SESSION['userid'], $_GET['badge']);
      $include = 'viewBadge';
      break;

    case 'grantBadge':
      $badge = new badge();
      $msg = $badge->grantBadge($data['user'],$_GET['badge']);
      $include = 'viewBadge';
      break;

    case 'approveBadge':
      $badge = new badge();
      $msg = $badge->grantBadge($_GET['user'],$_GET['badge']);
      $include = 'viewBadge';
      break;

    case 'revokeBadge':
      $badge = new badge();
      $msg = $badge->revokeBadge($_GET['user'],$_GET['badge']);
      $include = 'viewBadge';
      break;

    case 'viewBadge':
      $include = 'viewBadge';
      break;

}
