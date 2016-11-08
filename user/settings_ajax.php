<?php
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root.'/db/db_connect.php';
include_once $root.'/login/functions.php';
sec_session_start();
if(login_check($mysqli) == false)
  header('Location: /login/login.php?error=1');
$user_id = $_SESSION['user_id'];

switch ($_REQUEST['action']) {
	case 'delete_data':
		delete_data();
		break;
	default:
		break;
}

function delete_data() {
  global $mysqli, $user_id;
  $sql = "SELECT id FROM jayme.contacts WHERE user_id=$user_id";
  $debug['sql1'] = $sql;
  if ($result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'msg' => 'settings_ajax.php pull_contacts() q1')))) {
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
      $contacts[] = $row['id'];
    }
  }
  $sql = "DELETE FROM jayme.contacts WHERE user_id=$user_id";
  $debug['sql2'] = $sql;
  $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'err' => 'settings_ajax.php delete_data() q2')));
  if($contacts) {
    $sql = "DELETE FROM jayme.contact_fields WHERE contact_id IN (".implode(',', $contacts).")";
    $debug['sql3'] = $sql;
    $result = $mysqli->query($sql) or die(json_encode(array('msg' => mysqli_error($mysqli), 'sql' => $sql, 'err' => 'settings_ajax.php delete_data() q3')));
  }
  die(json_encode(array('msg' => 'Successfully deleted all data.')));
}
