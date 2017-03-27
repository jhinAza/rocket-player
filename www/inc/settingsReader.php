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
      foreach ($this->settings as $env) {
        if ($env["inUse"][0] == "true") {
          return $env->user->__toString();
        }
      }
    }

    function readDBPass() {
      foreach ($this->settings as $env) {
        if ($env["inUse"][0] == "true") {
          return $env->pass->__toString();
        }
      }
    }

    function readDBServer() {
      foreach ($this->settings as $env) {
        if ($env["inUse"][0] == "true") {
          return $env->server->__toString();
        }
      }
    }

    function readDBName() {
      foreach ($this->settings as $env) {
        if ($env["inUse"][0] == "true") {
          return $env->database->__toString();
        }
      }
    }

    function readDBType() {
      foreach ($this->settings as $env) {
        if ($env["inUse"][0] == "true") {
          return $env->databaseType->__toString();
        }
      }
    }
  }

?>
