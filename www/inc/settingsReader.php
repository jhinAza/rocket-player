<?php
  /**
   *
   */
  class SettingsReader {

    function __construct() {
      $filepath = "serverSettings/server_settings.xml";
      if (file_exists($filepath)) {
        $this->settings = simplexml_load_file($filepath);
        print $this->settings->__toString();
      }
    }

    function printSettings() {
      print_r($this->settings);
    }

    function readDBUser() {
      return $this->settings->environment->user;
    }

    function readDBPass() {
      return $this->settings->environment->pass;
    }
  }

?>
