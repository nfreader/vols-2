<?php

class slot {

  public function addSlot($badge=NULL,$shift) {
    $shiftdata = new shift();
    if (!$shiftdata->userCanEdit($shift)) {
      $return[] = array(
        'msg'=>"You must be a team lead or admin to edit slots.",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    $db->query("INSERT INTO tbl_slot (badge, shift)
      VALUES (:badge, :shift)");
    $db->bind(':badge',$badge);
    $db->bind(':shift',$shift);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to create slot. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Slot created!",
      'level'=>1
    );
    return $return;
  }

  public function getSlots($shift) {
    $db = new database();
    $db->query("SELECT tbl_slot.id,
      tbl_slot.shift,
      tbl_slot.badge,
      tbl_userslots.user,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, '')))) AS publicname,
      CASE WHEN (tbl_slot.badge IS NULL || tbl_slot.badge = 0)
      THEN 1
      ELSE 0
      END AS openjoin,
      CASE WHEN (tbl_userslots.user IS NULL)
      THEN 0
      ELSE 1
      END AS claimed,
      tbl_badge.id AS badgeid,
      tbl_badge.name AS badgename,
      tbl_badge.color AS badgecolor,
      tbl_badge.color2 AS badgecolor2,
      tbl_badge.icon AS badgeicon,
      tbl_badge.description AS badgedesc
      FROM tbl_slot
      LEFT JOIN tbl_userslots ON tbl_slot.id = tbl_userslots.slot
      LEFT JOIN tbl_user ON tbl_userslots.user = tbl_user.id
      LEFT JOIN tbl_badge ON tbl_slot.badge = tbl_badge.id
      WHERE tbl_slot.shift = :shift
      ORDER BY badgename DESC");
    $db->bind(':shift',$shift);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to get slots. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    return $db->resultSet();
  }

  public function getSlot($slot) {
    $db = new database();
    $db->query("SELECT tbl_slot.id,
      tbl_slot.shift,
      tbl_slot.badge,
      tbl_userslots.user,
      IF(tbl_user.callsign IS NOT NULL, tbl_user.callsign,
      IF(tbl_user.burnername IS NOT NULL, tbl_user.burnername,
      IF(CONCAT(tbl_user.firstname,tbl_user.lastname) IS NOT NULL, CONCAT_WS(' ',tbl_user.firstname, tbl_user.lastname),
      IF(tbl_user.username IS NOT NULL, tbl_user.username, '')))) AS publicname,
      CASE WHEN (tbl_slot.badge IS NULL || tbl_slot.badge = 0)
      THEN 1
      ELSE 0
      END AS openjoin,
      CASE WHEN (tbl_userslots.user IS NULL)
      THEN 0
      ELSE 1
      END AS claimed,
      tbl_badge.id AS badgeid,
      tbl_badge.name AS badgename,
      tbl_badge.color AS badgecolor,
      tbl_badge.color2 AS badgecolor2,
      tbl_badge.icon AS badgeicon,
      tbl_badge.description AS badgedesc
      FROM tbl_slot
      LEFT JOIN tbl_userslots ON tbl_slot.id = tbl_userslots.slot
      LEFT JOIN tbl_user ON tbl_userslots.user = tbl_user.id
      LEFT JOIN tbl_badge ON tbl_slot.badge = tbl_badge.id
      WHERE tbl_slot.id = :slot");
    $db->bind(':slot',$slot);
    $db->execute();
    return $db->single();
  }

  public function isUserOnShift($shift, $user) {
    $db = new database();
    $db->query("SELECT user, shift FROM tbl_userslots
      WHERE shift = :shift
      AND user = :user");
    $db->bind(':shift',$shift);
    $db->bind(':user',$user);
    $db->execute();
    if($db->single()){
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function canUserClaimSlot($slot) {
    $slot = $this->getSlot($slot);
    //slot object set!
    if (TRUE == $slot->claimed) {
      $return[] = array(
        'msg'=>"This slot has already been claimed. ",
        'level'=>2
      );
      return $return;
    } //Slot's already claimed. Bailing out.
    $team = new team();
    $user = new user();
    $shift = new shift();
    $shift = $shift->getShift($slot->shift);
    //shift object set!
    if($this->isUserOnShift($shift->id,$user->id)) {
      $return[] = array(
        'msg'=>"You can only work one slot per shift.",
        'level'=>2
      );
      return $return;      
    }
    if(FALSE == $team->isUserOnTeam($user->id,$shift->teamid)) {
      $return[] = array(
        'msg'=>"You must be on team $shift->teamname to claim this slot.",
        'level'=>2
      );
      return $return;
    } //"SHE DOESN'T EVEN GO HERE!"
    if (FALSE == $slot->openjoin) {
      $badge = new badge();
      if(!$badge->userHasBadge($user->id,$slot->badge)){
        $return[] = array(
          'msg'=>"You must have the $slot->badgename badge to claim this slot.",
          'level'=>2
        );
        return $return;
      }
    } //Slot requires a badge (that they don't have)
    return TRUE;
  }

  public function claimSlot($slot) {
    $canhas = $this->canUserClaimSlot($slot);
    if(is_array($canhas)) { //Oh man this is SO BAD
      return $canhas;
    }
    $slot = $this->getSlot($slot);
    $user = new user();
    $db = new database();
    //$db->query("UPDATE tbl_slot SET user = :user
    //  WHERE id = :slot");
    $db->query("INSERT INTO tbl_userslots (user, slot, shift)
      VALUES (:user, :slot, :shift)");
    $db->bind(':user',$user->id);
    $db->bind(':slot',$slot->id);
    $db->bind(':shift',$slot->shift);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to claim slot. ".$e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"You have claimed this slot!",
      'level'=>1
    );
    return $return;
  }

}