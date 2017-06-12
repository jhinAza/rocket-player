<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  require_once("inc/databaseController.php");
  if (isUserLoggedIn()) {
    writeHeader("home");
    $db = new DatabaseController();
    ?>
      <div class="container" style="padding-top:76px;">
        <div class="row">
          <div class="col-md-6">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4 class="panel-title">Videos</h4>
              </div>
              <div class="panel-body">
                <?php
                $list = ($db->getVideosByQuery($_GET["query"]));
                foreach ($list as $item) {
                  ?>
                  <div class="col-md-6 item">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="row">
                          <h4>
                            <a href=<?php print("/player.php?video=".$item["id"]) ?>>
                              <?php print($item["videoname"]) ?>
                            </a>
                          </h4>
                        </div>
                        <div class="row">
                          <h5><?php print($db->getUserName($item["userid"])) ?></h5>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <img src=<?php print(insertVideoImg($video["videoID"])) ?> style="width:100%">

                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h4 class="panel-title">Usuarios</h4>
              </div>
              <div class="panel-body">
                <?php
                $list = ($db->getUsersByQuery($_GET["query"]));
                foreach ($list as $item) {
                  ?>
                  <div class="col-md-6 item user">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="row">
                          <h4>
                            <a href=<?php print("/profile.php?uid=".$item["id"]) ?>>
                              <?php print($item["username"]) ?>
                            </a>
                          </h4>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <img src=<?php print(insertVideoImg($video["videoID"])) ?> style="width:100%">

                      </div>
                    </div>
                  </div>
                  <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
  } else {
    header("Location:/login.php");
  }
  writeFooter();
?>
