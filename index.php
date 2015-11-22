<?php header("Cache-Control: no-cache"); ?>
<?php session_start(); ?>
<?php $ver = "0.1"; ?>
<?php
function reload(){
  header("Location: .");
  die();
}

if (isset($_SESSION['mess'])){
  $mess = $_SESSION['mess'];
  unset($_SESSION['mess']);
}
if (isset($_SESSION['mess1'])){
  $mess1 = $_SESSION['mess1'];
  unset($_SESSION['mess1']);
}
if (isset($_SESSION['mess2'])){
  $mess2 = $_SESSION['mess2'];
  unset($_SESSION['mess2']);
}
error_reporting(0);
if (!file_exists("authinfo.php"))$mess = "authinfo.php doesn't exist";
else {
  require "authinfo.php";
  $passfile = "transfer-pass";
  if (!file_exists($passfile)){
    $_SESSION['transfer-priv'] = "yes";
    $nopass = true;
  }
  if (isset($_POST['pass'])){
    $pass = $_POST['pass'];
    if (!file_exists($passfile) || password_verify($pass, file_get_contents($passfile))){
      $_SESSION['transfer-priv'] = "yes";
      reload();
    } else {
      $_SESSION['mess'] = "Incorrect password";
      reload();
    }
  }
  if (isset($_SESSION['transfer-priv'])){
    if (isset($_POST['newpass'])){
      $pass = $_POST['newpass'];
      if ($pass == ""){
        unlink($passfile);
        $_SESSION['mess1'] = "Unset password";
        reload();
      } else {
        file_put_contents($passfile, password_hash($pass, PASSWORD_DEFAULT));
        $_SESSION['mess1'] = "Changed password";
        reload();
      }
    }
    if (isset($_GET['logout'])){
      session_destroy();
      reload();
    }
    
    $link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    if (mysqli_connect_errno()){
      $mess = "Couldn't connect to database: " . mysqli_connect_error();
    } else {
      if (isset($_POST['upload'])){
        $name = mysqli_real_escape_string($link, $_FILES['file']['name']);
        $tmp = $_FILES['file']['tmp_name'];
        $err = $_FILES['file']['error'];
        if ($err != 0){
          $_SESSION['mess1'] = "File upload error: $err";
          reload();
        }
        $sql = "INSERT INTO transfer (name) VALUES ('$name')";
        if (!mysqli_query($link, $sql)){
          $_SESSION['mess1'] = "Couldn't insert data into database: " . mysqli_error($link);
          reload();
        }
        $id = mysqli_insert_id($link);
        if (!is_dir("files/")){
          if (!mkdir("files/")){
            $_SESSION['mess1'] = "Couldn't make files directory";
            reload();
          }
        }
        if (!move_uploaded_file($tmp, "files/$id")){
          $_SESSION['mess1'] = "Couldn't upload file";
          reload();
        }
        $_SESSION['mess1'] = "Uploaded successfully";
        reload();
      }
      
      if (isset($_GET['getfile'])){
        $id = mysqli_real_escape_string($link, $_GET['getfile']);
        
        $sql = "SELECT name FROM transfer WHERE id='$id'";
        if (!($result = mysqli_query($link, $sql))){
          $_SESSION['mess2'] = "Couldn't access database: " . mysqli_error($link);
          reload();
        }
        if (!($row = mysqli_fetch_assoc($result))){
          $_SESSION['mess2'] = "File doesn't exist";
          reload();
        }
        $name = $row['name'];
        $file = "files/$id";
        // http://stackoverflow.com/questions/7263923/how-to-force-file-download-with-php
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"$name\"");
        header('Content-Length: ' . filesize($file));
        readfile($file);
        die();
      }
      
      if (isset($_POST['deleteid'])){
        $id = mysqli_real_escape_string($link, $_POST['deleteid']);
        
        $sql = "SELECT name FROM transfer WHERE id='$id'";
        if (!($result = mysqli_query($link, $sql))){
          $_SESSION['mess2'] = "Couldn't access database: " . mysqli_error($link);
          reload();
        }
        if (!($row = mysqli_fetch_assoc($result))){
          $_SESSION['mess2'] = "File doesn't exist";
          reload();
        }
        
        $name = $row['name'];
        $sql = "DELETE FROM transfer WHERE id='$id'";
        if (!mysqli_query($link, $sql)){
          $_SESSION['mess2'] = "Couldn't delete data from database: " . mysqli_error($link);
          reload();
        }
        unlink("files/$id");
        $_SESSION['mess2'] = "Deleted \"$name\"";
        reload();
      }
      
      $sql = "SELECT date, name, id FROM transfer";
      if (!($result = mysqli_query($link, $sql))){
        $mess = "Couldn't get data from database: " . mysqli_error($link);
        mysqli_close($link);
      }
    }
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>File Transfer <?php echo $ver ?></title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
  * {margin: 0; padding: 0; border: 0; font-family: "Courier New", "DejaVu Sans Mono", monospace;}
  #main {margin: 10px; margin-right: 16px;}
  h1, h2, p {margin-bottom: 10px;}
  h1 {font-size: 25px}
  input {border: 1px solid #000; height: 18px; width: 218px; padding: 1px 2px;}
  input[type=submit] {background-color: #F1EFEB; padding: 1px 10px; width: 70px; height: 22px;}
  input[type=submit]:hover {background-color: #E6E3DE;}
  input[type=file] {border: 0; padding: 0; height: 22px; width: 224px;}
  a {color: #005ec8;}
  
  span.buf {display: inline-block; height: 1px; width: 5px;}
  
  table {border-collapse: collapse; vertical-align: middle;}
  
  .table > div, .table > form {display: table-row; vertical-align: middle;}
  .table > div > div, .table > form > div {display: table-cell;}
  
  .table.border > div > div, .table.border > form > div {border-left: 1px solid #000; border-top: 1px solid #000;}
  .table.border > div > div:last-child, .table.border > form > div:last-child {border-right: 1px solid #000;}
  .table.border > div:last-child > div, .table.border > form:last-child > div {border-bottom: 1px solid #000;}
  
  .other {display: table;}
  .other > form > div {padding-left: 5px; padding-bottom: 10px; line-height: 22px;}
  .other > form > div:first-child {padding-left: 0;}
  
  .results {margin-bottom: 10px}
  .results > form > div {padding: 5px 7px;}
  .results > form > div:last-child {padding: 5px;}
  </style>
</head>

<body>
  <div id="main">
    <h1>File Transfer <?php echo $ver ?><?php if (isset($_SESSION['transfer-priv']) && !isset($nopass)){ ?> | <a href="?logout=true">Logout</a><?php } ?></h1>
    <?php if (isset($_SESSION['transfer-priv'])){ ?>
      <div class="table other">
        <form method="post">
          <div>New password:</div>
          <div><input type="password" name="newpass"></div>
          <div><input type="submit" value="Set"></div>
        </form>
        <form method="post" enctype="multipart/form-data">
          <div>Upload:</div>
          <div><input type="file" name="file"></div>
          <div><input type="submit" name="upload" value="Upload"></div>
        </form>
      </div>
      <?php if (isset($mess1)){ ?><p><?php echo $mess1 ?></p><?php } ?>
      <h2>Uploaded Files</h2>
      <?php if (isset($mess2)){ ?><p><?php echo $mess2 ?></p><?php } ?>
      <div class="table border results">
        <?php
        if (isset($result)){
          while ($row = mysqli_fetch_assoc($result)){
        ?>
        <form method="post" onsubmit="if (confirm('Are you sure you want to delete ' + this.deletename.value + ', id ' + this.deleteid.value + '?'))return true; else return false;">
          <div><?php echo $row["id"] ?></div>
          <div><?php echo $row["date"] ?></div>
          <div><a href="?getfile=<?php echo $row["id"] ?>"><?php echo $row["name"] ?></a></div>
          <div><input type="hidden" name="deleteid" value="<?php echo $row["id"] ?>"><input type="hidden" name="deletename" value="<?php echo $row["name"] ?>"><input type="submit" value="Delete"></div>
        </form>
        <?php
          }
          mysqli_free_result($result);
        }
        ?>
      </div>
    <?php } else { ?>
    <div class="table other">
      <form method="post">
        <div>Password:</div>
        <div><input type="password" name="pass"></div>
        <div><input type="submit" value="Login"></div>
      </form>
    </div>
    <?php } ?>
    <?php if (isset($mess)){ ?><p><?php echo $mess ?></p><?php } ?>
  </div>
</body>

</html>
