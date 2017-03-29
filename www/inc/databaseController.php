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

    function checkUserUID($user,$UID) {
      $id = $this->getUserID($user);
      $data = $this->selectUIDRow($user);
      if (count($data) == 1) {
        $row = $data[0];
        return ($id == $row["uid"]) && ($UID == $row["token"]);
      }
      return false;
    }

    function setUID($user) {
      $timestamp = time();
      $hash = hash("sha256", "$user-$timestamp");
      $data = $this->selectUIDRow($user);
      $date = date('Y-m-d H:i:s',$timestamp);
      $id = $this->getUserID($user);
      if (count($data) == 0) {
        $stm = $this->connect->prepare("insert into uuid values (:hash, :id, :time)");
        $stm->bindParam(":hash",$hash);
        $stm->bindParam(":id",$id);
        $stm->bindParam(":time", $date);
        $result = $stm->execute();
        if ($result) {
          return $hash;
        }
      } else {
        $stm = $this->connect->prepare("update uuid set token = :hash, date = :time where uid = :id");
        $stm->bindParam(":hash",$hash);
        $stm->bindParam(":id",$id);
        $stm->bindParam(":time", $date);
        $result = $stm->execute();
        if ($result) {
          return $hash;
        }
      }
      return false;
    }

    function getUserID($user) {
      $stm = $this->connect->prepare("select ID from users where username = :user");
      $stm->bindParam(":user",$user);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          $id = $data[0]['ID'];
          return $id;
        }
      }
      return false;
    }

    function selectUIDRow($user) {
      $id = $this->getUserID($user);
      if ($id) {
        $stm = $this->connect->prepare("select * from uuid where uid = :id");
        $stm->bindParam(":id",$id);
        $result = $stm->execute();
        if ($result) {
          $data = $stm->fetchAll();
          return $data;
        }
      }
      return false;
    }
  }
?>
