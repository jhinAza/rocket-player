$(function() {
  //
  switch (location.pathname) {
    case "/login.php":
      $(loginUser).submit(function(e) {
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
      break;
    case "/home.php":
      $(settings).click(function(e) {
        var temp = $.get({
          "url": "/settings.php",
          "success": success,
          "dataType": "html"
        });
      });

      function success(data) {
        $("body").append(data);
        $(myModal).modal("toggle");
        $(myModal).modal("show");
        $(myModal).on("hidden.bs.modal", function(e) {
          $(this).detach();
        });
        $(save).click(function(e) {
          $.post({
            "url": "/settings.php",
            "data": $(settingsModal).serialize()
          });
        });
      }
      break;
    default:
      console.log(location.pathname);
  }
});
