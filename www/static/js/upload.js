$(function() {
  /**
  * Validate if the video upload form is correct
  */
  $("#upload").submit(function(e) {
    var isValid = true;
    if ($("#video-name").val().length < 10) {
      isValid = false;
      bsAlert("El contenido del campo nombre es demasiado corto.\
      Debe contener al menos 10 caracteres.");
    }
    if ($("#video-desc").val().length < 10) {
      isValid = false;
      bsAlert("El contenido del campo descripcion es demasiado corto.\
      Debe contener al menos 10 caracteres.");
    }
    if ($("#video-tags")[0].selectedIndex === -1) {
      isValid = false;
      bsAlert("Debe seleccionar al menos un tag para el video");
    }
    if ($("#video-input")[0].files.length === 0) {
      isValid = false;
      bsAlert("Debe seleccionar un fichero para subir");
    }
    if ($("#img-input")[0].files.length === 0) {
      console.log("Hola!");
      isValid = false;
      bsAlert("Debe seleccionar una portada para el video");
    } else if($("#img-input")[0].files[0].type.indexOf("image") == -1) {
      isValid = false;
      bsAlert("El formato de la imagen no es soportado");
    } else {
      if (window.img.width > 0) {
        if (window.img.width > 500 || window.img.height > 500) {
          bsAlert("La imagen debe ser como maximo de 500px de ancho y alto");
          isValid = false;
        }
      } else {
        bsAlert("Por favor espere antes de volver a pulsar el boton de submit. Aun estamos preparando su formulario");
        isValid = false;
      }
    }
    if (isValid) {
      if (window.File && window.FileReader && window.FileList && window.Blob) {
        var fileSize = $("#video-input")[0].files[0].size;
        if (fileSize > 524288000) {
          bsAlert("El fichero es demasiado grande. No puede ser mayor de 500MB");
          isValid =  false;
        }
      } else {
        bsAlert("Tu navegador no soporta nuestro sistema de subida de ficheros\
        Por favor, descarga la ultima version de Google Chrome o Firefox");
        isValid = false;
      }
      options = {
        target: "upload.php",
        beforeSubmit: prepareUpload,
        success: uploadSuccess,
        error: uploadError,
        uploadProgress: updateProgressBar,
        resetForm: false,
        method: "post",
        dataType: "html"
      };
      $(this).ajaxSubmit(options);
      console.log("YA?");
    }
    return false;
    // Always return false to avoid needless redirects
  });

  $("#upload-res").submit(function(e) {
    var isValid = true;
    if ($("#resource-lang")[0].selectedIndex === -1 &&
        $("#resource-type").val() != "signal-language") {
      isValid = false;
      bsAlert("Debe seleccionar un lenguaje para el recurso a menos que el recurso\
             sea de tipo lenguaje de señas");
    }
    if ($("#resource-type")[0].selectedIndex === -1) {
      isValid = false;
      bsAlert("Debe señeccionar un tipo de recurso");
    }
    if ($("#res-input")[0].files.length === 0) {
      isValid = false;
      bsAlert("Debe seleccionar un fichero para subir");
    } else {
      switch ($("#resource-type").val()) {
        case "subtitles":
          if (!$("#res-input")[0].files[0].name.endsWith(".srt")) {
            bsAlert("Ha seleccionado subtitulos pero no un formato de subtitulos adecuado.\
            Por favor seleccione un fichero de tipo .srt");
            isValid = false;
          }
          break;
        case "transcription":
          if (!$("#res-input")[0].files[0].name.endsWith(".srt")) {
            bsAlert("Ha seleccionado trancripcion pero no un formato de transcripcion adecuado.\
            Por favor seleccione un fichero de tipo .srt");
            isValid = false;
          }
          break;
        case "signal-language":
          if (!$("#res-input")[0].files[0].name.endsWith(".mp4")) {
            bsAlert("Ha seleccionado lenguaje de señas pero no un formato de lenguaje de señas adecuado.\
            Por favor seleccione un fichero de tipo .mp4");
            isValid = false;
          }
          break;
      }
    }
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      var fileSize = $("#res-input")[0].files[0].size;
      if (fileSize > 524288000) {
        bsAlert("El fichero es demasiado grande. No puede ser mayor de 500MB");
        isValid =  false;
      }
    } else {
      bsAlert("Tu navegador no soporta nuestro sistema de subida de ficheros\
      Por favor, descarga la ultima version de Google Chrome o Firefox");
      isValid = false;
    }
    if (isValid) {
      options = {
        target: "resources.php",
        beforeSubmit: prepareUpload,
        success: uploadSuccess,
        error: uploadError,
        uploadProgress: updateProgressBar,
        resetForm: false,
        method: "post",
        data: {"video":url("?video")},
        dataType: "html"
      };
      $(this).ajaxSubmit(options);
    }
    return false;
    // Always return false to avoid needless redirects
  });
  /**
  * Update the input of the file showing the file name
  */
  $("#video-input").change(function(e) {
    $("#file-name").val(this.files[0].name);
  });
  $("#res-input").change(function(e) {
    $("#file-name").val(this.files[0].name);
  });
  $("#img-input").change(function(e) {
    var _URL = window.URL || window.webkitURL;
    $("#img-name").val(this.files[0].name);
    var file = $("#img-input")[0].files[0]
    if (file) {
      window.img = new Image();
      window.loaded = false;
      img.onload = function () {
        window.loaded = true;
      };
      img.src = _URL.createObjectURL(file);
      return false;
    }
  });
  /**
  * After the upload will clean the GUI and give feedback
  */
  function uploadSuccess(data) {
    console.log(data);
    $(".container").html("");
    bsAlert("Se ha subido el fichero correctamente, ahora seras redireccionado al inicio");
    setTimeout(function() {window.location.href="home.php", 5000});

  }
  /**
  * Before the upload will prepare a modal window and show a progress bar
  */
  function prepareUpload() {
    $("#main").children("form").hide();
    $content = '<h1>Se esta subiendo el fichero</h1>\
    <div class="progress">\
      <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="min-width: 10%;">\
        0%\
      </div>\
    </div>';
    $("#main").append($content);
  }
  /**
  * Will update the progress bar
  * @param {Event} e
  * @param {int} position
  * @param {int} total
  * @param {int} percentComplete
  */
  function updateProgressBar(e, position, total, percentComplete) {
    //Actualizar la barra
    $(".progress-bar").html(percentComplete+"%").css("width", percentComplete+"%");

  }

  function uploadError(xhr, ajaxOptions, thrownError) {
    bsAlert("Ha ocurrido un error subiendo el archivo, por favor recarga la pagina\
    y vuelve a intentarlo en unos minutos.");
  }

});
