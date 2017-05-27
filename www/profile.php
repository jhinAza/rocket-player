<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("profile", "profile");
    writeNavbar();
    session_start();
    $db = new DatabaseController();
    if (isset($_GET["uid"])) {
      $uid = $_GET["uid"];
      $isOwnProfile = ($uid == $db->getUserID($_SESSION["user"]));
    } else {
      $uid = $db->getUserID($_SESSION["user"]);
      $isOwnProfile = true;
      require_once("inc/databaseController.php");
    }
    session_write_close();
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
                  <?php if ($isOwnProfile): ?>
                    <form id="img-form" method="post">
                      <input type="file" name="img-file" id="img-file">
                    </form>
                  <?php endif; ?>
                  <?php if ($db->userHasProfileImage($uid)): ?>
                    <img src=<?php print('"/res/img/users/'.$db->getUserProfileImage($uid).'"'); ?> class="resp-img user-img" alt="Cambiar imagen">
                  <?php else: ?>
                    <img src="/static/img/user.png" class="resp-img user-img" alt="Cambiar imagen">
                  <?php endif; ?>
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
                    <?php if (!$isOwnProfile): ?>
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
                  $followers = $db->getFollowers($uid);
                  if ($followers) {
                    foreach ($followers as $follower) {
                      ?>
                      <li class="list-group-item">
                        <a href=<?php print("/profile.php?uid=".$follower["id"]); ?>><?php print($follower["username"]); ?></a>
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
                  $follows = $db->getFollows($uid);
                  if ($follows) {
                    foreach ($follows as $follow) {
                      ?>
                      <li class="list-group-item">
                        <a href=<?php print("/profile-php?uid=".$follow["id"]); ?>><?php print($follow["username"]); ?></a>
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
