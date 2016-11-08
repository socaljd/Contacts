<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

if ($_REQUEST['id']) {
  $view = $_REQUEST['id'];
  $sql = "SELECT * FROM jayme.contacts WHERE user_id=$user_id AND id=$view";
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'view_contact.php q1')))) {
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data = $row;
    } else {
      echo "<script>window.location = 'contact.php'</script>";
    }
  }
  $sql = "SELECT * FROM jayme.contact_fields WHERE contact_id=$view";
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'view_contact.php q2')))) {
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data['fields'][$row['type']][$row['id']][$row['name']] = $row['value'];
    }
  }
}
?>

<html>
  <head>
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
  </head>
  <body>
    <?php require_once $root.'/template/header.php' ?>
    <div class="container" style="width:80%;text-align:center">
      <div id="alert"></div>
      <?php if (!$view): ?>
        <h2>Add Contact</h2>
      <?php endif ?>
      <div class="jumbotron" align="center" style="min-width:1100px">
        <table id="controls" class="form-inline">
          <tr>
            <td>
              <label>First Name</label>
              <input type="text" id="first" value="<?=$data['first']?>" style="cursor:text" class="form-control" maxlength="20" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Last Name</label>
              <input type="text" id="last" value="<?=$data['last']?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Title</label>
              <input type="text" id="title" value="<?=$data['title']?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
          </tr>
          <tr>
            <td>
              <label>Location</label>
              <input type="text" id="location" value="<?=$data['location']?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Hourly</label>
              <input type="text" id="hourly" value="<?=$data['hourly'] > 0 ? $data['hourly'] : ''?>" style="cursor:text" class="form-control" maxlength="10" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Daily</label>
              <input type="text" id="daily" value="<?=$data['daily'] > 0 ? $data['daily'] : ''?>" style="cursor:text" class="form-control" maxlength="10" <?=$view ? 'disabled' : ''?>/>
            </td>
          </tr>
          <tr>
            <td>
              <label>Email</label>
              <input type="button" id="add_email" value="+" onclick="add_field('email')" class="btn btn-sm btn-success" <?=$view ? 'disabled' : ''?>/>
              <table id="email_table" class="field_table"></table>
            </td>
            <td>
              <label>Phone</label>
              <input type="button" id="add_phone" value="+" onclick="add_field('phone')" class="btn btn-sm btn-success" <?=$view ? 'disabled' : ''?>/>
              <table id="phone_table" class="field_table"></table>
            </td>
            <td>
              <label>URL</label>
              <input type="button" id="add_url" value="+" onclick="add_field('url')" class="btn btn-sm btn-success" <?=$view ? 'disabled' : ''?>/>
              <table id="url_table" class="field_table"></table>
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <label>Comments</label>
              <textarea id="comments" class="form-control" style="width:90%;max-width:560px;cursor:text" <?=$view ? 'disabled' : ''?>><?=$data['comments']?></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="3">
              <input type="button" id="submit" class="btn" />
            </td>
          </tr>
        </table>
      </div>
    </div>
  </body>
</html>

<script>
var count = {};
count['email'] = 0;
count['phone'] = 0;
count['url'] = 0;
$(document).ready(function() {

  if ('<?=$view?>') {
    var fields = <?=json_encode($data['fields'])?>;
    for(var type in fields) {
      for(var id in fields[type]) {
        for(var name in fields[type][id]) {
          add_field(type, id);
          var i = count[type];
          var value = fields[type][id][name];
          $('#'+type+'_name'+i).val(name).attr('disabled', true);
          $('#'+type+i).val(value).attr('disabled', true);
        }
      }
    }
    $('.remove_field').each(function() {
      $(this).attr('disabled', true);
    });
    $('#submit').val('Edit').addClass('btn-info').on('click', function() {
      edit_contact();
    });
  } else {
    add_field('email');
    add_field('phone');
    add_field('url');
    $('#submit').val('Save').addClass('btn-success').on('click', function() {
      add_contact();
    });
  }

});

function edit_contact() {
  $('select, .form-control, .remove_field').each(function() {
    $(this).attr('disabled', false);
  });
  $('#add_email, #add_phone, #add_url').attr('disabled', false);
  $('#submit').val('Save').removeClass('btn-info').addClass('btn-success').on('click', function() {
    update_contact();
  });
}

function update_contact() {
  console.log('update_contact');
  var dt = {};
  dt = get_data();
  dt['action'] = 'update_contact';
  dt['contact_id'] = '<?=$view?>';
  $.ajax({
    type: 'POST',
    url: 'contacts_ajax.php',
    data: dt,
    dataType: 'json',
    async: false,
    success: function(json) {
      // console.log('contact.php update_contact() ajax success');
      // console.log(json);
      window.location.href = 'contact.php?id='+'<?=$view?>';
    },
    error: function(json) {
      console.log('contact.php update_contact() ajax error');
      console.log(json);
    }
  });
}

function add_field(type, id) {
  count[type]++;
  var html = "<tr>";
    html += "<td>";
      html += "<select id='"+type+"_name"+count[type]+"' "+(id ? "data-field_id='"+id+"'" : '')+" class='form-control'>";
        html += "<option value='primary'>Primary</option>";
        html += "<option value='work'>Work</option>";
        html += "<option value='home'>Home</option>";
      html += "</select>";
    html += "</td>";
    html += "<td>";
      html += "<input type='text' id='"+type+count[type]+"' "+(id ? "data-field_id='"+id+"'" : '')+" class='"+type+" form-control' style='cursor:text'/>";
    html += "</td>";
    html += "<td>";
      html += "<input type='button' value='-' onclick='delete_field($(this), \""+(id ? id : false)+"\")' class='remove_field btn btn-sm btn-danger'/>";
    html += "</td>";
  html += "</tr>";
  $('#'+type+'_table').append(html);
}

function delete_field(e, id) {
  if (id == 'false') {
    e.parent().parent().remove();
  } else if (confirm('Are you sure you want to delete this field?')) {
    var dt = {};
    dt['action'] = 'delete_field';
    dt['field_id'] = id;
    $.ajax({
      type: 'POST',
      url: 'contacts_ajax.php',
      data: dt,
      dataType: 'json',
      async: false,
      success: function(json) {
        // console.log('contact.php delete_field() ajax success');
        // console.log(json);
      },
      error: function(json) {
        console.log('contact.php delete_field() ajax error');
        console.log(json);
      }
    });
    e.parent().parent().remove();
  }
}

function get_data() {
  var dt = {};
  dt['fields'] = {};
  dt['fields']['url'] = {};
  dt['fields']['url']['new'] = {};
  dt['fields']['email'] = {};
  dt['fields']['email']['new'] = {};
  dt['fields']['phone'] = {};
  dt['fields']['phone']['new'] = {};

  dt['first'] = $('#first').val();
  dt['last'] = $('#last').val();
  dt['location'] = $('#location').val();
  dt['title'] = $('#title').val();
  dt['hourly'] = $('#hourly').val();
  dt['daily'] = $('#daily').val();
  dt['comments'] = $('#comments').val();

  $('.email, .phone, .url').each(function() {
    if ($(this).hasClass('email')) {
      var type = 'email';
      var i = $(this).attr('id').substring(5);
    } else if ($(this).hasClass('phone')) {
      var type = 'phone';
      var i = $(this).attr('id').substring(5);
    } else if ($(this).hasClass('url')) {
      var type = 'url';
      var i = $(this).attr('id').substring(3);
    }
    var name = $('#'+type+'_name'+i).val();
    if ($(this).data('field_id')) {
      var field_id = $(this).data('field_id');
      dt['fields'][type][field_id] = {};
      dt['fields'][type][field_id][name] = $(this).val();
    } else {
      dt['fields'][type]['new'][i] = {};
      dt['fields'][type]['new'][i][name] = $(this).val();
    }
  });

  return dt;
}

function add_contact() {
  var dt = get_data();
  dt['action'] = 'add_contact';
  // return false;
  $.ajax({
    type: 'POST',
    url: 'contacts_ajax.php',
    data: dt,
    dataType: 'json',
    async: false,
    success: function(json) {
      // console.log('contact.php add_contact() ajax success');
      // console.log(json);
      window.location.href = window.location.href+'?id='+json.data;
    },
    error: function(json) {
      console.log('contact.php add_contact() ajax error');
      console.log(json);
    }
  });
}

</script>

<style type="text/css">
#controls {
  width: 100%;
}
#controls td {
  padding: 20px;
  max-width: 250px;
  text-align: center;
}
#controls label {
  display: block;
}
#controls .field_table {
  width: 100%;
}
#controls .field_table td {
  padding: 2px;
}
.bootstrap-select {
  width: 100px;
}
</style>
