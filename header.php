<?php
  require_once('inc/config.php');
  $user = new user();
  if(isset($_GET['action'])) {
    require_once('action.php'); 
  }
  $time = explode(' ', microtime());
  $start = $time[1] + $time[0];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/datetimepicker.css" rel="stylesheet">
    <link rel="stylesheet/less" type="text/css" href="assets/css/style.less" />

    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/1.6.3/less.min.js">
    </script>
    

    <style>
    body {
      padding-top: 60px; 
    }
    form.register .checkbox {
      display: none;
    } 
    .color-choice {
      width: 30px;
      margin: 0 5px 0 0;
      display: inline-block;
      height: 30px;
    }
    .color-choice.color-active {
      box-shadow: 0 0 4px #EA4443;
    }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <?php require_once('view/nav.php'); ?>
    <div class="container">

    <?php 
    if (!empty($msg)) {
      foreach ($msg as $alert){
        alert($alert);
      }
    }
    ?>