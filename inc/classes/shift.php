<?php

class shift {

  public function userCanEdit($event) {
    //TODO: Run back up the chain and see if the team assigned to this shift
    //was listed as a team that can make changes to this events shifts
    $user = new user();
    if ($user->isAdmin()){
      return true;
    } else {
      return false;
    }
    return false;
  }

  public function addShift($event, $team, $start, $end) {
    if ($this->userCanEdit($event)) {
      $db = new database();
      $db->query("INSERT INTO tbl_shift (event, team, start, end)
        VALUES (:event, :team, :start, :end)");
      $db->bind(':event',$event);
      $db->bind(':team',$team);
      $db->bind(':start',$start);
      $db->bind(':end',$end);
      try {
        $db->execute();
      } catch (Exception $e) {
        $return[] = array(
          'msg'=>"Something went wrong. Unable to create event. ".
          $e->getMessage(),
          'level'=>2
        );
        return $return; 
      }
      $return[] = array(
        'msg'=>"New shift created!",
        'level'=>1
      );
      return $return; 
    }
  }

  public function getShifts($event) {
    $db = new database();
    $db->query("SELECT tbl_shift.*,
      time_format(timediff(tbl_shift.end, tbl_shift.start),'%H') as duration,
      tbl_team.name AS teamname,
      tbl_team.id AS teamid,
      CASE WHEN tbl_shift.start < tbl_event.start
      THEN 1
      ELSE 0
      END AS startsbefore,
      CASE WHEN tbl_shift.end > tbl_event.end
      THEN 1
      ELSE 0
      END AS endsafter,
      COUNT(tbl_slot.id) AS slots,
      COUNT(tbl_userslots.user) AS filled
      FROM tbl_shift
      LEFT JOIN tbl_team ON tbl_shift.team = tbl_team.id
      LEFT JOIN tbl_event ON tbl_shift.event = tbl_event.id
      LEFT JOIN tbl_slot ON tbl_shift.id = tbl_slot.shift
      LEFT JOIN tbl_userslots ON tbl_slot.id = tbl_userslots.slot
      WHERE tbl_shift.event = :event
      GROUP BY tbl_shift.id
      ORDER BY tbl_shift.start ASC");
    $db->bind(':event',$event);
    $db->execute();
    return $db->resultset();
  }

  public function getShift($shift) {
    $db = new database();
    $db->query("SELECT tbl_shift.*,
      time_format(timediff(tbl_shift.end, tbl_shift.start),'%H') as duration,
      tbl_team.name AS teamname,
      tbl_team.id AS teamid,
      tbl_event.start AS eventstart,
      tbl_event.end AS eventend,
      time_format(timediff(tbl_event.end, tbl_event.start),'%H') as
      eventduration,
      CASE WHEN tbl_shift.start < tbl_event.start
      THEN 1
      ELSE 0
      END AS startsbefore,
      CASE WHEN tbl_shift.end > tbl_event.end
      THEN 1
      ELSE 0
      END AS endsafter,
      GROUP_CONCAT(tbl_slot.id) AS slots
      FROM tbl_shift
      LEFT JOIN tbl_team ON tbl_shift.team = tbl_team.id
      LEFT JOIN tbl_event ON tbl_shift.event = tbl_event.id
      LEFT JOIN tbl_slot ON tbl_shift.id = tbl_slot.shift
      WHERE tbl_shift.id = :shift");
    $db->bind(':shift',$shift);
    $db->execute();
    return $db->single();
  }

  function deleteShift($shift) {
    //There's some cleanup on this one, so we need the shift info first.
    $info = $this->getShift($shift);
    //getShift will always return startsbefore and endsafter as 0 even if there
    //is no shift. So we need to check another property for NULLness.
    if(NULL == $info->event) {
      $return[] = array(
        'msg'=>"Error: Unable to locate shift.",
        'level'=>2
      );
      return $return;
    }
    if(!$this->userCanEdit($info->event)) {
      $return[] = array(
        'msg'=>"You must be a team lead or admin to edit shifts.",
        'level'=>2
      );
      return $return;
    }
    $db = new database();
    //Clear out the associated slots from the userslots FK table
    //using the concatenated list of slots from getShift()
    //Or skip it entirely if there aren't any slots
    if(NULL != $info->slots) {
      $db->query("DELETE FROM tbl_userslots 
        WHERE tbl_userslots.slot IN ($info->slots)");
      try {
        $db->execute();
      } catch (Exception $e) {
        $return[] = array(
          'msg'=>"Something went wrong. Unable to delete slots. ".
          $e->getMessage(),
          'level'=>2
        );
        return $return; 
      }
    }
    //Now we can delete the shift from tbl_shift since our FK constraints are
    //cleared.
    $db->query("DELETE FROM tbl_shift WHERE tbl_shift.id = :shift");
    $db->bind(':shift',$shift);
    try {
      $db->execute();
    } catch (Exception $e) {
      $return[] = array(
        'msg'=>"Something went wrong. Unable to delete shift. ".
        $e->getMessage(),
        'level'=>2
      );
      return $return; 
    }
    $return[] = array(
      'msg'=>"Shift deleted and user schedules upated.",
      'level'=>1
    );
    return $return; 
  }

}