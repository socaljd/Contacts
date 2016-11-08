<?php
// exit;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
include_once $root.'/login/register.inc.php';
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
    <div class="container" style="width:25%;text-align:center">
      <h2>Register</h2>
      <div id="alert"></div>
      <div class="jumbotron" align="center">
        <form action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>" method="post" name="registration_form" class="form-signin" role="form">
          <input type="text" name="username" id="username" class="form-control" placeholder="Username" /><br />
          <input type="text" name="email" id="email" class="form-control" placeholder="E-mail" /><br />
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" /><br />
          <input type="password" name="confirmpwd" id="confirmpwd" class="form-control" placeholder="Confirm Password" /><br />
          <input type="button" value="Register" onclick="return regformhash(this.form,this.form.username,this.form.email,this.form.password,this.form.confirmpwd);" class="btn btn-lg btn-primary btn-block" />
        </form>
      </div>
    </div>
  </body>
</html>
