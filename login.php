<?php session_start(); ?>
<?php $ver = "2.1.0"; ?>
<?php require "functions.php"; ?>
<?php
if (isset($_SESSION['transfer-priv'])){
  redirect(".");
}

if (isset($_SESSION['mess1'])){
  $mess = $_SESSION['mess1'];
  unset($_SESSION['mess1']);
}

if (isset($_SESSION['mess2'])){
  $mess = $_SESSION['mess2'];
  unset($_SESSION['mess2']);
}

if (isset($_SESSION['mess'])){
  $mess = $_SESSION['mess'];
  unset($_SESSION['mess']);
}

if (!file_exists($passfile)){
  $_SESSION['transfer-priv'] = true;
  redirect(".");
}

if (isset($_POST['pass'])){
  $pass = $_POST['pass'];
  if (password_verify($pass, file_get_contents($passfile))){
    $_SESSION['transfer-priv'] = true;
    redirect(".");
    
  } else {
    $_SESSION['mess'] = "Incorrect password";
    reload();
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>File Transfer <?php echo $ver ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="transfer.css" rel="stylesheet">
</head>

<body>
  <div id="cont">
    <div id="main">
      <h1>File Transfer <?php echo $ver ?></h1>
      <div class="table other">
        <form method="post">
          <div>Password:</div>
          <div><input type="password" name="pass"></div>
          <div><input type="submit" value="Login"></div>
        </form>
      </div>
      <?php if (isset($mess)){ ?>
      <p><?php echo $mess ?></p>
      <?php } ?>
    </div>
  </div>
</body>

</html>
