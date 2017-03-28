<!-- This file wil include everything needed for the home page -->
<?php
  require_once("inc/functions.php");
  writeHeader("home");
  if (isUserLoggedIn()) {
    writeNavbar();
  } else {
    header("Location:/login.php");
  }
  writeFooter();
?>
