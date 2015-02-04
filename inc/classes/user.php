<?php

class user {

  public $id;
  public $status;

  public function __construct() {
    if(isset($_SESSION['userid'])) {
      $this->id = $_SESSION['userid'];
      $this->status = $_SESSION['status'];
    }
    else {
      return "No session detected";
    }
  }

  public function register($username, $email, $password) {
    if(!$this->isUnique($username, $email)) {
      $return[] = array(
        'msg'=>'Username or email address already in use',
        'level'=>2
      );
      return $return;
    }

    $username = validate($username);
    if ('' === $username) {
      $return[] = array(
        'msg'=>'Username cannot be empty!',
        'level'=>2
      );
      return $return; 
    }
    $email = validate($email);
    if ('' === $email) {
      $return[] = array(
        'msg'=>'Email cannot be empty!',
        'level'=>2
      );
      return $return;  
    }

    if ('' === trim($password)) {
      $return[] = array(
        'msg'=>'Password cannot be empty!',
        'level'=>2
      );
      return $return;
    } 
    $salt = getSalt();
    $db = new database();
    $db->query("INSERT into tbl_user 
      (username, email, password, salt, timestamp) VALUES
      (:username, :email, :password, :salt, NOW())");
    $db->bind(':username', $username);
    $db->bind(':password', hash('sha512', $salt . $password));
    $db->bind(':email', $email);
    $db->bind(':salt',$salt);
    $db->execute();
    $return[] = array(
      'msg'=>"You have successfully registered as $username! Please wait while an administrator activates your account.",
      'level'=>1
    );
    if(1 == $db->countRows('tbl_user')) {
      $db->query("SELECT id FROM tbl_user WHERE username = :username");
      $db->bind(':username',$username);
      $db->execute();
      $id = $db->single()->id;
      $db->query("UPDATE tbl_user SET status = 1, rank = 'A'
        WHERE id = :id");
      $db->bind(':id',$id);
      $db->execute();
      $return[] = array(
        'msg'=>"Initial user detected. You have been promoted to administrator and activated. Please log in now.",
        'level'=>1
      );
    }
    return $return;
  }

  public function isUnique($username, $email) {
    $db = new database();
    $db->query("SELECT COUNT(*) AS count
      FROM tbl_user WHERE username = :username OR email = :email");
    $db->bind(':username', $username);
    $db->bind(':email', $email);
    $db->execute();
    if (0 == $db->single()->count) {
      return true;
    } else {
      return false;
    }
  }

  public function isLoggedIn() {
    if ((isset($_SESSION['username'])) && (isset($_SESSION['userid'])) && $_SESSION['status'] == 1) {
      return true;
    }
  }

  public function logIn($username, $password) {
    $db = new database();
    $db->query("SELECT username, salt FROM tbl_user WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $check = $db->single();
    if ($check == array()) {
      return "Username or password invalid.";
    } else {
      $db->query("SELECT id, username, email, rank, status,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, 'zzz')))) AS publicname
      FROM tbl_user
      WHERE password = :password AND username = :username");
      $db->bind(':password', hash('sha512', $check->salt . $password));
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();
      if (array() == $login) {
        $return[] = array(
          'msg'=>"Username or password invalid",
          'level'=>2
          );
        return $return;
      } else {
        $_SESSION['username'] = $login->username;
        $_SESSION['userid'] = $login->id;
        $this->id = $login->id;
        $_SESSION['rank'] = $login->rank;
        $_SESSION['status'] = $login->status;
        if($this->isAdmin()){
          $_SESSION['sudo_mode'] = false;
        }
        $_SESSION['publicname'] = $login->publicname;
        if ($login->status == 0) {
          $return[] = array(
            'msg'=>"You are now logged in as $login->username. Your account is awaiting activation.",
            'level'=>1
          );
        } else {
          $return[] = array(
            'msg'=>"You are now logged in as $login->username.",
            'level'=>1
          );
        }
        return $return;
      }
    }
  }

  public function logOut(){
    $_SESSION = '';
    session_destroy();
    $return[] = array(
      'msg'=>'You have been logged out.',
      'level'=>1
    );
    return $return;
  }

  public function isAdmin() {
    $db = new database();
    $db->query("SELECT rank FROM tbl_user WHERE tbl_user.id = :id");
    $db->bind(':id',$this->id);
    if ($db->single()->rank === 'A') {
      return true;
    }
  }

  public function getTeamMemberships(){
    $db = new database();
    $db->query("SELECT
        tbl_team.id,
        tbl_team.name
        FROM tbl_userteams
        LEFT JOIN tbl_team ON tbl_userteams.team = tbl_team.id
        WHERE tbl_userteams.user = :user");
    $db->bind(':user',$this->id);
    $db->execute();
    return $db->resultset();
  }

}