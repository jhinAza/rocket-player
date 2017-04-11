<?php
  require_once("inc/functions.php");
  writeHeader("register");
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["userRegister"]) && isset($_POST["mailRegister"]) && isset($_POST["mailRegisterAgain"]) && isset($_POST["passRegister"])) {
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      $bool = $db->registerUser($_POST["userRegister"], $_POST["passRegister"], $_POST["mailRegister"]);
      $bool = true;
      if ($bool) {
        $id = $db->getUserID($_POST["userRegister"]);
        if ($id) {
          $filename = "user_$id.xml";
          $default = "default.xml";
          $folder = "userSettings/";
          if (file_exists($folder.$filename)) {
            unlink($folder.$filename);
          }
          $file = simplexml_load_file($folder.$default);
          $file->attributes()->user_id = $id;
          $file->asXML($folder.$filename);
          header("Location:/login.php");
        }
      } else {
        header("Location: http500.php");
      }
    }
  }
?>
