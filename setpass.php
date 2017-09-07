<?php session_start(); ?>
<?php require "functions.php"; ?>
<?php
if (isset($_POST['newpass'])){
  $pass = $_POST['newpass'];
  if ($pass == ""){
    file_put_contents($passfile, "");
    $_SESSION['mess1'] = "Unset password";
    redirect(".");
  } else {
    file_put_contents($passfile, password_hash($pass, PASSWORD_DEFAULT));
    $_SESSION['mess1'] = "Changed password";
    redirect(".");
  }
}
redirect(".");
?>
