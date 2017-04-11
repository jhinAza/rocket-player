<?php
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    session_start();
    $user = $_SESSION["user"];
    $uid = $_SESSION["UID"];
    session_write_close();
    require_once("inc/functions.php");
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    if (isUserLoggedIn() && $db->isUserAdmin($user)) {
      writeHeader("truncate");
      writeNavbar();
      $rows = $db->getAllTables();
      ?>
        <div class="container" style="padding-top:76px">
          <div class="well">
            <div class="row">
              <?php
              foreach ($rows as $row) {
                ?>
                <div class="col-md-2 col-sm-6">
                  <div class="check-list">
                    <input type="checkbox"  value="<?php print($row[0]) ?>" >
                    <?php print($row[0]); ?>
                  </div>
                </div>
                <?php
              }
              ?>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-md-push-3">
              <div class="btn-group btn-group-justified">
                <a href="#" id="check" class="btn btn-info">Check all</a>
                <a href="#" id="uncheck" class="btn btn-info">Uncheck all</a>
                <a href="#" id="send" class="btn btn-warning">Enviar</a>
              </div>
            </div>
          </div>
        </div>
      <?php
    } else {
      header("Location: http404.php");
    }
  } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    $user = $_SESSION["user"];
    $uid = $_SESSION["UID"];
    session_write_close();
    require_once("inc/functions.php");
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    if (isUserLoggedIn() && $db->isUserAdmin($user)) {
      print_r($_POST["tables"]);
      // TODO: Crear una funcion en el controlador de la base de datos para truncar
      // una tabla que se le pasa como parametro
      foreach ($_POST["tables"] as $table) {
        error_log($db->truncateTable($table));
      }
    }
  }
?>
