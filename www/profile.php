<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("profile");
    // writeNavbar();
    if (isset($_GET["uid"])) {
      $uid = $_GET["uid"];
    } else {
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      session_start();
      $uid = $db->getUserID($_SESSION["user"]);
      session_write_close();
    }
    require_once("inc/functions.php");
    ?>
    <div class="container" style="padding-top:76px;">
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
