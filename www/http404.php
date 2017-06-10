<?php
  require_once("inc/functions.php");
  writeHeader("404", "error");
  http_response_code(404);
?>
<div class="container">
  <div class="alert alert-warning">
    <h4>Parece que la pagina que estas buscando no existe</h4>
    <b>Pulsa  <a href="/" class="alert-link">aqui</a> para volver al inicio.</b>
  </div>
  <div class="row">
    <img src="/static/img/resource_not_found.jpg" class="col-md-8 col-md-push-2">
  </div>
</div>
