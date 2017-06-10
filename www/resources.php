<?php
require_once("inc/functions.php");
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Then we will show the page.
    writeHeader("Upload", "upload");
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $video = $db->getVideoInfo($_GET["video"]);
    if (! $video) {
      header("Location: /http404.php");
    }
    ?>
      <!-- Now we return a form -->
      <div class="container" style="padding-top:76px" id="main">
        <!-- <?php print_r($video); ?> -->
        <form id="upload-res" method="multipart/form-data">
          <fieldset>
            <legend>Upload resources form</legend>
            <div class="well">
              <div class="row">
                <div class="col-md-8">
                  <div class="row">
                    <h1>
                      <?php print($video["videoname"]); ?>
                    </h1>
                  </div>
                  <div class="row">
                    <h1>
                      <?php print($db->getUserName($video["userid"])) ?>
                    </h1>
                  </div>
                  <div class="row">
                    <p>
                      <?php print($video["description"]) ?>
                    </p>
                  </div>
                </div>
                <div class="col-md-4 item">
                  <img src="/static/img/video.jpg" >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="video-input">Selecciona el fichero a subir</label>
                <div class="form-group">
                  <div class="input-group">
                    <input type="text" name="file-name" id="file-name" disabled="true" class="form-control">
                    <span class="input-group-btn">
                      <label class="file-label btn btn-default">
                        Selecciona el fichero
                        <input type="file" name="res-input" id="res-input">
                      </label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="video-category">Selecciona un tipo de recurso</label>
                <div class="form-group">
                  <select class="form-control" name="resource-type" id="resource-type">
                    <option value="subtitles">Subtitulos</option>
                    <option value="transcription">Transcripcion</option>
                    <option value="signal-language">Lenguaje de señas</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="video-category">Selecciona un lenguaje</label>
                <div class="form-group">
                  <select class="form-control" name="resource-lang" id="resource-lang">
                    <option value="english">Ingles</option>
                    <option value="spanish">Español</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding-top:25px">
                <div class="btn-group col-md-8 col-md-push-2">
                  <input type="submit" name="send-res-form" id="send-res-form" value="Enviar" class="btn btn-success col-md-6">
                  <input type="reset" name="reset" id="reset" value="Borrar" class="btn btn-danger col-md-6">
                </div>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    <?php
  } else {
    // Then this must be an ajax call
    if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"])) {
      // If the request is not ajax
      http_response_code(404);
      die();
    } elseif (isUserLoggedIn()) {
      // debug(__FILE__,__FUNCTION__,__LINE__);
      // If the user is logged then will start the upload
      $res_type = $_POST["resource-type"];
      switch ($res_type) {
        case 'subtitles':
          $uploadDirectory = "res/subs";
          $file_tipe = '/.srt$/';
          break;
        case 'transcription':
          $uploadDirectory = "res/trans";
          $file_tipe = '/.srt$/';
          break;
        case 'signal-language':
          $uploadDirectory = "res/signal";
          $file_tipe = '/.mp4$/';
          break;
        default:
          http_response_code(500);
          die();
          break;
      }
      if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
      }
      $name = strtolower($_FILES["res-input"]["name"]);
      error_log($name);
      switch ($name) {
        case preg_match($file_tipe, $name) ? true : false:
          error_log("Yep!!");
          break;
        default:
          error_log("El fichero no es correcto");
          die();
          break;
      }
      // We save the data needed for the rename
      $hash_file = hash_file("sha256", $_FILES["res-input"]["tmp_name"]);
      $timestamp = time();
      $file_name = strtolower($_FILES["res-input"]["name"]);
      $ext = substr($file_name, strrpos($file_name, "."));
      $new_name = "$hash_file-$timestamp$ext";
      $full_name = $uploadDirectory."/".$new_name;
      move_uploaded_file($_FILES["res-input"]["tmp_name"], $full_name);
      // Now we only need save the data in the database
      $lang = $_POST["resource-lang"];
      $type = $_POST["resource-type"];
      $video = $_POST["video"];
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      $db->saveResourceInfo($new_name, $type, $lang, $video);
    } else {
      // If not, we will return him to the login page
      header("Location: login.php");
    }
  }
?>
