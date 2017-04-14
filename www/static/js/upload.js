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
        console.log(fileSize);
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
        uploadProgress: updateProgressBar,
        resetForm: false,
        method: "post"
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
  /**
  * After the upload will clean the GUI and give feedback
  */
  function uploadSuccess(data) {
    alert("Subido");
    console.log(data);
  }
  /**
  * Before the upload will prepare a modal window and show a progress bar
  */
  function prepareUpload() {
    alert("Empezando");
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
    console.log(position, total, percentComplete);
  }
});
