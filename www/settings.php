<?php
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Then we will get the settings
    require_once("inc/userSettingsReader.php");
    require_once("inc/functions.php");
    session_start();
    $user = $_SESSION["user"];
    $uid = $_SESSION["UID"];
    $tmp = "<h1>$user</h1> $uid";
    error_log($tmp);
    $reader = new UserSettingsReader($user);
    $theme = $reader->readTheme();
    require_once("inc/settingsParser.php");
  } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Then we will set the settings
  }
?>
