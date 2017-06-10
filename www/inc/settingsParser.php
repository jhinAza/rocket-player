<div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Ajustes</h4>
        </div>
        <div class="modal-body">
          <!-- Aqui irá todo el contenido del formulario para pedir al usuario los ajustes. -->
          <form class="form-horizontal" action="#" method="post" id="settingsModal">
            <div class="form-group">
              <label for="themes" class="control-label col-md-4 radio-label">Elige el tema que quieres usar</label>
              <div class="col-md-8">
                <select class="form-control col-md-8" name="themes">
                <?php
                      if ($theme == "night") {
                    ?>
                     <option value="night" selected>Modo noche</option>
                      <option value="day">Modo dia</option>
                    <?php
                      } elseif ($theme == "day") {
                    ?>
                     <option value="night">Modo noche</option>
                      <option value="day" selected>Modo dia</option>
                    <?php
                      }
                    ?>
               </select>
              </div>
            </div>
            <div class="row choices">
              <div class="col-md-6">
                <div class="row">
                  <label for='name-id' class="control-label col-md-6 radio-label">Lenguaje de señas</label>

                  <div class="btn-group col-md-6" data-toggle="buttons">
                    <?php
                      if ($signLang) {
                    ?>
                      <label class="btn btn-primary active">Si <input type="radio" name="sign" value="true" checked></label>
                      <label class="btn btn-primary">No <input type="radio" name="sign" value="false"></label>
                      <?php
                        } else {
                      ?>
                      <label class="btn btn-primary">Si <input type="radio" name="sign" value="true"></label>
                      <label class="btn btn-primary active">No <input type="radio" name="sign" value="false" checked></label>
                    <?php
                      }
                    ?>
                 </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for='name-id' class="control-label col-md-6 radio-label">Subtitulos</label>
                <div class="btn-group col-md-6 " data-toggle="buttons">
                  <?php
                    if ($subs) {
                  ?>
                   <label class="btn btn-primary radio active ">Si <input type="radio" name="subs" value="true" checked></label>
                  <label class="btn btn-primary radio">No <input type="radio" name="subs" value="false"></label>
                  <?php
                    } else {
                  ?>
                   <label class="btn btn-primary radio ">Si <input type="radio" name="subs" value="true"></label>
                  <label class="btn btn-primary radio active">No <input type="radio" name="subs" value="false" checked></label>
                  <?php
                    }
                  ?>
               </div>
              </div>
            </div>
            <div class="row choices">
              <div class="col-md-6">
                <label for='name-id' class="control-label col-md-6 radio-label">Transcripcion</label>
                <div class="btn-group col-md-6 " data-toggle="buttons">
                  <?php
                    if ($trans) {
                  ?>
                  <label class="btn btn-primary radio active ">Si <input type="radio" name="trans" value="true" checked></label>
                  <label class="btn btn-primary radio">No <input type="radio" name="trans" value="false"></label>
                  <?php
                    } else {
                  ?>
                  <label class="btn btn-primary radio ">Si <input type="radio" name="trans" value="true"></label>
                  <label class="btn btn-primary radio active">No <input type="radio" name="trans" value="false" checked></label>
                  <?php
                    }
                  ?>
               </div>
              </div>
            </div>
            <div class="form-group">
              <label for="first-lang" class="control-label col-md-4 radio-label">Elige que idiomas descargar en primer lugar</label>
              <div class="col-md-8">
                <select class="form-control col-md-8" name="first-lang" id="first-lang">
                  <option value="es-es" selected="true">Español</option>
                  <option value="en-en">Ingles</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="second-lang" class="control-label col-md-4 radio-label">Elige que idiomas descargar en segundo lugar</label>
              <div class="col-md-8">
                <select class="form-control col-md-8" name="second-lang" id="second-lang">
                  <option value="es-es">Español</option>
                  <option value="en-en" selected="true">Ingles</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="save">Save changes</button>
        </div>
      </div>
    </div>
  </div>
