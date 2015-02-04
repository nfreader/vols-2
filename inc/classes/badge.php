<?php

class badge {

  public function addNewBadge($team, $name, $description, $color, $color2, $icon) {
    $teaminfo = new team();
    if(!$teaminfo->isUserLead($team)) {
      $return[] = array(
        'msg'=>"You must be a team lead or admin to create a badge.",
        'level'=>2
      );
      return $return;
    }
    $name = validate($name);
    if ('' === $name) {
      $return[] = array(
        'msg'=>'Team name cannot be empty!',
        'level'=>2
      );
      return $return; 
    }
    $db = new database();
    $db->query("INSERT INTO tbl_badge 
      (name, team, icon, color, color2, description) VALUES
      (:name, :team, :icon, :color, :color2, :description);");
    $db->bind(':name',$name);
    $db->bind(':team',$team);
    $db->bind(':icon',$icon);
    $db->bind(':color',$color);
    $db->bind(':color2',$color2);
    $db->bind(':description',$description);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to create new badge.".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Badge $name created!",
      'level'=>1
    );
    return $return; 
  }

  public function getTeamBadges($team) {
    $db = new database();
    $db->query("SELECT * FROM tbl_badge WHERE team = :team");
    $db->bind(':team',$team);
    $db->execute();
    return $db->resultset();
  }

  public function getBadge($badge) {
    $db = new database();
    $db->query("SELECT tbl_badge.*,
      tbl_team.name AS teamname,
      tbl_team.id AS teamid,
      tbl_team.lead
      FROM tbl_badge
      LEFT JOIN tbl_team ON tbl_badge.team = tbl_team.id
      WHERE tbl_badge.id = :badge");
    $db->bind(':badge',$badge);
    $db->execute();
    return $db->single();
  }

  public function getBearers($badge) {
    $db = new database();
    $db->query("SELECT tbl_userbadges.*,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, 'zzz')))) AS publicname
      FROM tbl_userbadges
      LEFT JOIN tbl_user ON tbl_userbadges.user = tbl_user.id
      WHERE tbl_userbadges.badge = :badge");
    $db->bind(':badge',$badge);
    $db->execute();
    return $db->resultset();
  }

  public function getUnbadgedTeamMembers($badge) {
    
  }

  public function userHasBadge($user, $badge) {
    $db = new database();
    $db->query("SELECT * FROM tbl_userbadges
      WHERE user = :user
      AND badge = :badge");
    $db->bind(':user',$user);
    $db->bind(':badge',$badge);
    $db->execute();
    $return = $db->single();
    if (FALSE === $return) {
      return FALSE;
    } elseif ('G' == $return->status){
        return TRUE;
    } else {
      return FALSE;
    }
  }

  public function canApplyForBadge($user, $badge){
    $team = new team();
    if(!$team->isUserOnTeam($user, $this->getBadge($badge)->teamid)) {
      return false;
    }
    if(!$this->userHasBadge($user, $badge)) {
      return true;
    }
  }

  public function requestBadge($user, $badge) {
    if(!$this->canApplyForBadge($user, $badge)) {
      $return[] = array(
        'msg'=>"You cannot apply for this badge. You must be on this team, or you have already applied for this badge.",
        'level'=>2
      );
    }
    $db = new database();
    $db->query("INSERT INTO tbl_userbadges (user, badge, status)
      VALUES (:user, :badge, 'R')");
    $db->bind(':user',$user);
    $db->bind(':badge',$badge);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to request badge. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Badge requsted!",
      'level'=>1
    );
    return $return;
  }

  public function grantBadge($user, $badge) {
    $team = new team();
    if(!$team->isUserLead($this->getBadge($badge)->teamid)) {
      $return[] = array(
        'msg'=>"You must be a team lead or admin to grant a badge.",
        'level'=>2
      );
      return $return;
    }
    if(!$this->canApplyForBadge($user, $badge)) {
      $return[] = array(
        'msg'=>"Unable to grant badge. User already has this badge or user is not on team.",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    $db->query("INSERT INTO tbl_userbadges (user, badge, status)
      VALUES (:user, :badge, 'G')
      ON DUPLICATE KEY UPDATE status = 'G'");
    $db->bind(':user',$user);
    $db->bind(':badge',$badge);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to grant badge. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Badge granted!",
      'level'=>1
    );
    return $return;
  }

  public function revokeBadge($user, $badge) {
    $team = new team();
    if(!$team->isUserLead($this->getBadge($badge)->teamid)) {
      $return[] = array(
        'msg'=>"You must be a team lead or admin to revoke a badge.",
        'level'=>2
      );
      return $return;
    }
    if(!$this->userHasBadge($user, $badge)) {
      $return[] = array(
        'msg'=>"Unable to revoke badge.",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    $db->query("DELETE FROM tbl_userbadges WHERE user = :user 
      AND badge = :badge");
    $db->bind(':user',$user);
    $db->bind(':badge',$badge);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to revoke badge. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Badge revoked.",
      'level'=>1
    );
    return $return;
  }
}






