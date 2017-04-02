<?php
  class DatabaseController {
    function __construct() {
      require_once("inc/settingsReader.php");
      $settings = new SettingsReader();
      $this->server = $settings->readDBServer();
      $this->user = $settings->readDBUser();
      $this->pass = $settings->readDBPass();
      $this->name = $settings->readDBName();
      $connexionString = "mysql:host=$this->server;dbname=$this->name";
      $this->connect = new PDO($connexionString, "$this->user", "$this->pass");
    }

    function loginUser($user,$pass) {
      $stm = $this->connect->prepare("select username, userPassword from users where username = :user");
      $stm->bindParam(":user",$user);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          $row = $data[0];
          return password_verify($pass, $row[1]);
        }
      } else {
        error_log($this->connect->errorInfo());
        error_log($result);
      }
      return false;
    }

    function registerUser($user,$pass,$mail) {
      if ($this->isValidEmail($mail) && $this->isValidUser($user)) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $stm = $this->connect->prepare("insert into users (username, userPassword,userEmail) values (:user,:pass,:mail)");
        $stm->bindParam(":user",$user);
        $stm->bindParam(":pass",$hash);
        $stm->bindParam(":mail",$mail);
        $result = $stm->execute();
        if ($result) {
          return true;
        } else {
          error_log($this->connect->errorInfo());
          error_log($result);
        }
      }
      return false;
    }

    function isValidEmail($mail) {
      $stm = $this->connect->prepare("select userEmail from users where userEmail = :mail");
      $stm->bindParam(":mail",$mail);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        return count($data) == 0;
      } else {
        error_log($this->connect->errorInfo());
        error_log($result);
      }
      return false;
    }

    function isValidUser($user) {
      $stm = $this->connect->prepare("select username from users where username = :user");
      $stm->bindParam(":user",$user);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        return count($data) == 0;
      } else {
        error_log($this->connect->errorInfo());
        error_log($result);
      }
      return false;
    }

    function checkUserUID($user,$UID) {
      $id = $this->getUserID($user);
      $data = $this->selectUIDRow($user);
      if (count($data) == 1) {
        $row = $data[0];
        if (($id == $row["uid"]) && ($UID == $row["token"])) {
          $uidDate = strtotime($row["date"]);
          $currentDate = time();
          if ($uidDate + (40 * 60) > $currentDate) {
            // Si aun no han pasado mas de 40 minutos
            if ($uidDate + (30 * 60) > $currentDate) {
              // Si aun no han pasado mas de 30 minutos
              return true;
            } else {
              // Si han pasado generamos uno nuevo
              session_start();
              $_SESSION["UID"] = $this->setUID($user);
              session_write_close();
              return true;
            }
          } else {
            // El token ha caducado
            $this->deleteUIDRow($user);
            return false;
          }
        }
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
        } else {
          error_log($this->connect->errorInfo());
          error_log($result);
        }
      } else {
        $stm = $this->connect->prepare("update uuid set token = :hash, date = :time where uid = :id");
        $stm->bindParam(":hash",$hash);
        $stm->bindParam(":id",$id);
        $stm->bindParam(":time", $date);
        $result = $stm->execute();
        if ($result) {
          return $hash;
        } else {
          error_log($this->connect->errorInfo());
          error_log($result);
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
      } else {
        error_log($this->connect->errorInfo());
        error_log($result);
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
        }else {
          error_log($this->connect->errorInfo());
          error_log($result);
        }
      }
      return false;
    }

    function deleteUIDRow($user) {
      $id = $this->getUserID($user);
      $stm = $this->connect->prepare("delete from uuid where uid = :id");
      $stm->bindParam("id", $id);
      $stm->execute();
    }
  }
?>
