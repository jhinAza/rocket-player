<?php
  abstract class DatabaseController {
    function __construct() {
      require_once("/inc/settingsReader.php");
      $settings = new SettingsReader();
      $this->server = $settings->readDBServer();
      $this->user = $settings->readDBUser();
      $this->pass = $settings->readDBPass();
      $this->name = $settings->readDBName();
    }

    abstract function isValidLogin($user,$pass);
    abstract function registerUser($user,$pass,$email);
    abstract function isValidEmail($email);
    abstract function isValidUser($user);

    public static function DatabaseControlerFactory() {
      require_once("inc/settingsReader.php");
      $settings = new SettingsReader();
      switch ($settings->readDBType()) {
        case 'MySQL':
          require_once("/inc/mySQLController.php");
          return new MySQLController();
          break;
        default:
          // TODO: Raise an exception and a HTTP 500
          break;
      }
    }
  }


?>
