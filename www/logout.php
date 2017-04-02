<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    session_start();
    unset($_SESSION["user"]);
    unset($_SESSION["UID"]);
  }
  header("Location:/login.php");
?>
