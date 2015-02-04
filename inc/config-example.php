<?php

date_default_timezone_set('America/New_York');

define('DB_METHOD', 'CHANGEME');//Probably won't need to change
define('DB_NAME', 'CHANGEME');
define('DB_USER', 'CHANGEME');
define('DB_PASS', 'CHANGEME');
define('DB_HOST', 'localhost');//Probably won't need to change

//Probably won't need to change,
//unless you want two or more parallel installations
define('TBL_PREFIX', 'v2_');

//Salt length for hashing passwords
define('PASSWD_SALT_LENGTH', 16);
define('DEBUG',TRUE);

define('APP_NAME','Volunteer App');

define('DATE_FORMAT','F d Y \a\t H:i');

require_once ('autoload.php');
require_once ('functions.php');

$session = new session();
session_set_save_handler($session, true);

header('Content-type: text/html; charset=utf-8');

require_once('notification.php');