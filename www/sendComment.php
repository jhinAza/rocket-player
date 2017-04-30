<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
      if (isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
        require_once("inc/databaseController.php");
        session_start();
        $db = new databaseController();
        if (preg_match("/^(#[0-9]{1,} )/", $_POST["comment"])) {
          $id = preg_split("/ /", $_POST["comment"])[0];
          $parent = substr($id, 1);
          $db->saveComment($_COOKIE["video"], $_SESSION["user"], $_POST["comment"], $parent);
        } else {
          $db->saveComment($_COOKIE["video"], $_SESSION["user"], $_POST["comment"]);
        }
        die();
      }
    }
  }
  http_response_code(404);
  die();
?>
