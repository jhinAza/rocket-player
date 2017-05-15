<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  require_once("inc/databaseController.php");
  if (isUserLoggedIn()) {
    writeHeader("home");
    writeNavbar();
    ?>
      <div class="container" style="padding-top:76px;">
        <div class="panel-group">
          <?php
            $db = new DatabaseController();
            session_start();
            $uid = $db->getUserID($_SESSION["user"]);
            session_write_close();
            $follows = $db->getFollows($uid);
            if ($follows) {
              foreach ($follows as $follow) {
                if ($db->userHasVideos($follow["id"])) {
                  ?>
                  <div class="panel panel-info">
                    <div class="panel-heading">
                      <?php print($follow["username"]); ?>
                    </div>
                    <div class="panel-body">
                      <?php getRowOfUploaded($follow["id"]) ?>
                    </div>
                  </div>
                  <?php
                }
              }
            }
          ?>
        </div>
      </div>
    <?php
  } else {
    header("Location:/login.php");
  }
  writeFooter();
?>
