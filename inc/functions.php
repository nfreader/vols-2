<?php

/* Singular
 *
 * Based on the input, outputs the singular or plural of the specified unit
 *
 * @value (int) The value we're looking at
 * @one (string) The output if the value is one
 * @many (string) The output if the value is greater than one
 *
 * @return string
 *
 */

function singular($value, $one, $many) {
	if ($value == 1) {
		return "$value $one";
	} else {
		return "$value $many";
	}
}

/* getSalt
 *
 * Outputs a string of random characters to salt a hash function.
 * Credit to @arplynn for pointing me in the right direction.
 *
 * @return(string) A string of random characters, where the length is
 * specified by the PASSWD_SALT_LENGTH constant in inc/config.php
 *
 */

function getSalt() {
  $saltSource = fopen('/dev/urandom', 'rb');
  $saltData = bin2hex(fread($saltSource, PASSWD_SALT_LENGTH));
  fclose($saltSource);
  return $saltData;
}

/* icon
 *
 * Renders the HTML for a Font Awesome icon!
 *
 * @icon (string) Icon to display
 * @class (string) (optional) Additional class to add to the icon. 
 * Technically could be a part of @icon, but where's the fun in that?
 *
 * @return string
 *
 */

function icon($icon, $class = '') {
	return "<span class='fa fa-$icon $class'></span> ";
}

function tableHeader($columns, $class='') {
    $header = "<table class='table $class table-bordered table-hover table-condensed'><thead><tr>";
    foreach ($columns as $column) {
        $header.= "<th>$column</th>";
    }
    $header.= "</thead><tbody>";
    
    return $header;
}

function tableCells($cells, $class='') {
	$return = "<tr class='$class'>";
	foreach ($cells as $cell) {
		$return.= "<td>$cell</td>";
	}
	$return.= "</tr>";
	return $return;
}

function tableFooter() {
	return "</tbody></table>";
}

function relativeTime($date, $postfix = ' ago', $fallback = 'F Y') 
{
    $diff = time() - strtotime($date);
    if($diff < 60) 
        return $diff . ' second'. ($diff != 1 ? 's' : '') . $postfix;
    $diff = round($diff/60);
    if($diff < 60) 
        return $diff . ' minute'. ($diff != 1 ? 's' : '') . $postfix;
    $diff = round($diff/60);
    if($diff < 24) 
        return $diff . ' hour'. ($diff != 1 ? 's' : '') . $postfix;
    $diff = round($diff/24);
    if($diff < 7) 
        return $diff . ' day'. ($diff != 1 ? 's' : '') . $postfix;
    $diff = round($diff/7);
    if($diff < 4) 
        return $diff . ' week'. ($diff != 1 ? 's' : '') . $postfix;
    $diff = round($diff/4);
    if($diff < 12) 
        return $diff . ' month'. ($diff != 1 ? 's' : '') . $postfix;

    return date($fallback, strtotime($date));
}

function isEmpty($string) {
	if (empty($string) || trim($string) == '') {
		return true;
	}
	return false;
}

function futureDate($date) {
	$diff = date('U',strtotime($date)) - time();
	if($diff >= 86400) {
		$diff = round($diff/86400);
		$return = $diff." day". ($diff != 1 ? 's' : ''); 
	}
	if ($diff >= 3600) {
		$diff = round($diff/3600);
		$return = $diff." hour". ($diff != 1 ? 's' : '');
	}
	return "<span class='time' data-toggle='tooltip' title='".date('F d Y \a\t H:i',strtotime($date))."'>".timestamp($date)."</span>";
}

function timestamp($date) {
	return date(DATE_FORMAT,strtotime($date));
}

function alert($msg) {
  switch($msg['level']) {
    case 0:
    default:
      $level = 'info';
      break;

    case 1:
      $level = 'success';
      break;

    case 2:
      $level = 'danger';
      break;
  }
  echo "<div class='alert alert-$level alert-dismissable' role='alert'>";
  echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  echo $msg['msg'];
  echo "</div>";
}

function validate($string) {
  return trim(strip_tags($string));
}

function userLink($name,$id) {
  return "<a href='?action=viewMember&id=$id'>$name</a>";
}

function teamLink($name,$id) {
  return "<a href='?action=viewTeam&team=$id'>$name</a>";
}

function eventLink($name,$id) {
  return "<a href='?action=viewEvent&event=$id'>$name</a>";
}

function renderBadge($badge, $class='') {
  $return = "<a href='?action=viewBadge&badge=$badge->id'>";
  $return.= "<span class='label $class'";
  $return.= "style='background: $badge->color; color: $badge->color2'";
  $return.= "title='$badge->description'>";
  $return.= icon($badge->icon)." ";
  $return.= "$badge->name";
  $return.= "</span></a> ";
  return $return;
}

function generatePasswordResetLink(){
  include ('arrays.php');
  $words = array_rand($PGPWordList,10);
  $words = $PGPWordList[$words[0]]."-".
  $PGPWordList[$words[1]]."-".
  $PGPWordList[$words[2]]."-".
  $PGPWordList[$words[3]]."-".
  $PGPWordList[$words[4]];
  return strtolower($words);
}


