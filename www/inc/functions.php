<?php
  // This file contains the needed functions for anything that can appear in any page
  function writeHeader($title, $type=false) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <?php echo "<title>$title - project</title>" ?>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="/static/librerias/js/jquery.min.js" charset="utf-8"></script>
      <script src="/static/librerias/js/bootstrap.min.js" charset="utf-8"></script>
      <?php
        if ($type == "upload") {
          ?>
          <script src="http://malsup.github.com/jquery.form.js" charset="utf-8"></script>
          <script src="/static/js/upload.min.js" charset="utf-8"></script>
          <script src="/static/librerias/js/url.min.js" charset="utf-8"></script>
          <?php
        } elseif ($type == "player") {
          ?>
          <script src="/static/js/videoController.js" charset="utf-8"></script>
          <script src="/static/js/video.min.js" charset="utf-8"></script>
          <script src="/static/librerias/js/url.min.js" charset="utf-8"></script>
          <?php
        } elseif ($type == "profile") {
          ?>
          <script src="http://malsup.github.com/jquery.form.js" charset="utf-8"></script>
          <script src="/static/librerias/js/url.min.js" charset="utf-8"></script>
          <?php
        } elseif ($type == "login") {
          ?>
          <script src="/static/librerias/js/url.min.js" charset="utf-8"></script>
          <?php
        }
      ?>
      <script src="/static/js/main.min.js" charset="utf-8"></script>
      <?php
        if (isUserLoggedIn()) {
          require_once("inc/userSettingsReader.php");
          session_start();
          $reader = new UserSettingsReader($_SESSION["user"]);
          session_write_close();
          $theme = $reader->readTheme();
          if ($theme === "day") {
            ?>
            <link rel="stylesheet" href="/static/librerias/css/day.min.css">
            <link rel="stylesheet" href="/static/css/day.min.css">
            <?php
          } else {
            ?>
            <link rel="stylesheet" href="/static/librerias/css/night.min.css">
            <link rel="stylesheet" href="/static/css/night.css">
            <?php
          }
        } else {
          ?>
          <link rel="stylesheet" href="/static/librerias/css/night.min.css">
          <link rel="stylesheet" href="/static/css/night.css">
          <?php
        }
      ?>
    </head>
    <body>
    <?php
    if ($type != "login" and $type != "error") {
      writeNavbar();
    }
    ?>
    <div class="container" style="padding-top:76px">
      <div id="alert-pos"></div>
    <?php
  }

  function writeNavbar(){
    require_once("static/html/header.html");
  }

  function writeFooter() {
    ?>
        </body>
      </html>
    <?php
  }

  function isUserLoggedIn() {
    session_start();
    if (isset($_SESSION["user"]) && isset($_SESSION["UID"])) {
      $user = $_SESSION["user"];
      $uid = $_SESSION["UID"];
      session_write_close();
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      $isValid = $db->checkUserUID($user,$uid);
      return $isValid;
    }
    session_write_close();
    return false;
  }

  function debug($file,$function,$line) {
    // debug(__FILE__,__FUNCTION__,__LINE__);
    print("<H1>$file-$function-$line</H1>");
    error_log("<H1>$file-$function-$line</H1>");
  }

  function deleteDirectory($dir){
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
  }

  function getRowOfHistory($uid, $start=0) {
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $row = $db->getHistory($uid, $start, 3);
    if ($row) {
      print('<div class="row">');
      foreach ($row as $video) {
        ?>
        <div class="col-md-4 item">
          <div class="row">
            <div class="col-md-6 video-data">
              <div class="row">
                <h4>
                  <a href=<?php print("/player.php?video=".$video["videoID"]) ?>>
                    <?php print($video["videoname"]) ?>
                  </a>
                </h4>
              </div>
              <div class="row">
                <h5><?php print($video["creator"]) ?></h5>
              </div>
            </div>
            <div class="col-md-6">
              <img src=<?php print('"/res/img/videos/'.$video["img"].'"') ?> style="width:100%">
            </div>
          </div>
        </div>
        <?php
      }
      print("</div>");
    }
  }

  function getRowOfUploaded($uid, $start=0, $offset=6, $col_size=4) {
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $row = $db->getUserVideos($uid, $start, $offset);
    if ($row) {
      print('<div class="row">');
      foreach ($row as $video) {
        ?>
        <?php
          print('<div class="col-md-'.$col_size.' item">')
        ?>
          <div class="row">
            <div class="col-md-6 video-data">
              <div class="row">
                <h4>
                  <a href=<?php print("/player.php?video=".$video["id"]) ?>>
                    <?php print($video["videoname"]) ?>
                  </a>
                </h4>
              </div>
            </div>
            <div class="col-md-6">
              <img src=<?php print('"/res/img/videos/'.$video["videoimg"].'"'); ?> alt="" style="width:100%">
            </div>
          </div>
        </div>
        <?php
      }
      print("</div>");
    }
  }

  function getUserPreferredSubtitlesFile ($user, $video) {
    require_once("inc/userSettingsReader.php");
    $reader = new UserSettingsReader($user);
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $file = $db->getSubtitlesFile($video, $reader->getLang("1"));
    if ($file) {
      return "res/subs/".$file;
    }
    return false;
  }

  function getUserPreferredTranscriptionFile ($user, $video) {
    require_once("inc/userSettingsReader.php");
    $reader = new UserSettingsReader($user);
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $file = $db->getTranscriptionFile($video, $reader->getLang("1"));
    if ($file) {
      return "res/trans/".$file;
    }
    return false;
  }

  function getUserPreferredSignLanguageVideo ($user, $video) {
    require_once("inc/userSettingsReader.php");
    $reader = new UserSettingsReader($user);
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $file = $db->getSignLangVideo($video, $reader->getLang("1"));
    if ($file) {
      return "res/signal/".$file;
    }
    return false;
  }

  function getColOfRecommendations($video, $offset=0, $limit=10) {
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $data = $db->getRecomendationFromVideo($video, $offset, $limit);
    if ($data) {
      foreach ($data as $video_data) {
        ?>
        <div class="panel panel-info">
          <div class="panel-body">
            <div class='row'>
              <div class="col-xs-4 video-thumb">
                <img src=<?php print('/res/img/videos/"'.$video_data["videoimg"].'"') ?>>
              </div>
              <div class="col-md-8">
                <a href=<?php print("player.php?video=".$video_data["id"]) ?>>
                  <?php print($video_data["videoname"]) ?>
                </a>
                <br>
                <a href=<?php print("profile.php?uid=".$video_data["userid"]) ?>>
                  <?php print($db->getUserName($video_data["userid"])) ?>
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php
      }
    } else {
      print("No hay mas videos para recomendar");
    }
  }
?>
