<?php
  if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Then we will show the page.
    require_once("inc/functions.php");
    writeHeader("Upload");
    writeNavbar();
    ?>
      <!-- Now we return a form -->
      <div class="container" style="padding-top:76px">
        <form id="upload" method="post">
          <fieldset>
            <legend>Upload form</legend>
            <div class='row'>

              <div class="col-md-6">
                <label for="video-name">Nombre del video</label>
                <div class="form-group">
                  <input type="text" name="video-name" id="video-name" placeholder="Nombre" class="form-control">
                </div>
                <label for="video-input">Selecciona el fichero a subir</label>
                <div class="form-group">
                  <div class="input-group">
                  <input type="text" name="file-name" id="file-name" disabled="true" class="form-control">
                  <span class="input-group-btn">
                    <label class="file-label btn btn-default">
                      Selecciona el fichero
                      <input type="file" name="video-input" id="video-input" >
                    </label>
                  </span>
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
                    <label for="video-tags">Selecciona los tags</label>
                    <div class="form-group">
                      <select class="form-control" name="video-tags" id="video-tags" multiple size="7">
                        <option value="1">Accion</option>
                        <option value="2">Rol</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="btn-group col-md-4 col-md-push-4">
                <input type="submit" name="send" id="send" value="Enviar" class="btn btn-success col-md-6">
                <input type="reset" name="reset" id="reset" value="Borrar" class="btn btn-danger col-md-6">
              </div>
            </div>
          </fieldset>
        </form>
      </div>
    <?php
  } else {
    // Then this must be an ajax call
  }
?>
