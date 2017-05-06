<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("Reproductor", "player");
    writeNavbar();
    $video = $_GET["video"];
    setcookie("video", $_GET["video"]);
    session_start();
    $user = $_SESSION["user"];
    session_write_close();
    require_once("inc/databaseController.php");
    $db = new DatabaseController();
    $videoInfo = $db->getVideoInfo($video);
    if (!$videoInfo) {
      header("Location: http404.php");
    }
    $videoURL = "/res/video/".$videoInfo["filename"];
    $db->addVideoToHistory($video, $user);
    ?>
    <div class="container" style="padding-top:76px">
      <div class="video col-md-8 col-xs-12">
        <ul class="nav nav-pills nav-stacked video-header">
          <li class="active">
            <a><h4 class="panel-title"><?php print($videoInfo["videoname"]); ?></h4></a>
          </li>
        </ul>
        <div class="video-container">
          <video id="mainVideo">
            <source src="<?php print($videoURL); ?>" type="video/mp4">
            </video>
            <div class="controls">
              <div id="play" class="clickable">
                <div class="play">
                  <span class="glyphicon glyphicon-play"></span>
                </div>
                <div class="pause">
                  <span class="glyphicon glyphicon-pause"></span>
                </div>
              </div>
              <div id="stop" class="clickable">
                <span class="glyphicon glyphicon-stop"></span>
              </div>
              <div id="playThrough" >
                <span class="seek">
                  <input type="range" name="seekBar" id="seekBar" value="0" min="0" max="1" step="0.01">
                </span>
              </div>
              <div>
                <span id="time">0:00:00</span>
              </div>
              <div id="fullscreen" class="clickable">
                <span class="glyphicon glyphicon-fullscreen"></span>
              </div>
              <div id="mute" class="clickable">
                <div class="no-volume">
                  <span class="glyphicon glyphicon-volume-off"></span>
                </div>
                <div class="half-volume">
                  <span class="glyphicon glyphicon-volume-down"></span>
                </div>
                <div class="high-volume">
                  <span class="glyphicon glyphicon-volume-up"></span>
                </div>
              </div>
              <div id="volume" >
                <span class="glyphicon glyphicon-volume-down volume"></span>
                <input type="range" name="volumeBar" id="volumeBar" value="0.5" min="0" max="1" step="0.1" >
                <span class="glyphicon glyphicon-volume-up volume"></span>
                <span style="padding:8px;">
                </span>
              </div>
            </div>
        </div>
      </div>
      <div class="info col-md-4 col-xs-12">
          <div class="panel-group" id="info">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4 class="panel-title"><a href="#author" data-toggle="collapse" data-parent="#info">Información <span class="glyphicon glyphicon glyphicon-chevron-down"></span></a></h4>
              </div>
              <div class="panel-collapse collapse in" id="author">
                <div class="panel-body">
                  <a href="/profile.php?uid=<?php print($videoInfo["userid"]); ?>">
                    <?php print($db->getUserName($videoInfo["userid"])); ?>
                  </a><br>
                  <p>
                    <?php print($videoInfo["description"]); ?>
                  </p>
                </div>
              </div>
            </div>
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h4 class="panel-title"><a href="#downloads" data-toggle="collapse" data-parent="#info">Descargas <span class="glyphicon glyphicon glyphicon-chevron-down"></span></a></h4>
              </div>
              <div class="panel-collapse collapse" id="downloads">
                <div class="panel-body">
                  <ul class="list-group">
                    <li class="list-group-item"><a href=<?php print($videoURL); ?> download>Video</a></li>
                    <!-- Usar bucle para iterar por los recursos -->
                    <li class="list-group-item"><a href=<?php print("resources.php?video=".$_GET["video"]); ?> download>Añadir recursos</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      <div class="recomendations col-md-4 col-xs-12">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title">Videos recomendados</h4>
          </div>
          <div class="panel-body">
            <div class="panel-group">
              <div class="panel panel-info">
                <div class="panel-body">
                  <div class='row'>
                    <div class="col-xs-4 video-thumb">
                      <img src="/static/img/video.jpg">
                    </div>
                    <div class="col-md-8">
                      Titulo del video <br> Autor del video
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-info">
                <div class="panel-body">
                  <div class='row'>
                    <div class="col-xs-4 video-thumb">
                      <img src="/static/img/video.jpg" >
                    </div>
                    <div class="col-md-8">
                      Titulo del video <br> Autor del video
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-info">
                <div class="panel-body">
                  <div class='row'>
                    <div class="col-xs-4 video-thumb">
                      <img src="/static/img/video.jpg" >
                    </div>
                    <div class="col-md-8">
                      Titulo del video <br> Autor del video
                    </div>
                  </div>
                </div>
              </div>
              <div class="panel panel-info">
                <div class="panel-body">
                  <div class='row'>
                    <div class="col-xs-4 video-thumb">
                      <img src="/static/img/video.jpg" >
                    </div>
                    <div class="col-md-8">
                      Titulo del video <br> Autor del video
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="comments col-md-8 col-xs-12 ">
        <div class="comment-form">
          <div class="row">
            <div class="col-md-4 buttons">
              <div class="row">
                <div class="col-md-6">
                  <div class="btn btn-success col-xs-12">
                    <span class="glyphicon glyphicon-plus-sign"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="btn btn-danger col-xs-12">
                    <span class="glyphicon glyphicon-minus-sign"></span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8 col-md-push-2">
                  <div class="btn btn-info col-xs-12">
                    <span>Subscribe!</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8">
              <div class="row">
                <textarea name="comment" id="comment" rows="3" class="form-control"></textarea>
              </div>
              <div class="row">
                <button type="button" name="send-comment" id="send-comment" class="btn btn-info col-md-4 col-md-push-4">Enviar comentario
                </button>
                <div class="col-md-2 btn btn-success col-md-push-6" id="length-btn">
                  <span class="badge" id="length"></span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="panel panel-info">
          <div class="panel-heading">
            <h4 class="panel-title">Comentarios</h4>
          </div>
          <div class="panel-body">
            <?php
              require_once("inc/commentsParser.php");
            ?>
          </div>
        </div>
      </div>
    </div>
    <?php

  } else {
    header("Location: login.php");
  }
 ?>
