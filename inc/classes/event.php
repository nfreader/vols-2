<?php

class event {
  public function addEvent($name, $start, $end, $desc, $location) {
    $user = new user();
    if(!$user->isAdmin()) {
      $return[] = array(
        'msg'=>'You must be a site administrator!',
        'level'=>2
      );
      return $return;
    }
    if ($end < $start) {
      $return[] = array(
        'msg'=>"Those dates don't make any sense: $start vs. $end",
        'level'=>2
      );
      return $return;
    }
    $name = validate($name);
    if ('' === $name) {
      $return[] = array(
        'msg'=>'Name cannot be empty!',
        'level'=>2
      );
      return $return; 
    }
    $start = validate($start);
    if ('' === $start) {
      $return[] = array(
        'msg'=>'When does your event start?',
        'level'=>2
      );
      return $return; 
    }
    $end = validate($end);
    if ('' === $end) {
      $return[] = array(
        'msg'=>'When does your event start?',
        'level'=>2
      );
      return $return; 
    }
    $db = new database();
    $db->query("INSERT INTO tbl_event
      (name, start, end, description, location) VALUES
      (:name, :start, :end, :description, :location)");
    $db->bind(':name',$name);
    $db->bind(':start',date('Y-m-d H:i:s',strtotime(str_replace('/','-',$start))));
    $db->bind(':end',date('Y-m-d H:i:s',strtotime(str_replace('/','-',$end))));
    $db->bind(':description',$desc);
    $db->bind(':location',$location);
    $db->execute();
    $return[] = array(
      'msg'=>"Your event, '$name', was created.",
      'level'=>1
    );
    return $return;
  }

  public function listEvents() {
    $db = new database();
    $db->query("SELECT *,
        time_format(timediff(end, start),'%H') as duration
        FROM tbl_event
        ORDER BY tbl_event.start ASC");
    $db->execute();
    return $db->resultset();
  }
  public function getEvent($event) {
    $db = new database();
    $db->query("SELECT *,
        time_format(timediff(end, start),'%H') as duration
        FROM tbl_event
        WHERE tbl_event.id = :event");
    $db->bind(':event',$event);
    $db->execute();
    return $db->single();
  }
}