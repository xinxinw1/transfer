<?php session_start(); ?>
<?php require "functions.php"; ?>
<?php
check_login();

try {
  $link = connect_db();
} catch (Exception $e){
  $_SESSION['mess2'] = $e->getMessage();
  redirect(".");
}

$id = mysqli_real_escape_string($link, $_POST['deleteid']);

$sql = "SELECT name FROM transfer WHERE id='$id'";
if (!($result = mysqli_query($link, $sql))){
  $_SESSION['mess2'] = "Couldn't access database: " . mysqli_error($link);
  redirect(".");
}
if (!($row = mysqli_fetch_assoc($result))){
  $_SESSION['mess2'] = "File doesn't exist";
  redirect(".");
}

$name = $row['name'];
$sql = "DELETE FROM transfer WHERE id='$id'";
if (!mysqli_query($link, $sql)){
  $_SESSION['mess2'] = "Couldn't delete data from database: " . mysqli_error($link);
  redirect(".");
}
unlink("files/$id");
$_SESSION['mess2'] = "Deleted \"$name\"";
redirect(".");
?>
