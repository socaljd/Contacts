<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

switch ($_REQUEST['action']) {
  case 'add_contact':
    add_contact();
    break;
  case 'update_contact':
    update_contact();
    break;
  case 'delete_contact':
    delete_contact();
    break;
  case 'delete_field':
    delete_field();
    break;
  case 'pull_contacts':
    pull_contacts();
    break;
  default:
    break;
}

function add_contact() {
  global $mysqli, $user_id;
  $hourly = $_REQUEST['hourly'] ? $_REQUEST['hourly'] : 0;
  $daily = $_REQUEST['daily'] ? $_REQUEST['daily'] : 0;
  $sql = "INSERT INTO jayme.contacts (user_id,first,last,location,title,hourly,daily,comments)
          VALUES ($user_id,'{$_REQUEST['first']}','{$_REQUEST['last']}','{$_REQUEST['location']}','{$_REQUEST['title']}','$hourly','$daily','{$_REQUEST['comments']}')";
  $debug['sql1'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php add_contact() q1')));
  $contact_id = mysqli_insert_id($mysqli);
  $debug['contact_id'] = $contact_id;

  foreach ($_REQUEST['fields'] as $type => $v) {
    foreach($v['new'] as $v2) {
      foreach($v2 as $name => $value) {
        $sql = "INSERT INTO jayme.contact_fields (contact_id,type,name,value) VALUES ($contact_id,'$type','$name','$value')";
        $debug['sql2'][] = $sql;
        if ($value)
          $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php add_contact() q2')));
      }
    }
  }

  $debug['REQUEST'] = $_REQUEST;
  die(json_encode(array('msg' => 'Successfully added contact.', 'debug' => $debug, 'data' => $contact_id)));
}

function update_contact() {
  global $mysqli, $user_id;
  $hourly = !is_nan($_REQUEST['hourly']) && $_REQUEST['hourly'] > 0 ? $_REQUEST['hourly'] : 0;
  $daily = !is_nan($_REQUEST['daily']) && $_REQUEST['daily'] > 0 ? $_REQUEST['daily'] : 0;
  $sql = "UPDATE jayme.contacts SET first='{$_REQUEST['first']}', last='{$_REQUEST['last']}', location='{$_REQUEST['location']}', title='{$_REQUEST['title']}', hourly='$hourly', daily='$daily', comments='{$_REQUEST['comments']}' WHERE id={$_REQUEST['contact_id']}";
  $debug['sql1'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php add_contact() q1')));
  $contact_id = $_REQUEST['contact_id'];
  $debug['contact_id'] = $contact_id;

  foreach($_REQUEST['fields'] as $type => $v) {
    foreach($v as $field_id => $v2) {
      if ($field_id != 'new') {
        foreach($v2 as $name => $value) {
          $sql = "UPDATE jayme.contact_fields SET name='$name', value='$value' WHERE id=$field_id";
          $debug['sql2'][] = $sql;
          if ($value)
            $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php add_contact() q2')));
        }
      } else {
        foreach($v2 as $k => $v3) {
          foreach($v3 as $name => $value) {
            $sql = "INSERT INTO jayme.contact_fields (contact_id,type,name,value) VALUES ($contact_id,'$type','$name','$value')";
            $debug['sql2'][] = $sql;
            if ($value)
              $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php add_contact() q3')));
          }
        }
      }
    }
  }

  $debug['REQUEST'] = $_REQUEST;
  die(json_encode(array('msg' => 'Successfully updated contact.', 'debug' => $debug)));
}

function delete_contact() {
  global $mysqli;
  $sql = "DELETE FROM jayme.contacts WHERE id={$_REQUEST['contact_id']}";
  $debug['sql1'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php delete_contact() q1')));
  $sql = "DELETE FROM jayme.contact_fields WHERE contact_id={$_REQUEST['contact_id']}";
  $debug['sql2'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php delete_contact() q2')));
  $debug['REQUEST'] = $_REQUEST;
  die(json_encode(array('msg' => 'Successfully deleted contact.', 'debug' => $debug)));
}

function delete_field() {
  global $mysqli;
  $sql = "DELETE FROM jayme.contact_fields WHERE id={$_REQUEST['field_id']}";
  $debug['sql'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php delete_field()')));
  $debug['REQUEST'] = $_REQUEST;
  die(json_encode(array('msg' => 'Successfully deleted field.', 'debug' => $debug)));
}

function pull_contacts() {
  global $mysqli, $user_id;
  $debug['request'] = $_REQUEST;
  $clause = '';
  foreach($_REQUEST['filters'] as $k => $v) {
    if ($v) {
      if (in_array($k, array('hourly','daily'))) {
        if ($v = intval($v))
          $clause .= "AND c.$k {$_REQUEST['comp'][$k]} $v ";
      } elseif (in_array($k, array('email','phone','url')))
        $clause .= "AND cf.type='$k' AND cf.value LIKE '%$v%' ";
      else
        $clause .= "AND c.$k LIKE '%$v%' ";
    }
  }

  $sql = "SELECT c.* FROM jayme.contacts c
          LEFT JOIN jayme.contact_fields cf ON c.id=cf.contact_id
          WHERE c.user_id=$user_id $clause";
  $debug['sql1'] = $sql;
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'msg' => 'contacts_ajax.php pull_contacts() q1')))) {
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data[$row['id']] = $row;
    }
  }

  if ($data) {
    $sql = "SELECT * FROM jayme.contact_fields WHERE contact_id IN (".implode(',', array_keys($data)).")";
    $debug['sql2'] = $sql;
    if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'msg' => 'contacts_ajax.php pull_contacts() q2')))) {
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $data[$row['contact_id']]['fields'][$row['type']][$row['id']][$row['name']] = $row['value'];
      }
    }
  }

  die(json_encode(array('msg' => 'Successfully pulled contacts.', 'debug' => $debug, 'data' => $data)));
}
