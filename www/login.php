<!-- This file is for the sole purpose of log-in a user -->
<?php
  require_once("inc/functions.php");
  writeHeader("login");
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Then we will read the user and password data and try to check the credentials
    if (isset($_POST["user"]) && isset($_POST["pass"])) {
      require_once("inc/databaseController.php");
      $db = new DatabaseController();
      if ($db->loginUser($_POST["user"], $_POST["pass"])) {
        session_start();
        $_SESSION["user"] = $_POST["user"];
        $_SESSION["UID"] = $_POST["user"];
        header("Location:/home.php");
      } else {
        print "<h3>Ha ocurrido un error</h3>";
        print "<a href=/login.php>Pulse aqui</a> para reintentar";
      }
    }
  } else {
    // Is GET and then we need to show the data.
    ?>
     <div class="container" style="padding-top:86px">
      <div class="well col-md-4 col-md-push-1">
        <form id="loginUser" action="login.php" method="post" class="formUsers">
          <fieldset>
            <legend>Formulario de acceso</legend>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-user"></span>
                </div>
                <input type="text" name="user" class="form-control" id="user" placeholder="Usuario">
              </div>
            </div>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-lock"></span>
                </div>
                <input type="password" name="pass" class="form-control" id="pass" placeholder="Contraseña">
              </div>
            </div>
            <div class="row">
              <div class="btn-group col-md-8 col-md-push-2">
                <button type="submit" name="send" class="btn btn-success">Enviar</button>
                <button type="reset" name="reset" class="btn btn-danger">Borrar</button>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
      <div class="well col-md-4 col-md-push-3">
        <form id="registerUser" action="register.php" method="post" class="formUsers">
          <fieldset>
            <legend>Formulario de registro</legend>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-user"></span>
                </div>
                <input type="text" name="userRegister" placeholder="Usuario" id="userRegister" class="form-control">
              </div>
            </div>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-envelope"></span>
                </div>
                <input type="email" name="mailRegister" placeholder="Correo electronico" id="mailRegister" class="form-control">
              </div>
            </div>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-envelope"></span>
                </div>
                <input type="email" name="mailRegisterAgain" placeholder="Repite el correo electronico" id="mailRegisterAgain" class="form-control">
              </div>
            </div>
            <div class="row">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="glyphicon glyphicon-lock"></span>
                </div>
                <input type="password" name="passRegister" placeholder="Contraseña" id="passRegister" class="form-control">
              </div>
            </div>
            <div class="row">
              <div class="btn-group col-md-8 col-md-push-2">
                <button type="submit" name="send" class="btn btn-success">Enviar</button>
                <button type="reset" name="reset" class="btn btn-danger">Borrar</button>
              </div>
            </div>
          </fieldset>
        </form>
      </div>
     </div>
    <?php
  }
?>
