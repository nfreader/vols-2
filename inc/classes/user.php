<?php

class user {

  public $id;
  public $status;
  public $publicname;

  public function __construct() {
    if(isset($_SESSION['userid'])) {
      $this->id = $_SESSION['userid'];
      $this->status = $_SESSION['status'];
      $this->publicname = $_SESSION['publicname'];
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
    $db->bind(':password', password_hash($password));
    $db->bind(':email', $email);
    $db->bind(':salt', $salt);
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

  public function login($username, $password) {
    $db = new database();
    $db->query("SELECT password FROM tbl_user
      WHERE username = :username");
    $db->bind(':username',$username);
    $db->execute();
    $user = $db->single();
    if(!password_verify($password, $user->password)) {
      $return[] = array(
        'msg'=>"Incorrect password.",
        'level'=>2
      );
      return $return;
    } else {
      $db->query("SELECT id, username, email, rank, status,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, 'zzz')))) AS publicname
      FROM tbl_user
      WHERE username = :username");
      $db->bind(':username', $username);
      $db->execute();
      $login = $db->single();

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

  public function logOut(){
    $_SESSION = '';
    session_destroy();
    $return[] = array(
      'msg'=>'You have been logged out.',
      'level'=>1
    );
    return $return;
  }

  public function issuePasswordReset($email) {
    $email = $this->getUserByEmail($email);
    if(!$email) {
      $return[] = array(
        'msg'=>"Unable to find the specified user.",
        'level'=>2
      );
      return $return;
    }

    $link = $this->generatePasswordReset($email->id);
    if(!$link) {
      $return[] = array(
        'msg'=>"Unable to generate a new password reset link.",
        'level'=>2
      );
      return $return;
    }
    $to = $email->email;
    $subject = APP_NAME." password reset";
    $message = "<strong>$subject</strong><br>--------<br>";
    $message.= "If you need to reset your passoword for ".APP_NAME." ";
    $message.= "please click the link below and follow the instructions. ";
    $message.= "If you did not request a password reset, please disregard ";
    $message.= "this message.<br>--------<br>";
    $message.= "<a href='".APP_URL."?action=resetPassword&link=$link'>Reset Password</a> <em>This link will expire in 15 minutes</em>";

    $app = new app();
    try{
      $app->systemMail($email->email,$subject,$message);
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Unable to send password reset. ".$e->getMessage(),
        'level'=>2
      );
      return $return;
    }
    $return[] = array(
      'msg'=>"A link to reset your password has been sent.",
      'level'=>1
    );
    return $return; 
  }

  public function getUserByEmail($email) {
    $db = new database();
    $db->query("SELECT id, username, email FROM tbl_user WHERE email = :email");
    $db->bind(':email',$email);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Unable to find email address.".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    return $db->single();
  }

  public function generatePasswordReset($user) {
    $link = generatePasswordResetLink();
    $db = new database();
    $db->query("INSERT INTO tbl_passwordresets (user, link, timestamp)
      VALUES (:user, :link, NOW())");
    $db->bind(':user',$user);
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false; 
    }
    return $link;
  }

  public function isPasswordResetValid($link) {
    $db = new database();
    $db->query("SELECT *,
      CASE WHEN (tbl_passwordresets.timestamp >= NOW() - INTERVAL 15 MINUTE)
      THEN 1
      ELSE 0
      END AS valid
      FROM tbl_passwordresets
      WHERE tbl_passwordresets.link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false; 
    }
    $link = $db->single();
    if(FALSE == $link->valid) {
      $this->deletePasswordResetLink($link);
      return false;
    }
    return true;
  }

  public function deletePasswordResetLink($link) {
    $db = new database();
    $db->query("DELETE FROM tbl_passwordresets WHERE link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      return false; 
    }
    return true;
  }

  public function resetPassword($link, $password, $password2) {
    if ($password != $password2) {
      $return[] = array(
        'msg'=>"Passwords must match!",
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
    if (!$this->isPasswordResetValid($link)){
      $return[] = array(
        'msg'=>"This link has expired.",
        'level'=>2
      );
      return $return;
    }

    $db = new database();
    $db->query("SELECT * FROM tbl_passwordresets WHERE link = :link");
    $db->bind(':link',$link);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Unable to find password reset. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $user = $db->single();
    $this->deletePasswordResetLink($user->link);
    $db->query("UPDATE tbl_user SET password = :password
      WHERE id = :user");
    $db->bind(':password',password_hash($password,PASSWORD_DEFAULT));
    $db->bind(':user',$user->user);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Unable to reset password. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Your password has been reset. Please log in.",
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