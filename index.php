<?php 

require_once('header.php');

if (isset($include)) {
  include "view/$include.php";
} elseif(!$user->isLoggedIn()) {
  include('view/guest.php');
} else {
  include ('view/home.php');
}

require_once('footer.php');
