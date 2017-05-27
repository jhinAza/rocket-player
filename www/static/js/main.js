$(function() {
  // Events
  /**
  * Validate if the user login form is correct
  */
  $("#loginUser").submit(function(e) {
    var isValid = true;
    if (user.value.length == 0) {
      isValid = false;
      alert("El campo de usuario no puede estar vacio");
    }
    if (pass.value.length == 0) {
      isValid = false;
      alert("El campo de la contraseña no puede estar vacio");
    }
    return isValid;
  });

  /**
  * Makes a AJAX call against the server to retrieve the modal window
  */
  $("#settings").click(function(e) {
    $.get({
      "url": "/settings.php",
      "success": getSettingsSuccess,
      "dataType": "html"
    });
  });
  /**
  * Checks every item in the truncate view
  */
  $("#check").click(function(e) {
    $.each($(".check-list"), function() {
      $(this).children("input")[0].checked = true;
    });
  });
  /**
  * Uncheck every item in the truncate list
  */
  $("#uncheck").click(function(e) {
    $.each($(".check-list"), function() {
      $(this).children("input")[0].checked = false;
    });
  });
  /**
  * Makes an AJAX call against the server to send the tables that must be truncated
  */
  $("#send").click(function(e) {
    var list = [];
    $.each($(".check-list"), function() {
      if ($(this).children("input")[0].checked) {
        list.push($(this).children("input")[0].value);
      }
    });
    if (list.length > 0) {
      $.post({
        "url": "/truncate.php",
        "data": {"tables": list}
      });
    }
  });
  /**
  * Makes an AJAX call agains the server to store a comment in a video
  */
  $("#send-comment").click(function (e) {
    comment = $("#comment").val();
    var isValid = true
    if (comment.length > 300) {
      isValid = false;
      alert("El comentario es demasiado largo");
    }
    if (isValid) {
      $.post({
        "url": "/sendComment.php",
        "success": sendCommentSuccess,
        "data": $("#comment").serialize()
      });
    }
  });

  $(".comment-response").click(function(e) {
    console.log($(this).data("comment-id"));
    var commentID = "#" + $(this).data("comment-id") + " ";
    $("#comment").val(commentID).focus();
  })

  $("#comment").on("keyup", function(e) {
    var length = this.value.length;
    $("#length").html(length);
    if (length >= 300) {
      this.value = this.value.substring(0,300);
      $("#length-btn").removeClass("btn-success").removeClass("btn-warning").addClass("btn-danger");
    } else if (length > 200) {
      $("#length-btn").removeClass("btn-success").removeClass("btn-danger").addClass("btn-warning");
    } else {
      $("#length-btn").removeClass("btn-warning").removeClass("btn-danger").addClass("btn-success");
    }
  })

  $("#follow").on("click", function(e) {
    if ($(this).data("following") == true) {
      var type = "unfollow";
      var success = unfollowSuccess;
    } else {
      var type = "follow";
      var success = followSuccess;
    }
    $.post({
      "url": "/requests.php",
      "success": success,
      "data": {
        "type": type,
        "followed": url("?uid") || $(this).data("uid")
      }
    })
  });



  $("#send-search").click(function(e) {
    var query = $("#search").val();
    console.log(query);
    if (query.length > 3) {
      var url = "/search.php?query=" + query;
      window.location = url;
    } else {
      alert("El contenido del campo de busqueda debe ser mayor de 3 caracteres")
    }
  });

  $(".user-img").click(function(e) {
    $("#img-file").trigger("click");
  });

  $("#img-file").on("change", function(e) {
    var options = {
      "data":   {"type":"updateUserImage"},
      "url": "/requests.php",
      "type": "post",
      "resetForm": false,
      "success": genericSuccess,
      "beforeSubmit": test
    }
    $("#img-form").ajaxSubmit(options);
  });
  // Functions
  /**
  * Appends the settings modal window to the body and sets the events
  *
  * @param {HTMLElement} data
  */
  function getSettingsSuccess(data) {
    $("body").append(data);
    $("#myModal").modal("toggle");
    $("#myModal").modal("show");
    /**
    * detach the modal window when the user closes it.
    */
    $("#myModal").on("hidden.bs.modal", function(e) {
      $(this).detach();
    });
    /**
    * Makes a AJAX call against the server to save the new settings of the user.
    */
    $("#save").click(function(e) {
      $.post({
        "url": "/settings.php",
        "data": $("#settingsModal").serialize(),
        "success": location.reload(true)
      });
    });
  }

  function sendCommentSuccess(data) {
    $("#comment").val("");
    $("#length").html("");
    console.log(data);
  }

  function followSuccess(data) {
    $("#follow").removeClass("btn-info").addClass("btn-warning").html("Dejar de seguir!").data("following", "true");
    console.log(data);
  }
  function unfollowSuccess(data) {
    console.log("Unfollowed!!");
    $("#follow").removeClass("btn-warning").addClass("btn-info").html("Seguir!").data("following", "false");
    console.log(data);
  }

  function genericSuccess(data) {
    console.log(data);
  }

  function test() {
    console.log("Hola!!");
  }
});
