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

    function readTheme() {
      return $this->settings->theme["name"]->__toString();
    }

    function writeTheme($theme) {
      $this->settings->theme["name"] = $theme;
    }

    function isToggledSignLanguage() {
      return $this->settings->resources->sign_language["toggle"]->__toString();
    }

    function toggleSignLanguage($bool) {
      $this->settings->resources->sign_language["toggle"] = $bool;
    }

    function isToggledSubtitles() {
      return $this->settings->resources->subtitles["toggle"]->__toString();
    }

    function toggleSubtitles($bool) {
      $this->settings->resources->subtitles["toggle"] = $bool;
    }

    function isToggledTranscription() {
      return $this->settings->resources->transcription["toggle"]->__toString();
    }

    function toggleTranscription($bool) {
      $this->settings->resources->transcription["toggle"] = $bool;
    }

    function isToggledDubbing() {
      return $this->settings->resources->dubbing["toggle"]->__toString();
    }

    function toggleDubbing($bool) {
      $this->settings->resources->dubbing["toggle"] = $bool;
    }

    function getLang($order) {
      foreach ($this->settings->languages->language as $lang) {
        if ($lang["order"] === $order) {
          return $lang["lang"];
        }
      }
    }

    function setLang($order, $newLang) {
      foreach ($this->settings->languages->language as $lang) {
        if ($lang["order"] === $order) {
          $lang["lang"] = $newLang;
        }
      }
    }

    function save() {
      $this->settings->asXML($this->filepath);
    }
  }

?>
