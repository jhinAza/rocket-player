<?php
  require_once("inc/functions.php");
  require_once("inc/databaseController.php");
  print("prueba");
  if (isUserLoggedIn()) {
    if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
      http_response_code(404);
      die();
    } else {
      session_start();
      $user = $_SESSION["user"];
      session_write_close();
      if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $db = new DatabaseController();
        if ($_POST["type"] == "follow") {
          print("follow");
          $db->followUser($user, $_POST["followed"]);
        } elseif ($_POST["type"] == "unfollow") {
          print("Unfollow");
          $db->unfollowUser($user, $_POST["followed"]);
        } elseif ($_POST["type"] == "updateUserImage") {
          print_r($_POST);
          $uploadDirectory = "res/img/users";
          if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
          }
          $hash_file = hash_file("sha256", $_FILES["img-file"]["tmp_name"]);
          $timestamp = time();
          $file_name = strtolower($_FILES["img-file"]["name"]);
          $ext = substr($file_name, strrpos($file_name, "."));
          $new_name = "$hash_file-$timestamp$ext";
          $full_name = $uploadDirectory."/".$new_name;
          error_log($file_name);
          error_log($full_name);
          move_uploaded_file($_FILES["img-file"]["tmp_name"], $full_name);
          $db->updateUserImage($user, $new_name);
        } elseif ($_POST["type"] == "update-vote") {
          if ($_POST["vote"] == 0) {
            $db->deleteUserVote($user, $_POST["video"]);
          } else {
            if ($db->userHasVotedVideo($user, $_POST["video"])) {
              $db->updateUserVideoVote($user, $_POST["video"], $_POST["vote"]);
            } else {
              $db->addUserVideoVote($user, $_POST["video"], $_POST["vote"]);
            }
          }
        } elseif ($_POST["type"] == "update-comment-vote") {
          if ($_POST["vote"] == 0) {
            $db->deleteUserCommentVote($user, $_POST["comment"]);
          } else {
            if ($db->userHasVotedComment($user, $_POST["comment"])) {
              $db->updateUserCommentVote($user, $_POST["comment"], $_POST["vote"]);
            } else {
              $db->addUserCommentVote($user, $_POST["comment"], $_POST["vote"]);
            }
          }
        }
      }
    }
  }
?>
