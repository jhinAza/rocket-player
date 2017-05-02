<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("profile", "profile");
    writeNavbar();
    $db = new DatabaseController();
    if (isset($_GET["uid"])) {
      $uid = $_GET["uid"];
    } else {
      require_once("inc/databaseController.php");
      session_start();
      $uid = $db->getUserID($_SESSION["user"]);
      session_write_close();
    }
    require_once("inc/functions.php");
    ?>
    <div class="container" style="padding-top:76px;">
      <div class="row">
        <!-- Here should go the info of the user -->
        <div class="col-xs-12">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="row">
                <div class="col-md-2">
                  <img src="/static/img/user.png" class="resp-img">
                </div>
                <div class="col-md-10">
                  <div class="row">
                    <h4>
                      <a href="#">
                        <?php print($db->getUserName($uid)); ?>
                      </a>
                    </h4>
                  </div>
                  <div class="row">
                    <?php if (isset($_GET["uid"])): ?>
                      <?php if ($db->isFollowing($_SESSION["user"], $_GET["uid"]) == "false"): ?>
                        <button type="button" name="follow" id="follow" class="btn btn-info" data-following="false">Seguir!</button>
                      <?php else: ?>
                        <button type="button" name="follow" id="follow" class="btn btn-warning" data-following="true">Dejar de seguir!</button>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-title">Videos subidos</h4>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-title">Seguidores</h4>
            </div>
            <div class="panel-body">
              <ul class="list-group">
                <?php
                  $followers = $db->getFollowers($_GET["uid"]);
                  if ($followers) {
                    foreach ($followers as $follower) {
                      ?>
                      <li class="list-group-item">
                        <a href=<?php print("/profile?uid=".$follower["id"]); ?>><?php print($follower["username"]); ?></a>
                      </li>
                      <?php
                    }
                  }
                ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">Siguiendo</h4>
            </div>
            <div class="panel-body">
              <ul class="list-group">
                <?php
                  $follows = $db->getFollows($_GET["uid"]);
                  if ($follows) {
                    foreach ($follows as $follow) {
                      ?>
                      <li class="list-group-item">
                        <a href=<?php print("/profile?uid=".$follow["id"]); ?>><?php print($follow["username"]); ?></a>
                      </li>
                      <?php
                    }
                  }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h4 class="panel-tittle"> Historial </h4>
            </div>
            <div class="panel-body">
              <?php getRowOfHistory($uid); ?>
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
