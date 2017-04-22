<?php
  require_once("inc/databaseController.php");
  $db = new databaseController();
  function createComment($comment) {
    $db = new databaseController();
    $user = $db->getUserName($comment["userID"]);
    ?>
    <div class="comment">
          <div class="row">
            <div class="col-md-3">
              <img src="user.png"  class="img-prueba">
            </div>
            <div class="col-md-9">
              <p>
                <h4><?php print($user); ?></h4>
                <?php print($comment["Comments"]); ?>
              </p>
            </div>
          </div>
        </div>
    <?php
  }
  $comments = $db->getComments($_COOKIE["video"]);
  foreach ($comments as $comment) {
    createComment($comment);
  }
?>
