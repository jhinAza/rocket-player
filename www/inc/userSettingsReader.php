<?php
  class UserSettingsReader {

    function __construct($username ) {
      require_once("inc/databaseController.php");
      $this->username = $username;
      $this->db = new DatabaseController();
      $this->id = $this->db->getUserID($this->username);
      $this->filepath = "userSettings/user_$this->id.xml";
      if (file_exists($this->filepath)) {
        $this->settings = simplexml_load_file($this->filepath);
      }
    }

    function readTheme(){
      return $this->settings->theme["name"];
    }
  }

?>
