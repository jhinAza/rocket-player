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
  * Validate if the video upload form is correct
  */
  $("#upload").submit(function(e) {
    var isValid = true;
    if ($("#video-name").val().length < 10) {
      isValid = false;
      // alert("El contenido del campo nombre es demasiado corto.\
      // Debe contener al menos 10 caracteres.");
    }
    if ($("#video-desc").val().length < 10) {
      isValid = false;
      // alert("El contenido del campo descripcion es demasiado corto.\
      // Debe contener al menos 10 caracteres.");
    }
    if ($("#video-tags")[0].selectedIndex === -1) {
      isValid = false;
      // alert("Debe seleccionar al menos un tag para el video");
    }
    if ($("#video-input")[0].files.length === 0) {
      isValid = false;
      alert("Debe seleccionar un fichero para subir");
    }
    if (!isValid) {
      return false;
    }
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
});
