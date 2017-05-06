<?php
require_once("inc/functions.php");
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Then we will show the page.
    writeHeader("Upload", "upload");
    writeNavbar();
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
        <form id="upload" method="multipart/form-data">
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
                        <input type="file" name="video-input" id="video-input" accept="video/mp4">
                      </label>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="video-category">Selecciona un tipo de recurso</label>
                <div class="form-group">
                  <select class="form-control" name="resource-lang" id="resource-lang">
                    <option value="1">Subtitulos</option>
                    <option value="2">Transcripcion</option>
                    <option value="2">Lenguaje de señas</option>
                    <option value="2">Pista de audio</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label for="video-category">Selecciona un lenguaje</label>
                <div class="form-group">
                  <select class="form-control" name="resource-lang" id="resource-lang">
                    <option value="1">Ingles</option>
                    <option value="2">Español</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6" style="padding-top:25px">
                <div class="btn-group col-md-8 col-md-push-2">
                  <input type="submit" name="send-form" id="send-form" value="Enviar" class="btn btn-success col-md-6">
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
      $uploadDirectory = "res/video";
      if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
      }
      switch (strtolower($_FILES["video-input"]["type"])) {
        case 'video/mp4':
          break;
        default:
          error_log("El fichero no es correcto");
          die();
          break;
      }
      // We save the data needed for the rename
      $hash_file = hash_file("sha256", $_FILES["video-input"]["tmp_name"]);
      $timestamp = time();
      $file_name = strtolower($_FILES["video-input"]["name"]);
      $ext = substr($file_name, strrpos($file_name, "."));
      $new_name = "$hash_file-$timestamp$ext";
      $full_name = $uploadDirectory."/".$new_name;
      move_uploaded_file($_FILES["video-input"]["tmp_name"], $full_name);
      // Now we only need save the data in the database
      $videoname = $_POST["video-name"];
      $tags = $_POST["video-tags"];
      $desc = $_POST["video-desc"];
      $cat = $_POST["video-category"];
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      $db->saveVideoInfo($videoname, $tags, $desc, $cat, $new_name, $timestamp);
    } else {
      // If not, we will return him to the login page
      header("Location: login.php");
    }
  }
?>
