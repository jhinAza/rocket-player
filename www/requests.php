<?php
  require_once("inc/functions.php");
  require_once("inc/databaseController.php");
  print("prueba");
  if (isUserLoggedIn()) {
    if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
      http_response_code(404);
      die();
    } else {
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $db = new DatabaseController();
        if ($_POST["type"] == "follow") {
          print("follow");
          session_start();
          $db->followUser($_SESSION["user"], $_POST["followed"]);
          session_write_close();
        } elseif ($_POST["type"] == "unfollow") {
          print("Unfollow");
          session_start();
          $db->unfollowUser($_SESSION["user"], $_POST["followed"]);
          session_write_close();
        }
      }
    }
  }
?>
