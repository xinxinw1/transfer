<?php
$passfile = "transfer-pass";

function redirect($a){
  header("Location: $a");
  die();
}

function reload(){
  redirect($_SERVER['REQUEST_URI']);
}

function connect_db(){
  if (!file_exists("authinfo.php"))throw new Exception("authinfo.php doesn't exist");
  require "authinfo.php";
  $link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  if (mysqli_connect_errno()){
    throw new Exception("Couldn't connect to database: " . mysqli_connect_error());
  }
  return $link;
}

function check_login(){
  global $passfile;
  
  if (!isset($_SESSION['transfer-priv'])){
    redirect("login");
  }
  
  $exists = file_exists($passfile);
  
  if ($exists && isset($_SESSION['transfer-nopass'])){
    unset($_SESSION['transfer-priv']);
    unset($_SESSION['transfer-nopass']);
    redirect("login");
  }
  
  if (!$exists && !isset($_SESSION['transfer-nopass'])){
    $_SESSION['transfer-nopass'] = true;
  }
}
?>
