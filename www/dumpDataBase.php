<?php
  require_once("inc/functions.php");
  require_once("inc/databaseController.php");
  $db = new DatabaseController();
  session_start();
  $user = $_SESSION["user"];
  session_write_close();
  if (isUserLoggedIn() && $db->isUserAdmin($user)) {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      writeHeader("Dump");
      writeNavbar();
      ?>
      <div class="container" style="padding-top: 76px;">
        <form action="dumpDataBase.php" method="post" enctype="multipart/form-data">
          <input type="file" name="schema" id="schema">
          <input type="submit" name="send" value="Enviar">
        </form>
      </div>
      <?php
    } else {
      if ($_FILES["schema"]) {
        // print($_FILES["schema"]["tmp_name"]);
        $file = file_get_contents($_FILES["schema"]["tmp_name"]);
        $file = str_replace("{{dabatase}}", $db->name, $file);
        $db->executeQuery($file);
        deleteDirectory("res");
        header("Location: /login.php");
      }
    }
  } else {
    header("Location: /http404.php");

  }
 ?>
