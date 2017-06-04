<?php
  require_once("inc/databaseController.php");
  $db = new databaseController();
  function createComment($comment) {
    $db = new databaseController();
    $user = $db->getUserName($comment["userid"]);
    ?>
      <div class="comment">
        <div class="row">
          <div class="col-md-3">
            <img src="/static/img/user.png"  class="img-prueba">
          </div>
          <div class="col-md-9">
            <div class="row">
              <div class="col-md-6">
                <h4><?php print($user); ?></h4>
              </div>
              <div class="col-md-6">
                <div class="btn-group btn-group-sm comments-btn"  data-comment-id=<?php print($comment["id"]); ?>>
                  <div class="btn btn-info comment-response">
                    <span>Responder</span>
                  </div>
                  <div class="btn btn-success vote-comment" data-vote="1" data-voted="false">
                    <span class="glyphicon glyphicon-plus"></span>
                  </div>
                  <div class="btn btn-danger vote-comment"  data-vote="-1" data-voted="false">
                    <span class="glyphicon glyphicon-minus"></span>
                  </div>
                </div>
              </div>
            </div>
            <p>
              <?php print($comment["comments"]); ?>
            </p>
          </div>
        </div>
        <?php
          $childComments = $db->getChildComments($comment["id"]);
          if ($childComments) {
            foreach ($childComments as $childComment) {
              createComment($childComment);
            }
          }
        ?>
      </div>
    <?php
  }
  $comments = $db->getComments($_GET["video"]);
  if ($comments) {
    foreach ($comments as $comment) {
      createComment($comment);
    }
  }
?>
