<?php
require_once("inc/functions.php");
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Then we will show the page.
    writeHeader("Upload", "upload");
    writeNavbar();
    ?>
      <!-- Now we return a form -->
      <div class="container" style="padding-top:76px" id="main">
        <form id="upload" method="multipart/form-data">
          <fieldset>
            <legend>Upload form</legend>
            <div class='row'>

              <div class="col-md-6">
                <label for="video-name">Nombre del video</label>
                <div class="form-group">
                  <input type="text" name="video-name" id="video-name" placeholder="Nombre" class="form-control">
                </div>
                <div class='row '>
                  <div class="col-md-7">
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
                  <div class="col-md-5">
                    <label for="video-category">Selecciona una categoria</label>
                    <div class="form-group">
                      <select class="form-control" name="video-category" id="video-category">
                        <?php
                          require_once("inc/databaseController.php");
                          $db = new DatabaseController();
                          $list = $db->getCategories();
                          foreach ($list as $item) {
                            ?>
                              <option value=<?php print($item["id"]) ?>>
                                <?php print($item["nombre"]) ?>
                              </option>
                            <?php
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-7">
                    <label for="video-input">Selecciona la portada del video</label>
                    <div class="form-group">
                      <div class="input-group">
                        <input type="text" name="img-name" id="img-name" disabled="true" class="form-control">
                        <span class="input-group-btn">
                          <label class="file-label btn btn-default">
                            Selecciona el fichero
                            <input type="file" name="img-input" id="img-input" accept="image/*">
                          </label>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-8">
                    <label for="video-desc">Descripcion</label>
                    <div class="form-group">
                      <textarea name="video-desc" id="video-desc"rows="5" class="form-control"></textarea>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <label for="video-tags">Selecciona los generos</label>
                    <div class="form-group">
                      <select class="form-control" name="video-tags[]" id="video-tags" multiple size="7">
                        <?php
                          require_once("inc/databaseController.php");
                          $db = new DatabaseController();
                          $list = $db->getGenres();
                          foreach ($list as $item) {
                            ?>
                              <option value=<?php print($item["id"]) ?>>
                                <?php print($item["nombre"]) ?>
                              </option>
                            <?php
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="btn-group col-md-4 col-md-push-4">
                <input type="submit" name="send-form" id="send-form" value="Enviar" class="btn btn-success col-md-6">
                <input type="reset" name="reset" id="reset" value="Borrar" class="btn btn-danger col-md-6">
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
      $video_upload_directory = "res/video";
      if (!file_exists($video_upload_directory)) {
        mkdir($video_upload_directory, 0777, true);
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
      $video_hash_file = hash_file("sha256", $_FILES["video-input"]["tmp_name"]);
      $timestamp = time();
      $video_file_name = strtolower($_FILES["video-input"]["name"]);
      $video_ext = substr($video_file_name, strrpos($video_file_name, "."));
      $video_new_name = "$video_hash_file-$timestamp$video_ext";
      $video_full_name = $video_upload_directory."/".$video_new_name;
      move_uploaded_file($_FILES["video-input"]["tmp_name"], $video_full_name);
      // We save the data needed for the image
      $img_upload_directory = "res/img/videos";
      if (!file_exists($img_upload_directory)) {
        mkdir($img_upload_directory, 0777, true);
      }
      $img_hash_file = hash_file("sha256", $_FILES["img-input"]["tmp_name"]);
      $timestamp = time();
      $img_file_name = strtolower($_FILES["img-input"]["name"]);
      $img_ext = substr($img_file_name, strrpos($img_file_name, "."));
      $img_new_name = "$img_hash_file-$timestamp$img_ext";
      $img_full_name = $img_upload_directory."/".$img_new_name;
      move_uploaded_file($_FILES["img-input"]["tmp_name"], $img_full_name);
      // Now we only need save the data in the database
      $videoname = $_POST["video-name"];
      $tags = $_POST["video-tags"];
      $desc = $_POST["video-desc"];
      $cat = $_POST["video-category"];

      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      $db->saveVideoInfo($videoname, $tags, $desc, $cat, $video_new_name, $timestamp, $img_new_name);
    } else {
      // If not, we will return him to the login page
      header("Location: login.php");
    }
  }
?>
