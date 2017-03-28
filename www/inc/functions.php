<?php
  // This file contains the needed functions for anything that can appear in any page
  function writeHeader($title) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <?php echo "<title>$title - project</title>" ?>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="/static/librerias/js/jquery.min.js" charset="utf-8"></script>
      <script src="/static/librerias/js/bootstrap.min.js" charset="utf-8"></script>
      <script src="/static/js/main.min.js" charset="utf-8"></script>
      <link rel="stylesheet" href="/static/librerias/css/night.min.css">
      <link rel="stylesheet" href="/static/css/night.css">
    </head>
    <body>
    <?php
  }

  function writeNavbar(){
    include_once("static/html/header.html");
  }

  function writeFooter() {
    ?>
        </body>
      </html>
    <?php
  }

  function isUserLoggedIn() {
    session_start();
    return isset($_SESSION["user"]) && isset($_SESSION["UID"]);
  }
?>
