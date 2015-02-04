<?php

class volunteer {

  public $id;
  public $teams;

  public function __construct($user) {
    $user = new user();
    $this->id = $user->id;
    $this->teams = $this->getTeamMembership($this->id);
  }

  public function getTeamMembership() {

  }

  public function listUsers() {
    $database = new database();
    $db->query();
  }

}