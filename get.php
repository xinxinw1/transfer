<?php session_start(); ?>
<?php require "functions.php"; ?>
<?php
try {
  $link = connect_db();
} catch (Exception $e){
  $_SESSION['mess2'] = $e->getMessage();
  redirect(".");
}

$id = mysqli_real_escape_string($link, $_GET['id']);

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
$file = "files/$id";
// http://stackoverflow.com/questions/7263923/how-to-force-file-download-with-php
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"$name\"");
header('Content-Length: ' . filesize($file));
readfile($file);
?>
