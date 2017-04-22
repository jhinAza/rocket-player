<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
        require_once("inc/databaseController.php");
        session_start();
        $db = new databaseController();
        $db->saveComment($_COOKIE["video"], $_SESSION["user"], $_POST["comment"]);
        die();
      }
    }
  }
  http_response_code(404);
  die();
?>
