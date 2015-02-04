<?php 

class team {

  public function addTeam($name, $desc, $openjoin) {
    if(!$this->isUnique($name)) {
      $return[] = array(
        'msg'=>'This team already exists!',
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
    if(!is_bool($openjoin)) {
      $openjoin = 0;
    }
    $db = new database();
    $db->query("INSERT INTO tbl_team (name, description, openjoin) VALUES
      (:name, :description, :openjoin)");
    $db->bind(':name',$name);
    $db->bind(':description',$desc);
    $db->bind(':openjoin',$openjoin);
    $db->execute();
    $return[] = array(
      'msg'=>"Team $name has been created.",
      'level'=>1
    );
    return $return;
  }

  public function isUnique($name) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count
      FROM tbl_team WHERE name = :name");
    $db->bind(':name', $name);
    $db->execute();
    if (0 == $db->single()->count) {
      return true;
    } else {
      return false;
    }
  }

  public function listTeams() {
    $db = new database();
    $db->query("SELECT tbl_team.*,
      CASE WHEN tbl_userteams.user = NULL
      THEN 0
      ELSE (GROUP_CONCAT(tbl_userteams.user))
      END AS members,
      COUNT(v2_userteams.user) AS membercount,
      IF(lead.callsign IS NOT NULL, lead.callsign,
      IF(lead.burnername IS NOT NULL, lead.burnername,
      IF(CONCAT(lead.firstname,lead.lastname) IS NOT NULL, CONCAT_WS(' ',lead.firstname, lead.lastname),
      IF(lead.username IS NOT NULL, lead.username, 'Not Assigned')))) AS publicname
      FROM tbl_team
      LEFT JOIN tbl_userteams ON tbl_team.id = tbl_userteams.team
      LEFT JOIN tbl_user AS lead ON tbl_team.lead = lead.id
      GROUP BY tbl_team.name
      ORDER BY tbl_team.name ASC");
    $db->execute();
    return $db->resultSet();
  }

  public function viewTeam($team) {
    $db = new database();
    $db->query("SELECT tbl_team.*,
      CASE WHEN tbl_userteams.user = NULL
      THEN 0
      ELSE (GROUP_CONCAT(tbl_userteams.user))
      END AS members,
      IF(lead.callsign IS NOT NULL, lead.callsign,
      IF(lead.burnername IS NOT NULL, lead.burnername,
      IF(CONCAT(lead.firstname,lead.lastname) IS NOT NULL, CONCAT_WS(' ',lead.firstname, lead.lastname),
      IF(lead.username IS NOT NULL, lead.username, 'zzz')))) AS publicname
      FROM tbl_team
      LEFT JOIN tbl_userteams ON tbl_team.id = tbl_userteams.team
      LEFT JOIN tbl_user ON tbl_userteams.user = tbl_user.id
      LEFT JOIN tbl_user AS lead ON tbl_team.lead = lead.id
      WHERE tbl_team.id = :team
      GROUP BY tbl_team.name");
    $db->bind(':team',$team);
    $db->execute();
    return $db->single();
  }

  public function joinTeam($team) {
    $db = new database();
    $db->query("SELECT openjoin FROM tbl_team WHERE id = :team");
    $db->bind(':team',$team);
    $db->execute();
    if (FALSE == $db->single()->openjoin) {
      $user = new user();
      $db->query("INSERT INTO tbl_userteams (user, team, status) VALUES
        (:user, :team, 'A')");
      $db->bind(':user',$user->id);
      $db->bind(':team',$team);
      try {
        $db->execute();
      } catch (Exception $e) {
        $return[] = array(
          'msg'=>"You are already a member of this team.",
          'level'=>2
        );
        return $return;      
      }
      $return[] = array(
        'msg'=>"You have applied to this team.",
        'level'=>1
      );
      return $return;
    } else {
      $user = new user();
      $db->query("INSERT INTO tbl_userteams (user, team, status) VALUES
        (:user, :team, 'M')");
      $db->bind(':user',$user->id);
      $db->bind(':team',$team);
      try {
        $db->execute();
      } catch (Exception $e) {
        $return[] = array(
          'msg'=>"You are already a member of this team.",
          'level'=>2
        );
        return $return;      
      }
      $return[] = array(
        'msg'=>"You joined the team.",
        'level'=>1
      );
      return $return;
    }
  }

  public function isUserLead($team) {
    $db = new database();
    $db->query("SELECT lead FROM tbl_team
      WHERE id = :team");
    $db->bind(':team', $team);
    $db->execute();
    $lead = $db->single()->lead;
    $user = new user();
    if (!$user->isAdmin()) {
      if ($lead == $user->id) {
        return true;
      } else {
        return false;
      }
    } else {
      return true;
    }
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
    } elseif ('R' == $return->status){
        return FALSE;
    } else {
      return TRUE;
    }
  }

  public function approveMembership($team, $user){
    if (!$this->isUserLead($team)) {
      $return[] = array(
        'msg'=>"You must be a team lead to do this!",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    $db->query("UPDATE tbl_userteams SET status = 'M'
      WHERE tbl_userteams.team = :team
      AND tbl_userteams.user = :user");
    $db->bind(':team',$team);
    $db->bind(':user',$user);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong.".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Membership approved!",
      'level'=>1
    );
    return $return;     
  }

  public function bounceMember($team, $user, $denied=FALSE){
    if (!$this->isUserLead($team)) {
      $return[] = array(
        'msg'=>"You must be a team lead to do this!",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    $db->query("DELETE FROM tbl_userteams
      WHERE tbl_userteams.team = :team
      AND tbl_userteams.user = :user");
    $db->bind(':team',$team);
    $db->bind(':user',$user);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong.".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    if (FALSE === $denied) {
      $return[] = array(
        'msg'=>"Member removed from team!",
        'level'=>1
      );
      return $return;
    } else {
      $return[] = array(
        'msg'=>"Membership denied.",
        'level'=>1
      );
      return $return;      
    }
  }

  public function getTeamMembers($team) {
    $db = new database();
    $db->query("SELECT
      tbl_user.username,
      tbl_user.id,
      tbl_userteams.status,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, 'zzz')))) AS publicname
      FROM tbl_userteams
      LEFT JOIN tbl_user ON tbl_userteams.user = tbl_user.id
      WHERE tbl_userteams.team = :team");
    $db->bind(':team',$team);
    $db->execute();
    return $db->resultset();
  }

  public function isUserOnTeam($user,$team) {
    $db = new database();
    $db->query("SELECT * FROM tbl_userteams
      WHERE user = :user
      AND team = :team");
    $db->bind(':user',$user);
    $db->bind(':team',$team);
    $db->execute();
    return $db->single();
  }

  public function designateLead($team, $lead) {
    $db = new database();
    $db->query("UPDATE tbl_team SET lead = :lead WHERE tbl_team.id = :team");
    $db->bind(':team', $team);
    $db->bind(':lead',$lead);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to designate team lead.",
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"You have assigned a new team lead.",
      'level'=>1
    );
    return $return; 
  }

}