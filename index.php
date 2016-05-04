<?php session_start(); ?>
<?php $ver = "2.0.0"; ?>
<?php require "functions.php"; ?>
<?php
check_login();

if (isset($_SESSION['mess1'])){
  $mess1 = $_SESSION['mess1'];
  unset($_SESSION['mess1']);
}
if (isset($_SESSION['mess2'])){
  $mess2 = $_SESSION['mess2'];
  unset($_SESSION['mess2']);
}

if (isset($_GET["success"])) {
  $_SESSION['mess2'] = "Uploaded successfully";
  redirect(".");
}

$nopass = isset($_SESSION['transfer-nopass']);

try {
  $link = connect_db();
} catch (Exception $e){
  $mess2 = $e->getMessage();
}

if (isset($link)){
  $sql = "SELECT date, name, id FROM transfer";
  if (!($result = mysqli_query($link, $sql))){
    $mess2 = "Couldn't get data from database: " . mysqli_error($link);
    mysqli_close($link);
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
  <link href="fine-uploader/fine-uploader-new.min.css" rel="stylesheet">
</head>

<body>
  <div id="cont">
    <div id="main">
      <h1>File Transfer <?php echo $ver ?><?php if (!$nopass){ ?> | <a href="logout">Logout</a><?php } ?></h1>
        <div class="table other">
          <form method="post" action="setpass">
            <div>New password:</div>
            <div><input type="password" name="newpass"></div>
            <div><input type="submit" value="Set"></div>
          </form>
        </div>
        <div id="fine-uploader"></div>
        <?php if (isset($mess1)){ ?>
        <p><?php echo $mess1 ?></p>
        <?php } ?>
        <h2>Uploaded Files</h2>
        <?php if (isset($mess2)){ ?>
        <p><?php echo $mess2 ?></p>
        <?php } ?>
        <div class="table border results">
          <?php
          if (isset($result)){
            while ($row = mysqli_fetch_assoc($result)){
          ?>
          <form method="post" action="delete" onsubmit="if (confirm('Are you sure you want to delete ' + this.deletename.value + ', id ' + this.deleteid.value + '?'))return true; else return false;">
            <div><?php echo $row["id"] ?></div>
            <div><?php echo $row["date"] ?></div>
            <div><a href="get?id=<?php echo $row["id"] ?>"><?php echo $row["name"] ?></a></div>
            <div><input type="hidden" name="deleteid" value="<?php echo $row["id"] ?>"><input type="hidden" name="deletename" value="<?php echo $row["name"] ?>"><input type="submit" value="Delete"></div>
          </form>
          <?php
            }
            mysqli_free_result($result);
          }
          ?>
        </div>
    </div>
  </div>
  
  <script type="text/template" id="qq-template">
    <div class="qq-uploader-selector qq-uploader xuploader" qq-drop-area-text="Drop files here">
      <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
      </div>
      <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
        <span class="qq-upload-drop-area-text-selector"></span>
      </div>
      <div class="qq-upload-button-selector qq-upload-button">
        <div>Upload a file</div>
      </div>
        <span class="qq-drop-processing-selector qq-drop-processing">
          <span>Processing dropped files...</span>
          <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
      <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
        <li>
          <div class="qq-progress-bar-container-selector">
            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
          </div>
          <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
          <span class="qq-upload-file-selector qq-upload-file"></span>
          <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
          <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
          <span class="qq-upload-size-selector qq-upload-size"></span>
          <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
          <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
          <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button>
          <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
        </li>
      </ul>

      <dialog class="qq-alert-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
          <button type="button" class="qq-cancel-button-selector">Close</button>
        </div>
      </dialog>

      <dialog class="qq-confirm-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <div class="qq-dialog-buttons">
          <button type="button" class="qq-cancel-button-selector">No</button>
          <button type="button" class="qq-ok-button-selector">Yes</button>
        </div>
      </dialog>

      <dialog class="qq-prompt-dialog-selector">
        <div class="qq-dialog-message-selector"></div>
        <input type="text">
        <div class="qq-dialog-buttons">
          <button type="button" class="qq-cancel-button-selector">Cancel</button>
          <button type="button" class="qq-ok-button-selector">Ok</button>
        </div>
      </dialog>
    </div>
  </script>
  
  <script src="fine-uploader/fine-uploader.min.js"></script>
  <script>
  var uploader = new qq.FineUploader({
    debug: true,
    element: document.getElementById('fine-uploader'),
    request: {
      endpoint: "upload"
    },
    chunking: {
      enabled: true,
      concurrent: {
        enabled: true
      },
      success: {
        endpoint: "upload?done"
      }
    },
    resume: {
      enabled: true
    },
    retry: {
      enableAuto: false,
      showButton: true
    },
    callbacks: {
      onAllComplete: function (success, failed){
        if (failed.length == 0)location.replace("?success");
      }
    }
  });
  </script>
</body>

</html>
