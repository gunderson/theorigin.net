<?php
include('functions.inc.php');
if ($_POST['do'] == 'login'){
  $result = login($_POST['username'], $_POST['password']);
  if (!$result){
    $error = 0;
    $message = "You Are Logged in as $username .\n<br>";
    $message .= "Your Session is:" . session_id();
  } else {
    $error = 1;
    $message = "You Ain\'t Logged in.";
  }
  echo $message;
  echo $result;
}
?>
