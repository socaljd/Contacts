<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once 'functions.php';
include_once $root.'db/db_connect.php';
?>

<html>
  <head>
    <link href="/includes/bootstrap-3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/includes/bootstrap-3.1.1/js/bootstrap.min.js"></script>
    <script type="text/JavaScript" src="/js/sha512.js"></script>
    <script type="text/JavaScript" src="/js/forms.js"></script>
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:25%">
    <?php if($_REQUEST['error'] == 1): ?>
      <div id="alert" class="alert alert-danger" align="center">Please log in.</div>
    <?php else: ?>
      <div id="alert"></div>
    <?php endif ?>
      <div class="jumbotron" align="center">
      <?php if(login_check($mysqli) == true): ?>
        <h1>Welcome <?php echo htmlentities($_SESSION['username']) ?>!</h1>
      <?php else: ?>
        <h2>Log In</h2><br />
        <form action="process_login.php" method="post" name="login_form" class="form-signin" role="form">
          <input type="text" name="username" id="username" class="form-control" placeholder="Username" /><br />
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" onkeypress="return checkSubmit(event)" /><br />
          <input type="button" value="Login" onclick="formhash(this.form, this.form.password);" class="btn btn-lg btn-primary btn-block" />
        </form>
      <?php endif ?>
      </div>
    </div>
  </body>
</html>

<script type="text/javascript">
  function checkSubmit(e) {
    if(e && e.keyCode == 13) {
      formhash(document.forms[0], document.forms[0].password);
    }
  }
</script>
