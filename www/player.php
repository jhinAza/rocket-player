<?php
  require_once("inc/functions.php");
  if (isUserLoggedIn()) {
    writeHeader("Reproductor", "player");
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
    require_once("inc/userSettingsReader.php");
    $reader = new UserSettingsReader($user);
    require_once("inc/resourcesParser.php");
    require_once("inc/functions.php");
    $pos = $db->getCountsOfVideoVotes($video, 1)[0]["count"];
    $neg = $db->getCountsOfVideoVotes($video, -1)[0]["count"];
    ?>
    <div class="container" style="padding-top:76px">
      <div class="video col-md-8 col-xs-12">
        <ul class="nav nav-pills nav-stacked video-header">
          <li class="active">
            <a><h4 class="panel-title"><?php print($videoInfo["videoname"]); ?></h4></a>
          </li>
        </ul>
        <div class="video-container">
          <div class="fit-container">
            <video id="mainVideo">
              <source src="<?php print($videoURL); ?>" type="video/mp4"/>
            </video>
            <?php if ($reader->isToggledSubtitles()): ?>
              <div class="subtitles">
                <p>
                  <?php
                    $list = parse_subtitles_file(getUserPreferredSubtitlesFile($user, $_GET["video"]));
                    if ($list) {
                      foreach ($list as $item) {
                        print($item);
                      }
                    } else {
                      print("No hay ningun fichero de subtitulos para tus lenguajes");
                    }
                  ?>
                </p>
              </div>
            <?php endif; ?>
          </div>
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
              <?php if ($reader->isToggledSubtitles()): ?>
              <div id="subs">
                <span class="glyphicon glyphicon-subtitles"></span>
              </div>
              <?php endif; ?>
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
                  <p>
                    Categoria: <b>
                      <?php print($db->getCatName($videoInfo["cat"])); ?>
                    </b>
                  </p>
                  <p>
                    Generos:
                    <ul>
                      <?php
                        $list = $db->getGenresName($videoInfo["id"]);
                        foreach ($list as $row) {
                          print("<li>".$row["nombre"]."</li>");
                        }
                      ?>
                    </ul>

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
                    <?php
                      $list = $db->getResourcesInfoFromVideo($_GET["video"]);
                      foreach ($list as $row) {
                        if ($row["restype"] === "subtitles") {
                          $folder = "/res/subs/";
                        } elseif ($row["restype"] === "transcription") {
                          $folder = "/res/trans/";
                        } else {
                          $folder = "/res/signal/";
                        }
                        ?>
                        <li class="list-group-item">
                          <a href=<?php print($folder.$row["filename"]); ?> download>
                            <?php print($row["restype"]." (".$row['lang'].")") ?>
                          </a>
                        </li>
                        <?php
                      }
                    ?>
                    <li class="list-group-item"><a href=<?php print("resources.php?video=".$_GET["video"]); ?>>Añadir recursos</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <?php if ($reader->isToggledTranscription()): ?>
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h4 class="panel-title"><a href="#transcription-pane" data-toggle="collapse" data-parent="#info">Transcripcion <span class="glyphicon glyphicon glyphicon-chevron-down"></span></a></h4>
                </div>
                <div class="panel-collapse collapse" id="transcription-pane">
                  <div class="panel-body transcription">
                    <?php
                      $list = parse_transcription_file(getUserPreferredTranscriptionFile($user, $_GET["video"]));
                      if ($list) {
                        foreach ($list as $item) {
                          print($item);
                        }
                      } else {
                        print("No hay ningun fichero de transcripcion para tus lenguajes");
                      }
                    ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($reader->isToggledSignLanguage()): ?>
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h4 class="panel-title"><a href="#sign-pane" data-toggle="collapse" data-parent="#info">Lenguaje de señas <span class="glyphicon glyphicon glyphicon-chevron-down"></span></a></h4>
                </div>
                <div class="panel-collapse collapse" id="sign-pane">
                  <div class="panel-body sign-lang">
                    <?php
                      $video = getUserPreferredSignLanguageVideo($user, $_GET["video"]);
                    ?>
                    <video class="video-sign-lang">
                      <source src=<?php print('"'.$video.'"'); ?> type="video/mp4">
                    </video>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <div class="recomendations col-md-4 col-xs-12">
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title">Videos recomendados</h4>
          </div>
          <div class="panel-body">
            <div class="panel-group">
              <?php getColOfRecommendations($video); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="comments col-md-8 col-xs-12 ">
        <div class="comment-form">
          <div class="row">
            <div class="col-md-4 buttons">
              <div class="row">
                <?php if ($db->userHasVotedVideo($user, $video)): ?>
                  <?php if ($db->getUserVideoVote($user, $video) == 1): ?>
                    <div class="col-md-6">
                      <div class="btn btn-success col-xs-12 active" id="vote-up" data-voted="true">
                        <span class="glyphicon glyphicon-plus-sign"></span>
                        <span class="badge"><?php print($pos) ?></span>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="btn btn-danger col-xs-12" id="vote-down" data-voted="false">
                        <span class="glyphicon glyphicon-minus-sign"></span>
                        <span class="badge"><?php print($neg) ?></span>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="col-md-6">
                      <div class="btn btn-success col-xs-12" id="vote-up" data-voted="false">
                        <span class="glyphicon glyphicon-plus-sign"></span>
                        <span class="badge"><?php print($pos) ?></span>

                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="btn btn-danger col-xs-12 active" id="vote-down" data-voted="true">
                        <span class="glyphicon glyphicon-minus-sign"></span>
                        <span class="badge"><?php print($neg) ?></span>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <div class="col-md-6">
                    <div class="btn btn-success col-xs-12" id="vote-up" data-voted="false">
                      <span class="glyphicon glyphicon-plus-sign"></span>
                      <span class="badge"><?php print($pos) ?></span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="btn btn-danger col-xs-12" id="vote-down" data-voted="false">
                      <span class="glyphicon glyphicon-minus-sign"></span>
                      <span class="badge"><?php print($neg) ?></span>
                    </div>
                  </div>
                <?php endif; ?>
              </div>
              <?php if ($videoInfo["userid"] != $db->getUserID($user)): ?>
                <div class="row">
                  <div class="col-md-8 col-md-push-2">
                      <?php if ($db->isFollowing($user, $videoInfo["userid"]) == 'false'): ?>
                        <button type="button" name="follow" id="follow" class="btn btn-info" data-following="false" data-uid=<?php print('"'.$videoInfo["userid"].'"'); ?>>Seguir!</button>
                      <?php else: ?>
                        <button type="button" name="follow" id="follow" class="btn btn-warning" data-following="true" data-uid=<?php print('"'.$videoInfo["userid"].'"'); ?>>Dejar de seguir!</button>
                      <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
            <div class="col-md-8">
              <div class="row">
                <textarea name="comment" id="comment" rows="3" class="form-control"></textarea>
              </div>
              <div class="row">
                <button type="button" name="send-comment" id="send-comment" class="btn btn-info col-md-4 col-md-push-4">Enviar comentario
                </button>
                <div class="col-md-2 btn btn-success col-md-push-6" id="length-btn">
                  <span class="badge" id="length">0</span>
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
