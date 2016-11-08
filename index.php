<?php
include_once 'functions.php';
include_once 'db/db_connect.php';
include_once 'login/functions.php';

sec_session_start();

?>

<html>
  <head>
    <link href="includes/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="includes/bootstrap-3.1.1/js/bootstrap.min.js"></script>
  </head>
  <body>
    <?php require_once 'template/header.php' ?>
    <div class="container">
      <?php if($_REQUEST['error'] == 1): ?>
        <div id="alert" class="alert alert-info" align="center">You are already logged in.</div>
      <?php endif ?>
        <div class="jumbotron" align="center">
      <?php if(login_check($mysqli) == true): ?>
        <h1>Welcome <?php echo ucfirst(htmlentities($_SESSION['username'])) ?>!</h1>
      <?php else: ?>
        <h2>Welcome</h2><br />
        <h5></h5>
      <?php endif ?>
      </div>
    </div>
  </body>
</html>




