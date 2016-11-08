<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if(login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

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
    <!-- needed for ajax request -->
    <script src="http://malsup.github.com/jquery.form.js"></script>
    <!-- needed for file input -->
    <script src="/includes/bootstrap-3.1.1/js/bootstrap.fileinput.js"></script>
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:50%;text-align:center">
    	<div id="alert"></div>
      <h2>Settings</h2>
      <div class="jumbotron" align="center">
        <input type="button" id="delete_data" value="Delete All Data" class="btn btn-danger" onclick="delete_data()" />
      </div>
    </div>
  </body>
</html>

<script>

function delete_data() {
  if(confirm("Are you sure you want to delete all contacts?")) {
    var dt = {};
    dt['action'] = 'delete_data';
    $.ajax({
      type: 'POST',
      url: 'settings_ajax.php',
      data: dt,
      dataType: 'json',
      async: false,
      success: function(json) {
        // console.log('settings.php delete_data() ajax success');
        // console.log(json);
        $('#alert').html(json.msg).addClass('alert alert-success');
      },
      error: function(json) {
        console.log('settings.php delete_data() ajax error');
        console.log(json);
      }
    });
  }
}

</script>
