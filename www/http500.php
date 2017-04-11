<?php
  require_once("inc/functions.php");
  writeHeader("500");
  http_response_code(500);
?>
<div class="container">
  <div class="alert alert-danger">
    <h4>Lo sentimos, ha ocurrido un error procesando su peticion</h4>
    <p>Nuestro altamente cualificado tecnico esta intentando arreglarlo</p>
    <b>Pulsa  <a href="/" class="alert-link">aqui</a> para volver al inicio.</b>
  </div>
  <div class="row">
    <img src="/static/img/server_error.jpg" class="col-md-8 col-md-push-2">
  </div>
</div>
