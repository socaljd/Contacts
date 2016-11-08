<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
include_once $root.'/contacts_ajax.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM jayme.groups WHERE user_id=$user_id ORDER BY group_name";
if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'view_contact.php q1')))) {
  while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    $groups[$row['id']] = $row['group_name'];
  }
}

if ($_REQUEST['id']) {
  $view = $_REQUEST['id'];
  $sql = "SELECT * FROM jayme.contacts WHERE user_id=$user_id AND id=$view";
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'view_contact.php q2')))) {
    if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data = $row;
    } else {
      echo "<script>window.location = 'contact.php'</script>";
    }
  }
  $sql = "SELECT * FROM jayme.contact_fields WHERE contact_id=$view";
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'view_contact.php q3')))) {
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
      if (in_array($row['type'], array('email','phone','url')))
        $data['fields'][$row['type']][$row['id']][$row['name']] = $row['value'];
      else if ($row['type'] == 'group')
        $data['fields'][$row['type']][$row['id']] = $row['value'];
    }
  }
  // echo '<textarea>';
  // echo print_r($data);
  // echo '</textarea>';
}
?>

<!DOCTYPE html>
<html>
  <head>
    <script src="/contacts/contact.js"></script>
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
    <div class="container" style="min-width:1400px;text-align:center">
      <div id="alert"></div>
      <?php if (!$view): ?>
        <h2>Add Contact</h2>
      <?php endif ?>
      <div class="jumbotron" align="center">
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
            <td>
              <label>Reference</label>
              <input type="text" id="reference" value="<?=$data['reference']?>" style="cursor:text" class="form-control" maxlength="45" <?=$view ? 'disabled' : ''?>/>
            </td>
          </tr>
          <tr id="team_row" <?=$data['team'] == 1 ? '' : "style='display:none'"?>>
            <td>
              <label>First Name</label>
              <input type="text" id="first2" value="<?=$data['first2']?>" style="cursor:text" class="form-control" maxlength="20" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Last Name</label>
              <input type="text" id="last2" value="<?=$data['last2']?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Title</label>
              <input type="text" id="title2" value="<?=$data['title2']?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>

            </td>
          </tr>
          <tr>
            <td>
              <label>Hourly</label>
              <input type="text" id="hourly" value="<?=$data['hourly'] > 0 ? $data['hourly'] : ''?>" style="cursor:text" class="form-control" maxlength="10" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Daily</label>
              <input type="text" id="daily" value="<?=$data['daily'] > 0 ? $data['daily'] : ''?>" style="cursor:text" class="form-control" maxlength="10" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <label>Weekly</label>
              <input type="text" id="weekly" value="<?=$data['weekly'] > 0 ? $data['weekly'] : ''?>" style="cursor:text" class="form-control" maxlength="30" <?=$view ? 'disabled' : ''?>/>
            </td>
            <td>
              <table style="width:100%">
                <tr>
                  <td>
                    <label>Team</label>
                    <input type="checkbox" id="team" onclick="$('#team_row').toggle()" class="form-control" <?=$data['team'] ? 'checked' : ''?> <?=$view ? 'disabled' : ''?>/>
                  </td>
                  <td>
                    <label>Freelance</label>
                    <input type="checkbox" id="freelance" class="form-control" <?=$data['freelance'] ? 'checked' : ''?> <?=$view ? 'disabled' : ''?>/>
                  </td>
                  <td>
                    <label>Full Time</label>
                    <input type="checkbox" id="full_time" class="form-control" <?=$data['full_time'] ? 'checked' : ''?> <?=$view ? 'disabled' : ''?>/>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td>
              <label>Groups</label>
              <input type="button" id="add_group" value="+" onclick="add_field('group')" class="btn btn-sm btn-success" <?=$view ? 'disabled' : ''?>/>
              <table id="group_table" class="field_table"></table>
            </td>
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
            <td colspan="4">
              <label>Status</label>
              <select id="status" class="form-control" <?=$view ? 'disabled' : ''?>>
                <option></option>
                <option value="red" <?=$data['status'] == 'red' ? 'selected' : ''?>>Red</option>
                <option value="green" <?=$data['status'] == 'green' ? 'selected' : ''?>>Green</option>
                <option value="black" <?=$data['status'] == 'black' ? 'selected' : ''?>>Black</option>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <label>Comments</label>
              <textarea id="comments" class="form-control" style="width:90%;max-width:560px;cursor:text" <?=$view ? 'disabled' : ''?>><?=$data['comments']?></textarea>
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <input type="button" id="submit" class="btn" />
            </td>
          </tr>
        </table>
      </div>
    </div>
  </body>
</html>

<script>
var view = '<?=$view?>';
var count = {};
var groups = <?=json_encode($groups)?>;
count['group'] = 0;
count['email'] = 0;
count['phone'] = 0;
count['url'] = 0;
$(document).ready(function() {
  var fields;

  $('#status').on('change', function() {
    update_status();
  });

  if ('<?=$view?>') {
    update_status();
    fields = <?=json_encode($data['fields'])?>;
    contact_groups = <?=json_encode($data['fields']['group'])?>;
    for (var group_id in contact_groups) {
      var i = count['group'];
      add_field('group', group_id);
      $('#group'+i).val(contact_groups[group_id]);
    }
    for (var type in fields) {
      if (inArray(type, ['email','phone','url'])) {
        for (var id in fields[type]) {
          for (var name in fields[type][id]) {
            var i = count[type];
            var value = fields[type][id][name];
            add_field(type, id);
            $('#'+type+i).val(value).attr('disabled', true);
            if (inArray(type, ['email','phone','url']))
              $('#'+type+'_name'+i).val(name).attr('disabled', true);
          }
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
    $('#submit').val('Save').addClass('btn-success').on('click', function() {
      add_contact();
    });
  }

});

function add_contact() {
  var dt = {};
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

function update_status() {
  if ($('#status').val() == 'red')
    $('#status').css('background-color', 'red').css('color', 'black');
  else if ($('#status').val() == 'green')
    $('#status').css('background-color', 'green').css('color', 'white');
  else if ($('#status').val() == 'black')
    $('#status').css('background-color', 'black').css('color', 'white');
  else
    $('#status').css('background-color', '').css('color', '');
}

function edit_contact() {
  view = '';
  $('select, .form-control, .remove_field, #add_email, #add_phone, #add_url, #add_group').each(function() {
    $(this).attr('disabled', false);
  });
  $('#submit').val('Save').removeClass('btn-info').addClass('btn-success').on('click', function() {
    update_contact();
  });
}

function add_field(type, id) {
  var html = "<tr>";
  if (inArray(type, ['email','phone','url'])) {
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
      html += "<input type='button' value='-' onclick='delete_field($(this), \""+(id ? id : false)+"\")' class='remove_field btn btn-sm btn-danger' "+(view ? 'disabled' : '')+"/>";
    html += "</td>";
  } else if (type == 'group') {
    html += "<td>";
      html += "<select id='group"+count['group']+"' "+(id ? "data-field_id='"+id+"'" : '')+" class='group form-control' style='width:95%' "+(view ? 'disabled' : '')+">";
        html += "<option></option>";
      for (var i in groups)
        html += "<option value='"+i+"'>"+groups[i]+"</option>";
      html += "</select>";
    html += "</td>";
    html += "<td style='width:1%'>";
      html += "<input type='button' value='-' onclick='delete_field($(this), \""+(id ? id : false)+"\")' class='remove_field btn btn-sm btn-danger' "+(view ? 'disabled' : '')+"/>";
    html += "</td>";
  }
  html += "</tr>";
  count[type]++;
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
  dt['first'] = $('#first').val();
  dt['last'] = $('#last').val();
  dt['title'] = $('#title').val();
  dt['first2'] = $('#first2').val();
  dt['last2'] = $('#last2').val();
  dt['title2'] = $('#title2').val();
  dt['hourly'] = $('#hourly').val();
  dt['daily'] = $('#daily').val();
  dt['weekly'] = $('#weekly').val();
  dt['reference'] = $('#reference').val();
  dt['team'] = $('#team').is(':checked') ? 1 : 0;
  dt['freelance'] = $('#freelance').is(':checked') ? 1 : 0;
  dt['full_time'] = $('#full_time').is(':checked') ? 1 : 0;
  dt['status'] = $('#status').val();
  dt['comments'] = $('#comments').val();

  dt['fields'] = {};
  dt['fields']['group'] = {};
  dt['fields']['group']['new'] = {};
  dt['fields']['url'] = {};
  dt['fields']['url']['new'] = {};
  dt['fields']['email'] = {};
  dt['fields']['email']['new'] = {};
  dt['fields']['phone'] = {};
  dt['fields']['phone']['new'] = {};

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

  $('.group').each(function() {
    if ($(this).val() && !inArray($(this).val(), dt['fields']['group'])) {
      if ($(this).data('field_id')) {
        var group_id = $(this).data('field_id');
        dt['fields']['group'][group_id] = $(this).val();
      } else {
        var i = $(this).attr('id').substring(5);
        dt['fields']['group']['new'][i] = $(this).val();
      }
    }
  });
  return dt;
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
