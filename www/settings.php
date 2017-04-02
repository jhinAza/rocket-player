<?php
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Then we will get the settings
    require_once("inc/userSettingsReader.php");
    require_once("inc/functions.php");
    session_start();
    $user = $_SESSION["user"];
    $uid = $_SESSION["UID"];
    session_write_close();
    $tmp = "<h1>$user</h1> $uid";
    $reader = new UserSettingsReader($user);
    $theme = $reader->readTheme();
    $signLang = $reader->isToggledSignLanguage();
    $subs = $reader->isToggledSubtitles();
    $trans = $reader->isToggledTranscription();
    $dub = $reader->isToggledDubbing();
    require_once("inc/settingsParser.php");
  } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Then we will set the settings
    require_once("inc/userSettingsReader.php");
    require_once("inc/functions.php");
    session_start();
    $user = $_SESSION["user"];
    $uid = $_SESSION["UID"];
    session_write_close();
    $reader = new UserSettingsReader($user);
    $theme = $_POST["themes"];
    $sign = $_POST["sign"];
    $subs = $_POST["subs"];
    $trans = $_POST["trans"];
    $dub = $_POST["dub"];
    $reader->writeTheme($theme);
    $reader->toggleSubtitles($subs);
    $reader->toggleSignLanguage($sign);
    $reader->toggleTranscription($trans);
    $reader->toggleDubbing($dub);
    $reader->save();
  }
?>
