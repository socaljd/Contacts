<?php
$root = $_SERVER['DOCUMENT_ROOT'];
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Secure Login: Registration Success</title>
    <link rel="stylesheet" href="styles/main.css" />
    <link href="/includes/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:50%">
      <div id="alert"></div>
      <div class="jumbotron" align="center">
        <div id="templatemo_middle" align="center">
          <h2>Registration successful!</h2>
          <p>You can now go back to the <a href="/login/login.php">login page</a> and log in</p>
        </div>
      </div>
    </div>
  </body>
</html>
