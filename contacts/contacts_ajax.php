<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if (login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

if($_REQUEST['action']) {
  switch ($_REQUEST['action']) {
    case 'add_contact':
    case 'update_contact':
    case 'delete_contact':
      contact_action($_REQUEST['action']);
      break;
    case 'delete_field':
      delete_field();
      break;
    case 'pull_contacts':
      pull_contacts();
      break;
    case 'add_group':
    case 'delete_group':
      group_action($_REQUEST['action']);
      break;
    case 'pull_groups':
      pull_groups();
      break;
    default:
      break;
  }
}

function contact_action($action, $test = false) {
  global $mysqli, $user_id;
  $debug['REQUEST'] = $_REQUEST;
  $hourly = !is_nan($_REQUEST['hourly']) && $_REQUEST['hourly'] > 0 ? $_REQUEST['hourly'] : 0;
  $daily = !is_nan($_REQUEST['daily']) && $_REQUEST['daily'] > 0 ? $_REQUEST['daily'] : 0;
  $weekly = !is_nan($_REQUEST['weekly']) && $_REQUEST['weekly'] > 0 ? $_REQUEST['weekly'] : 0;
  if($action == 'add_contact') {
    $sql = "INSERT INTO jayme.contacts (user_id,first,last,title,first2,last2,title2,hourly,daily,weekly,reference,team,freelance,full_time,status,comments)
            VALUES ($user_id,'{$_REQUEST['first']}','{$_REQUEST['last']}','{$_REQUEST['title']}','{$_REQUEST['first2']}','{$_REQUEST['last2']}','{$_REQUEST['title2']}','$hourly','$daily','$weekly','{$_REQUEST['reference']}','{$_REQUEST['team']}','{$_REQUEST['freelance']}','{$_REQUEST['full_time']}','{$_REQUEST['status']}','{$_REQUEST['comments']}')";
  } elseif ($action == 'update_contact') {
    $sql = "UPDATE jayme.contacts SET first='{$_REQUEST['first']}', last='{$_REQUEST['last']}', title='{$_REQUEST['title']}', first2='{$_REQUEST['first2']}', last2='{$_REQUEST['last2']}', title2='{$_REQUEST['title2']}', reference='{$_REQUEST['reference']}', hourly='$hourly', daily='$daily', weekly='$weekly', team='{$_REQUEST['team']}', freelance='{$_REQUEST['freelance']}', full_time='{$_REQUEST['full_time']}', status='{$_REQUEST['status']}', comments='{$_REQUEST['comments']}' WHERE id={$_REQUEST['contact_id']}";
  } elseif ($action == 'delete_contact') {
    $sql = "DELETE FROM jayme.contacts WHERE id={$_REQUEST['contact_id']}";
  }
  $debug['sql1'] = $sql;
  if(!$test)
    $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php update_contact() q1')));

  if($action == 'add_contact') {
    $contact_id = mysqli_insert_id($mysqli) ? mysqli_insert_id($mysqli) : 0;
  } elseif ($action == 'update_contact') {
    $contact_id = $_REQUEST['contact_id'];
  } elseif ($action == 'delete_contact') {
    $sql = "DELETE FROM jayme.contact_fields WHERE contact_id={$_REQUEST['contact_id']}";
    $debug['sql2'] = $sql;
    $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php contact_action() q2')));
  }

  $debug['contact_id'] = $contact_id;
  $debug['update_fields'] = update_fields($contact_id, $_REQUEST['fields'], $test);
  die(json_encode(array('msg' => 'Successfully updated contact.', 'debug' => $debug, 'contact_id' => $contact_id)));
}

function update_fields($contact_id, $fields, $test = false) {
  global $mysqli;
  foreach ($fields as $type => $v) {
    if (in_array($type, array('email', 'phone','url'))) {
      foreach ($v as $field_id => $v2) {
        if ($field_id != 'new') {
          foreach ($v2 as $name => $value) {
            $sql = "UPDATE jayme.contact_fields SET name='$name', value='$value' WHERE id=$field_id";
            $debug['sql1'][] = $sql;
            if ($value && !$test)
              $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php update_fields() q1')));
          }
        } else {
          foreach ($v2 as $k => $v3) {
            foreach ($v3 as $name => $value) {
              $sql = "INSERT INTO jayme.contact_fields (contact_id,type,name,value) VALUES ($contact_id,'$type','$name','$value')";
              $debug['sql2'][] = $sql;
              if ($value && !$test)
                $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php update_fields() q2')));
            }
          }
        }
      }
    } elseif ($type == 'group') {
      foreach ($v as $field_id => $value) {
        if ($field_id != 'new') {
          $sql = "UPDATE jayme.contact_fields SET value='$value' WHERE id=$field_id";
          $debug['sql3'][] = $sql;
          if ($value && !$test)
            $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php update_fields() q3')));
        } else {
          foreach ($value as $new_group) {
            $sql = "INSERT INTO jayme.contact_fields (contact_id,type,value) VALUES ($contact_id,'group','$new_group')";
            $debug['sql4'][] = $sql;
            if ($new_group && !$test)
              $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php update_fields() q4')));
          }
        }
      }
    }
  }
  return $debug;
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
  foreach ($_REQUEST['filters'] as $k => $v) {
    if ($v) {
      if (in_array($k, array('hourly','daily','weekly'))) {
        if ($v = intval($v))
          $clause .= "AND c.$k {$_REQUEST['comp'][$k]} $v ";
      } elseif (in_array($k, array('email','phone','url'))) {
        $clause .= "AND cf.type='$k' AND cf.value LIKE '%$v%' ";
      } elseif (in_array($k, array('first','last','title'))) {
        $clause .= "AND c.$k LIKE '$v' OR c.{$k}2 LIKE '$v' ";
      } elseif ($k == 'groups') {
        $clause .= "AND (cf.type='group' AND cf.value IN (".implode(',', $v).')) ';
      } elseif (in_array($k, array('freelance','full_time'))) {
        $clause .= "AND c.$k=$v ";
      } elseif ($k == 'status') {
        $clause .= "AND c.status in ('".implode("','", $v)."') ";
      } elseif ($k == 'team' && $v == 1) {
        $clause .= "AND c.team=1 ";
      } else {
        $clause .= "AND c.$k LIKE '%$v%' ";
      }
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
      while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        if(in_array($row['type'], array('email','phone','url')))
          $data[$row['contact_id']]['fields'][$row['type']][$row['id']][$row['name']] = $row['value'];
        else {
          $group_ids[] = $row['value'];
          $data[$row['contact_id']]['fields'][$row['type']][$row['id']] = $row['value'];
        }
      }
    }
  }
  $groups = pull_groups(true);
  die(json_encode(array('msg' => 'Successfully pulled contacts.', 'debug' => $debug, 'data' => $data, 'groups' => $groups)));
}

function group_action($action, $test = false) {
  global $mysqli, $user_id;
  if ($action == 'add_group') {
    $sql = "INSERT INTO jayme.groups (user_id,group_name) VALUES ($user_id,'{$_REQUEST['group_name']}')";
  } elseif ($action == 'delete_group') {
    $sql = "DELETE FROM jayme.groups WHERE id={$_REQUEST['group_id']}";
  }
  $debug['sql'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'err' => 'contacts_ajax.php group_action()')));
  die(json_encode(array('msg' => 'Successfully updated group.', 'debug' => $debug)));
}

function pull_groups($return = false) {
  global $mysqli, $user_id;
  $debug['request'] = $_REQUEST;
  $sql = "SELECT id,group_name FROM jayme.groups WHERE user_id=$user_id ORDER BY group_name";
  $debug['sql1'] = $sql;
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'debug' => $debug, 'sql' => $sql, 'msg' => 'contacts_ajax.php pull_groups() q1')))) {
    $i = 0;
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $data[$i]['id'] = $row['id'];
      $data[$i]['group_name'] = $row['group_name'];
      $i++;
    }
  }
  if($return)
    return $data;
  else
    die(json_encode(array('msg' => 'Successfully pulled groups.', 'debug' => $debug, 'data' => $data)));
}
