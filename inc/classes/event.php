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
        'msg'=>"These dates don't make any sense: $start vs. $end",
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
        'msg'=>'When does this event start?',
        'level'=>2
      );
      return $return; 
    }
    $end = validate($end);
    if ('' === $end) {
      $return[] = array(
        'msg'=>'When does this event start?',
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
    $events = $db->resultset();
    $return = tableHeader(array('Name','Start','End','Duration','Location'),'sort');

    if (!$events) {
      $return.= emptyTable('No events scheduled',5);
      $return.= tableFooter();
      return $return;
    }

    foreach ($events as $event) {
      $return.= tableCells(array(
        "<a href='?action=viewEvent&event=$event->id'>$event->name</a>", 
        timestamp($event->start), 
        timestamp($event->end), 
        singular($event->duration,'Hour','Hours'), 
        icon('map-marker')." <a href='https://www.google.com/maps/place/".urlencode($event->location)."' target='_blank'>$event->location</a>"
      ));
    }
    $return.= tableFooter();
    echo $return;
  }
  public function getEvent($event) {
    $db = new database();
    $db->query("SELECT tbl_event.*,
        time_format(timediff(tbl_event.end, tbl_event.start),'%H') as duration,
        GROUP_CONCAT(tbl_shift.id) AS shifts,
        GROUP_CONCAT(tbl_slot.id) AS slots
        FROM tbl_event
        LEFT JOIN tbl_shift ON tbl_event.id = tbl_shift.event
        LEFT JOIN tbl_slot ON tbl_shift.id = tbl_slot.shift
        LEFT JOIN tbl_userslots ON tbl_slot.id = tbl_userslots.slot
        WHERE tbl_event.id = :event");
    $db->bind(':event',$event);
    $db->execute();
    return $db->single();
  }

  public function getEventByShift($shift) {
    $db = new database();
    $db->query("SELECT tbl_event.* FROM tbl_event
      LEFT JOIN tbl_shift ON tbl_event.id = tbl_shift.event
      WHERE tbl_shift.id = :shift");
    $db->bind(':shift',$shift);
    $db->execute();
    return $db->single();
  }

  public function cancelEvent($event) {
    $info = $this->getEvent($event);
    $user = new user();
    if(!$user->isAdmin()) {
      $return[] = array(
        'msg'=>"You must be an administrator to do this.",
        'level'=>2
      );
      return $return;
    }
    if (!$info) {
      $return[] = array(
        'msg'=>"Error: Unable to locate event.",
        'level'=>2
      );
      return $return;
    }
  }

}