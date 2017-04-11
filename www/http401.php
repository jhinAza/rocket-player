<?php
  require_once("inc/functions.php");
  writeHeader("404");
  http_response_code(401);
?>
<div class="container">
  <div class="alert alert-danger">
    <h4>Usuario o contraseña equivocado</h4>
    <b>Pulsa  <a href="/" class="alert-link">aqui</a> para volver a intentarlo.</b>
  </div>
  <div class="row">
    <img src="/static/img/keyError.jpg" class="col-md-8 col-md-push-2">
  </div>
</div>
