<?php
  class DatabaseController {
    function __construct() {
      require_once("/inc/settingsReader.php");
      $settings = new SettingsReader();
      $this->server = $settings->readDBServer();
      $this->user = $settings->readDBUser();
      $this->pass = $settings->readDBPass();
      $this->name = $settings->readDBName();
      $connexionString = "mysql:host=$this->server;dbname=$this->name";
      $this->connect = new PDO($connexionString, "$this->user", "$this->pass");
    }

    function loginUser($user,$pass) {
      $result = $this->connect->query("select username, userPassword from users where username = '$user'");
      if ($result) {
        $data = $result->fetchAll();
        if (count($data) == 1) {
          $row = $data[0];
          // print($row[1]);
          return password_verify($pass, $row[1]);
        } else {
          return false;
        }
      } else {
        print_r($this->connect->errorInfo());
        return $result;
      }
    }

    function registerUser($user,$pass,$mail) {
      if ($this->isValidEmail($mail) && $this->isValidUser($user)) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stm = $this->connect->prepare("insert into users (username, userPassword,userEmail) values (:user,:pass,:mail)");
        $stm->bindParam(":user",$user);
        $stm->bindParam(":pass",$hash);
        $stm->bindParam(":mail",$mail);
        return $stm->execute();
      }
      return false;
    }

    function isValidEmail($mail) {
      $result = $this->connect->query("select userEmail from users where userEmail = '$mail'");
      if ($result) {
        $data = $result->fetchAll();
        return count($data) == 0;
      } else {
        return $result;
      }
    }

    function isValidUser($user) {
      $result = $this->connect->query("select username from users where username = '$user'");
      if ($result) {
        $data = $result->fetchAll();
        return count($data) == 0;
      } else {
        return $result;
      }
    }

  }
?>
