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
        error_log($this->connect->errorInfo()[2]);
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
          error_log($this->connect->errorInfo()[2]);
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
        error_log($this->connect->errorInfo()[2]);
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
        error_log($this->connect->errorInfo()[2]);
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
          error_log($this->connect->errorInfo()[2]);
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
          error_log($this->connect->errorInfo()[2]);
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
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function getUserName($uid) {
      $stm = $this->connect->prepare("select username from users where ID = :user");
      $stm->bindParam(":user",$uid);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          $id = $data[0]['username'];
          return $id;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
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
          error_log($this->connect->errorInfo()[2]);
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

    function isUserAdmin($user) {
      $id = $this->getUserID($user);
      $stm = $this->connect->prepare("select userRole from users where id = :id");
      $stm->bindParam(":id", $id);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          $row = $data[0];
          return $row["userRole"] === "admin";
        }
      }
      return false;
    }

    function getAllTables() {
      $stm = $this->connect->prepare("show tables");
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      return false;
    }

    function truncateTable($table) {
      $stm = $this->connect->prepare("truncate table $table");
      // $stm->bindParam(":table", $table);
      $res = $stm->execute();
    }

    function saveVideoInfo($videoname, $tags, $desc, $cat, $videofile, $time, $public=true, $active=true) {
      // We need to store all the data in the database
      session_start();
      $user = $_SESSION["user"];
      session_write_close();
      $uid = $this->getUserID($user);
      $date = date('Y-m-d H:i:s',$time);
      $stm = $this->connect->prepare("insert into videos (filename, videoname, description, creationdate, userID, cat, public, active) values (:filename, :videoname, :desc, :date, :uid, :cat, :public, :active)");
      $stm->bindParam(":filename", $videofile);
      $stm->bindParam(":videoname",$videoname);
      $stm->bindParam(":desc", $desc);
      $stm->bindParam(":date",$date);
      $stm->bindParam(":uid",$uid);
      $stm->bindParam(":cat",$cat);
      $stm->bindParam(":public",$public);
      $stm->bindParam(":active",$active);
      $result = $stm->execute();
      if ($result) {
        // Now that we have the video stored in the DB we need the ID
        $videoID = $this->connect->lastInsertId();
        print($videoID." video ID\n");
        foreach ($tags as $tag) {
          print($tag);
          $stm = $this->connect->prepare("insert into video_genres values (:video, :tag)");
          $stm->bindParam(":video", $videoID);
          $stm->bindParam(":tag", $tag);
          $result = $stm->execute();
          print($result);
          print_r($this->connect->errorInfo()[2]);
          print($this->connect->errorCode());
        }
        return true;
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
    }

    function getVideoInfo($videoID) {
      $stm = $this->connect->prepare("select * from videos where id = :vid");
      $stm->bindParam(":vid", $videoID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          return $data[0];
        }
      }
      return false;
    }

    function saveComment($videoID, $user, $comment, $parent=null) {
      $uid = $this->getUserID($user);
      $timestamp = time();
      $date = date('Y-m-d H:i:s',$timestamp);
      $stm = $this->connect->prepare("insert into comments (creationdate, parentcomment, userID, video, comments) values (:date, :parent, :uid, :video, :text)");
      $stm->bindParam(":date", $date);
      $stm->bindParam(":parent", $parent);
      $stm->bindParam(":uid", $uid);
      $stm->bindParam(":video", $videoID);
      $stm->bindParam(":text", $comment);
      $result = $stm->execute();
      if ($result) {
        return true;
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function getComments($videoID) {
      $stm = $this->connect->prepare("select * from comments where video = :id and parentcomment is NULL");
      $stm->bindParam(":id", $videoID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      return false;
    }

    function getChildComments($parentID) {
      $stm = $this->connect->prepare("select * from comments where parentcomment = :id");
      $stm->bindParam(":id", $parentID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      return false;
    }

    function executeQuery($str) {
      $stm = $this->connect->prepare($str);
      $result = $stm->execute();
      return $result;
    }

    function addVideoToHistory($video, $user) {
      $uid = $this->getUserID($user);
      $timestamp = time();
      $date = date('Y-m-d H:i:s',$timestamp);
      $stm = $this->connect->prepare("select * from history where user = :user and video = :video");
      $stm->bindParam(":user", $uid);
      $stm->bindParam(":video", $video);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          $stm = $this->connect->prepare("update history set date = :date where user = :user and video = :video");
        } else {
          $stm = $this->connect->prepare("insert into history values (:user, :video, :date)");
        }
        $stm->bindParam(":user", $uid);
        $stm->bindParam(":video", $video);
        $stm->bindParam(":date", $date);
        $result = $stm->execute();
        if ($result) {
          return true;
        } else {
          error_log($this->connect->errorInfo()[0]);
          error_log($result);
        }
      } else {
        error_log($this->connect->errorInfo()[0]);
        error_log($result);
      }
      return false;
    }

    function getHistory($user, $start, $limit) {
      $stm = $this->connect->prepare("SELECT u.username as 'creator', v.videoname, v.id as 'videoID' FROM `history` as h, `users` as u, `videos` as v WHERE h.user = :user and v.userid = u.id and h.video = v.id order by date desc limit :offset,:limit");
      $stm->bindParam(":user", $user);
      $stm->bindValue(":offset", $start, PDO::PARAM_INT);
      $stm->bindValue(":limit", $limit, PDO::PARAM_INT);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function getUserVideos($user, $start, $limit) {
      $stm = $this->connect->prepare("SELECT v.videoname, v.id FROM `videos` as v WHERE userid = :user ORDER BY creationdate DESC limit :offset, :limit ");
      $stm->bindParam(":user", $user);
      $stm->bindValue(":offset", $start, PDO::PARAM_INT);
      $stm->bindValue(":limit", $limit, PDO::PARAM_INT);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function followUser($user, $followed) {
      $uid = $this->getUserID($user);
      $stm = $this->connect->prepare("INSERT into following values (:follower, :followed)");
      $stm->bindParam(":follower", $uid);
      $stm->bindParam(":followed", $followed);
      $result = $stm->execute();
      return $result;
    }

    function unfollowUser($user, $followed) {
      $uid = $this->getUserID($user);
      $stm = $this->connect->prepare("DELETE from following where follower = :follower and followed = :followed");
      $stm->bindParam(":follower", $uid);
      $stm->bindParam(":followed", $followed);
      $result = $stm->execute();
      return $result;
    }

    function isFollowing($user, $followed) {
      $uid = $this->getUserID($user);
      $stm = $this->connect->prepare("SELECT * from following where follower = :follower and followed = :followed");
      $stm->bindParam(":follower", $uid);
      $stm->bindParam(":followed", $followed);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        error_log(count($data) > 0 ? "true" : "false");
        return count($data) > 0 ? "true" : "false";
      } else {
        return "false";
      }
    }

    function getFollowers($uid) {
      $stm = $this->connect->prepare("SELECT u.username, u.id FROM `following` as f, `users` as u WHERE f.followed = :followed and f.follower = u.id");
      $stm->bindParam(":followed", $uid);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      return false;
    }

    function getFollows($uid) {
      $stm = $this->connect->prepare("SELECT u.username, u.id FROM `following` as f, `users` as u WHERE f.follower = :follower and f.followed = u.id");
      $stm->bindParam(":follower", $uid);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      }
      return false;
    }

    function userHasVideos($uid) {
      $stm = $this->connect->prepare("SELECT COUNT(*) as 'count' FROM videos where userid = :uid ");
      $stm->bindParam(":uid", $uid);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        return ((int) $data[0]["count"]) > 0;
      }
      else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function saveResourceInfo($filename, $type, $lang, $video) {
      // We need to store all the data in the database
      session_start();
      $user = $_SESSION["user"];
      session_write_close();
      $uid = $this->getUserID($user);
      $time = time();
      $date = date('Y-m-d H:i:s',$time);
      $stm = $this->connect->prepare("insert into resources (filename, creationdate, userid, restype, lang, video) values (:filename, :date, :uid, :type, :lang, :video)");
      $stm->bindParam(":filename", $filename);
      $stm->bindParam(":date",$date);
      $stm->bindParam(":uid",$uid);
      $stm->bindParam(":type", $type);
      $stm->bindParam(":lang", $lang);
      $stm->bindParam(":video", $video);
      $result = $stm->execute();
      print($type."\n");
      print($lang."\n");
      print($this->connect->errorCode()."\n");
      print_r($this->connect->errorInfo());
      if ($result) {
        return true;
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return false;
    }

    function getSubtitlesFile($video, $lang="english") {
      $stm = $this->connect->prepare("select * from resources where restype = 'subtitles' and lang = :lang and video = :video limit 1");
      $stm->bindParam(":lang", $lang);
      $stm->bindParam(":video", $video);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data[0]["filename"];
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
    }

    function getTranscriptionFile($video, $lang="english") {
      $stm = $this->connect->prepare("select * from resources where restype = 'transcription' and lang = :lang and video = :video limit 1");
      $stm->bindParam(":lang", $lang);
      $stm->bindParam(":video", $video);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data[0]["filename"];
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
    }

    function getSignLangVideo($video, $lang="english") {
      $stm = $this->connect->prepare("select * from resources where restype = 'signal-language' and lang = :lang and video = :video limit 1");
      $stm->bindParam(":lang", $lang);
      $stm->bindParam(":video", $video);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data[0]["filename"];
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
    }

    function getVideosByQuery($query) {
      $query = "%".$query."%";
      $stm = $this->connect->prepare("SELECT videoname, id, userid from videos where videoname like :query");
      $stm->bindParam(":query", $query);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getUsersByQuery($query) {
      $query = "%".$query."%";
      $stm = $this->connect->prepare("SELECT username, id from users where username like :query");
      $stm->bindParam(":query", $query);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getCategories() {
      $stm = $this->connect->prepare("SELECT * from categories");
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getGenres() {
      $stm = $this->connect->prepare("SELECT * from genres");
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getCatName($catID) {
      $stm = $this->connect->prepare("SELECT * from categories where id = :catID");
      $stm->bindParam(":catID", $catID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) == 1) {
          return $data[0]["nombre"];
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getGenresName($videoID) {
      $stm = $this->connect->prepare("  SELECT nombre from genres g, video_genres v where v.video = :videoID and v.genres = g.id");
      $stm->bindParam(":videoID", $videoID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }

    function getResourcesInfoFromVideo($videoID) {
      $stm = $this->connect->prepare("SELECT * FROM resources WHERE video = :videoID");
      $stm->bindParam(":videoID", $videoID);
      $result = $stm->execute();
      if ($result) {
        $data = $stm->fetchAll();
        if (count($data) > 0) {
          return $data;
        }
      } else {
        error_log($this->connect->errorInfo()[2]);
        error_log($result);
      }
      return [];
    }
  }

?>
