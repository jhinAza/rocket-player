<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("home");
    writeNavbar();
  } else {
    header("Location:/login.php");
  }
  writeFooter();
?>
