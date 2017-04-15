$(function() {
  /**
  * Validate if the video upload form is correct
  */
  $("#upload").submit(function(e) {
    var isValid = true;
    if ($("#video-name").val().length < 10) {
      isValid = false;
      alert("El contenido del campo nombre es demasiado corto.\
      Debe contener al menos 10 caracteres.");
    }
    if ($("#video-desc").val().length < 10) {
      isValid = false;
      alert("El contenido del campo descripcion es demasiado corto.\
      Debe contener al menos 10 caracteres.");
    }
    if ($("#video-tags")[0].selectedIndex === -1) {
      isValid = false;
      alert("Debe seleccionar al menos un tag para el video");
    }
    if ($("#video-input")[0].files.length === 0) {
      isValid = false;
      alert("Debe seleccionar un fichero para subir");
    }
    if (window.File && window.FileReader && window.FileList && window.Blob) {
      var fileSize = $("#video-input")[0].files[0].size;
      if (fileSize > 524288000) {
        alert("El fichero es demasiado grande. No puede ser mayor de 500MB");
        isValid =  false;
      }
    } else {
      alert("Tu navegador no soporta nuestro sistema de subida de ficheros\
      Por favor, descarga la ultima version de Google Chrome o Firefox");
      isValid = false;
    }
    if (isValid) {
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
  /**
  * Update the input of the file showing the file name
  */
  $("#video-input").change(function(e) {
    $("#file-name").val(this.files[0].name);
  });
  /**
  * After the upload will clean the GUI and give feedback
  */
  function uploadSuccess(data) {
    $(".container").html("");
    alert("Se ha subido el video correctamente, ahora seras redireccionado al inicio");
    setTimeout(function() {window.location.href="home.php", 5000});
  }
  /**
  * Before the upload will prepare a modal window and show a progress bar
  */
  function prepareUpload() {
    $("#main").children("form").hide();
    $content = '<h1>Se esta subiendo el video</h1>\
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
    alert("Ha ocurrido un error subiendo el video, por favor recarga la pagina\
    y vuelve a intentarlo en unos minutos.");
  }

});
