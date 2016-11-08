<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
include_once $root.'/contacts_ajax.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM jayme.groups WHERE user_id=$user_id";
if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'contact_groups.php q1')))) {
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $groups[$row['id']] = $row['group_name'];
  }
}

// echo '<pre>';
// echo print_r($groups);
// echo '</pre>';

?>

<!-- <!DOCTYPE html> -->
<html>
  <head>
    <script src="/contacts/contacts.js"></script>
    <link href="/includes/bootstrap-3.1.1/css/bootstrap.css" rel="stylesheet">
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
<!--
    <script type="text/javascript" src="/includes/bootstrap-3.1.1/js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="/includes/bootstrap-3.1.1/css/bootstrap-multiselect.css" type="text/css"/> -->
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:40%;text-align:center">
      <div id="alert"></div>
      <h2>Groups</h2>
      <div class="jumbotron" align="center">
        <table id="groups_table" class="form-inline" style="width:80%"></table>
      </div>
    </div>
  </body>
</html>

<script>
groups = {};
$(document).ready(function() {
  get_groups();
});

function get_groups() {
  var dt = {};
  dt['action'] = 'pull_groups';
  // return false;
  $.ajax({
    type: 'POST',
    url: 'contacts_ajax.php',
    data: dt,
    dataType: 'json',
    async: false,
    success: function(json) {
      console.log('contact.php get_groups() ajax success');
      console.log(json);
      groups = json.data;
      var table = '';

      table += "<tr style='border:1px solid'>";
        table += "<td style='padding:10px;text-align:center'>";
          table += "<input type='text' id='group_name' class='form-inline' />";
        table += "</td>";
        table += "<td style='width:10%;text-align:center'>";
          table += "<input type='button' value='+' onclick='group_action(\"add_group\")' class='btn btn-sm btn-success' />";
        table += "</td>";
      table += "</tr>";

      for (var i in json.data) {
        table += "<tr style='border:1px solid'>";
          table += "<td style='padding:10px'>";
            table += "<div style='text-align:center'>"+json.data[i].group_name+"</div>";
          table += "</td>";
          table += "<td style='width:10%;text-align:center'>";
            table += "<input type='button' value='-' onclick='group_action(\"delete_group\", "+json.data[i].id+")' class='btn btn-sm btn-danger'>";
          table += "</td>";
        table += "</tr>";
      }
      $('#groups_table').html(table);
    },
    error: function(json) {
      console.log('contact.php get_groups() ajax error');
      console.log(json);
    }
  });
}

function group_action(action, group_id) {
  if(action == 'delete_group') {
    var group_name = '';
    for(var i in groups) {
      if(group_id == groups[i].id)
        group_name = groups[i].group_name;
    }
    if(!confirm('Are you sure you want to delete '+group_name+'?'))
      return false;
  } else if (action == 'add_group' && !$('#group_name').val()) {
    alert('Enter a group name first.');
    return false;
  }
  var dt = {};
  dt['action'] = action;
  dt['group_id'] = group_id;
  dt['group_name'] = $('#group_name').val();
  $.ajax({
    type: 'POST',
    url: 'contacts_ajax.php',
    data: dt,
    dataType: 'json',
    async: false,
    success: function(json) {
      console.log('contact.php update_group() ajax success');
      console.log(json);
      get_groups();
    },
    error: function(json) {
      console.log('contact.php update_group() ajax error');
      console.log(json);
    }
  });
}

</script>


